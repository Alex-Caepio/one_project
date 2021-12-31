<?php

namespace App\Http\Controllers;

use App\Actions\Promo\CalculatePromoPrice;
use App\Actions\Schedule\PurchaseInstallment;
use App\Actions\Stripe\GetViablePaymentMethod;
use App\Actions\Stripe\TransferFundsWithCommissions;
use App\DTO\Schedule\PaymentIntentDto;
use App\Events\AppointmentBooked;
use App\Filters\PurchaseFilters;
use App\Http\Requests\PromotionCode\ValidatePromocodeRequest;
use App\Http\Requests\Request;
use App\Http\Requests\Schedule\PurchaseFinalizeRequest;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Models\Booking;
use App\Models\Price;
use App\Models\PromotionCode;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\ScheduleFreeze;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingSnapshotService;
use App\Transformers\PromocodeCalculateTransformer;
use App\Transformers\PurchaseTransformer;
use Carbon\Carbon;
use Illuminate\Database\Connection;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class PurchaseController extends Controller
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $query = Purchase::query();

        $purchaseFilter = new PurchaseFilters();
        $purchaseFilter->apply($query, $request);

        $includes = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        return response(
            fractal($paginator->getCollection(), new PurchaseTransformer())
                ->parseIncludes($request->getIncludes())
        )
            ->withPaginationHeaders($paginator);
    }

    public function purchase(
        PurchaseScheduleRequest $request,
        Schedule $schedule,
        StripeClient $stripe
    ): array {
        $price = $schedule->prices()->where('id', $request->get('price_id'))->first();
        $cost = $price->cost * $request->amount;
        $practitioner = $schedule->service->user;

        $promo = null;
        $discount = $discountPerAppointment = 0;

        if ($request->has('promo_code')) {
            $promo = PromotionCode::where('name', $request->get('promo_code'))->with('promotion')->first();
            if ($promo instanceof PromotionCode) {
                $newCost = run_action(CalculatePromoPrice::class, $promo, $request->amount, $price->cost);
                if ($newCost != $cost) {
                    $discount = $cost - $newCost;
                }
                $cost = $newCost;
            }
        }

        $schedule->load('service');
        $isInstallment = $schedule->deposit_accepted
            && isset($request->installments)
            && (int)$request->installments > 0;

        $this->connection->beginTransaction();

        $paymentIntentData = null;

        try {
            $purchase = new Purchase();
            $purchase->schedule_id = $schedule->id;
            $purchase->service_id = $schedule->service->id;
            $purchase->price_id = $price->id;
            $purchase->user_id = Auth::id();
            $purchase->promocode_id = $promo instanceof PromotionCode ? $promo->id : null;
            $purchase->price_original = $price->cost;
            $purchase->price = $cost;
            $purchase->is_deposit = $isInstallment;
            $purchase->amount = $request->amount;
            $purchase->discount = $discount;
            $purchase->discount_applied = $promo instanceof PromotionCode ? $promo->promotion->applied_to : null;
            $purchase->save();

            if ($schedule->service->service_type_id === Service::TYPE_APPOINTMENT) {
                $availabilities = $request->get('availabilities');

                if ($discount > 0) {
                    $discountPerAppointment = round($discount / count($availabilities));
                }
                $costPerAppointment = round($cost / count($availabilities));

                foreach ($availabilities as $availability) {
                    $booking = new Booking();
                    $booking->user_id = $request->user()->id;
                    $booking->practitioner_id = $schedule->service->user_id;
                    $booking->price_id = $request->get('price_id');
                    $booking->schedule_id = $schedule->id;
                    $booking->datetime_from = $availability['datetime_from'];
                    $datetimeTo = (new Carbon($booking->datetime_from))->addMinutes($price->duration);
                    $booking->datetime_to = $datetimeTo->format('Y-m-d H:i:s');
                    $booking->cost = $costPerAppointment;
                    $booking->purchase_id = $purchase->id;
                    $booking->amount = $request->amount;
                    $booking->discount = $discountPerAppointment;
                    $booking->is_installment = $isInstallment;
                    $booking->is_fully_paid = !$isInstallment;
                    $booking->save();
                    event(new AppointmentBooked($booking));
                }
            } else {
                $booking = new Booking();
                $booking->user_id = $request->user()->id;
                $booking->practitioner_id = $schedule->service->user_id;

                $booking->datetime_from = $schedule->start_date ?: Carbon::now();
                $booking->datetime_to = $schedule->end_date ?: Carbon::now();

                $booking->price_id = $request->get('price_id');
                $booking->schedule_id = $schedule->id;
                $booking->cost = $cost;
                $booking->purchase_id = $purchase->id;
                $booking->amount = $request->amount;
                $booking->is_installment = $isInstallment;
                $booking->is_fully_paid = !$isInstallment;
                $booking->discount = $discount;
                $booking->save();
            }

            if ($cost && !$price->is_free) {
                $paymentIntentData = $isInstallment
                    ? $this->payInInstallments($request, $schedule, $price, $practitioner, $cost, $purchase, $booking)
                    : $this->payInstant($request, $schedule, $price, $stripe, $purchase, $practitioner);
            }

            if ($schedule->service->service_type_id === Service::TYPE_BESPOKE) {
                BookingSnapshotService::create($booking);
            }

            ScheduleFreeze::where('schedule_id', $schedule->id)->where('user_id', $request->user()->id)->delete();

            //PromotionCode status
            if ($promo instanceof PromotionCode) {
                $cntPurchases = Purchase::where('promocode_id', $promo->id)->count();
                if (!$promo->uses_per_code || (int)$promo->uses_per_code === $cntPurchases) {
                    $promo->status = PromotionCode::STATUS_COMPLETE;
                    $promo->save();
                }
            }
        } catch (Exception $e) {
            Log::channel('stripe_purchase_schedule_error')
                ->info("Common Purchase Error", [
                    'user_id' => $request->user()->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'practitioner_id' => $schedule->service->user_id,
                    'schedule_id' => $schedule->id,
                    'payment_intent' => $paymentIntent->id ?? null,
                    'message' => $e->getMessage(),
                ]);
            $this->connection->rollBack();
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }

        $this->connection->commit();

        $purchaseData = fractal($purchase, new PurchaseTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();

        return array_merge(
            $purchaseData,
            $paymentIntentData ? $paymentIntentData->toArray() : ['status' => 'succeeded']
        );
    }

    public function validatePromocode(ValidatePromocodeRequest $request, Schedule $schedule)
    {
        $promo = PromotionCode::where('name', $request->get('promo_code'))->with('promotion')->first();
        $price = $schedule->prices()->find($request->get('price_id'));
        if (!$price) {
            abort(500, 'Price not found');
        }

        return fractal(
            (object)['promocode' => $promo, 'amount' => $request->amount, 'price' => $price->cost],
            new PromocodeCalculateTransformer()
        );
    }

    protected function payInInstallments(
        PurchaseScheduleRequest $request,
        Schedule $schedule,
        Price $price,
        User $practitioner,
        $cost,
        Purchase $purchase,
        Booking $booking
    ): PaymentIntentDto {
        $payment_method_id = run_action(GetViablePaymentMethod::class, $practitioner, $request->payment_method_id);
        $depositCost = $schedule->deposit_amount * 100 * $request->amount;

        try {
            $responseData = run_action(
                PurchaseInstallment::class,
                $schedule,
                $request,
                $payment_method_id,
                $cost,
                $purchase,
                $booking
            );
        } catch (ApiErrorException $e) {
            Log::channel('stripe_installment_fail')
                ->info('The client could not purchase installment', [
                    'user_id' => $request->user()->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'schedule_id' => $schedule->id,
                    'payment_method' => $payment_method_id,
                    'amount' => $request->amount,
                    'message' => $e->getMessage(),
                ]);

            throw new Exception('Cannot handle installments payment');
        }

        try {
            run_action(
                TransferFundsWithCommissions::class,
                $depositCost,
                $practitioner,
                $schedule,
                $request->user(),
                $purchase
            );

            Log::channel('stripe_transfer_success')
                ->info("The practitioner received transfer", [
                    'user_id' => $request->user()->id,
                    'practitioner' => $practitioner->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'schedule_id' => $schedule->id,
                    'payment_method' => $payment_method_id,
                    'amount' => $request->amount,
                ]);
        } catch (ApiErrorException $e) {
            Log::channel('stripe_transfer_fail')
                ->info("The practitioner could not received transfer", [
                    'user_id' => $request->user()->id,
                    'practitioner' => $practitioner->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'schedule_id' => $schedule->id,
                    'payment_method' => $payment_method_id,
                    'amount' => $request->amount,
                    'message' => $e->getMessage(),
                ]);
        }

        return $responseData;
    }

    protected function payInstant(
        $request,
        Schedule $schedule,
        Price $price,
        $stripe,
        Purchase $purchase,
        $practitioner
    ): PaymentIntentDto {
        $payment_method_id = run_action(GetViablePaymentMethod::class, $practitioner, $request->payment_method_id);
        $paymentIntent = null;
        try {
            $client = $request->user();
            $reference = implode(', ', $purchase->bookings->pluck('reference')->toArray());
            $paymentIntent = $stripe->paymentIntents->create(
                [
                    'amount' => $purchase->price * 100,
                    'currency' => config('app.platform_currency'),
                    'payment_method_types' => ['card'],
                    'customer' => Auth::user()->stripe_customer_id,
                    'payment_method' => $payment_method_id,
                    'metadata' => [
                        'Practitioner business email' => $practitioner->business_email,
                        'Practitioner business name' => $practitioner->business_name,
                        'Practitioner stripe id' => $practitioner->stripe_customer_id,
                        'Practitioner connected account id' => $practitioner->stripe_account_id,
                        'Client first name' => $client->first_name,
                        'Client last name' => $client->last_name,
                        'Client stripe id' => $client->stripe_customer_id,
                        'Booking reference' => $reference
                    ]
                ]
            );

            /** @var PaymentIntent $paymentIntent */
            $paymentIntent =
                $stripe->paymentIntents->confirm($paymentIntent->id, ['payment_method' => $payment_method_id]);
            $purchase->stripe_id = $paymentIntent->id;
            $purchase->save();
        } catch (ApiErrorException $e) {
            Log::channel('stripe_purchase_schedule_error')
                ->info("Client could not purchase schedule", [
                    'user_id' => $request->user()->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'schedule_id' => $schedule->id,
                    'payment_intent' => $paymentIntent->id ?? null,
                    'payment_method' => $payment_method_id,
                    'amount' => $purchase->amount,
                    'price' => $purchase->price_original,
                    'total' => $purchase->price,
                    'discount' => $purchase->discount,
                    'discount_applied' => $purchase->discount_applied,
                    'message' => $e->getMessage(),
                ]);

            throw new Exception('Cannot handle instant payment');
        }

        Log::channel('stripe_purchase_schedule_success')
            ->info("Client purchased schedule", [
                'user_id' => $request->user()->id,
                'price_id' => $price->id,
                'service_id' => $schedule->service->id,
                'schedule_id' => $schedule->id,
                'payment_intent' => $paymentIntent->id,
                'payment_method' => $payment_method_id,
                'amount' => $purchase->amount,
                'price' => $purchase->price_original,
                'total' => $purchase->price,
                'discount' => $purchase->discount,
                'discount_applied' => $purchase->discount_applied,
            ]);

        try {
            run_action(
                TransferFundsWithCommissions::class,
                $purchase->price,
                $practitioner,
                $schedule,
                $client,
                $purchase
            );

            Log::channel('stripe_transfer_success')
                ->info("The practitioner received transfer", [
                    'user_id' => $request->user()->id,
                    'practitioner' => $practitioner->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'schedule_id' => $schedule->id,
                    'payment_intent' => $paymentIntent->id ?? null,
                    'payment_method' => $payment_method_id,
                    'amount' => $purchase->amount,
                    'price' => $purchase->price_original,
                    'total' => $purchase->price,
                    'discount' => $purchase->discount,
                    'discount_applied' => $purchase->discount_applied,
                ]);
        } catch (ApiErrorException $e) {
            Log::channel('stripe_transfer_fail')
                ->info("The practitioner could not received transfer", [
                    'user_id' => $request->user()->id,
                    'practitioner' => $practitioner->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'schedule_id' => $schedule->id,
                    'payment_intent' => $paymentIntent->id ?? null,
                    'payment_method' => $payment_method_id,
                    'amount' => $purchase->amount,
                    'price' => $purchase->price_original,
                    'total' => $purchase->price,
                    'discount' => $purchase->discount,
                    'discount_applied' => $purchase->discount_applied,
                    'message' => $e->getMessage(),
                ]);
        }

        return new PaymentIntentDto(
            $paymentIntent->status,
            $paymentIntent->client_secret,
            $paymentIntent->confirmation_method,
            $paymentIntent->next_action
        );
    }

    public function finalize(PurchaseFinalizeRequest $request, Purchase $purchase, StripeClient $stripe): array
    {
        $paymentIntentId = $request->payment_intent_id;

        $logData = [
            'user_id' => $request->user()->id,
            'price_id' => $purchase->price_id,
            'service_id' => $purchase->service_id,
            'schedule_id' => $purchase->schedule_id,
            'payment_intent' => $paymentIntentId,
            'amount' => $purchase->amount,
            'price' => $purchase->price_original,
            'total' => $purchase->price,
            'discount' => $purchase->discount,
            'discount_applied' => $purchase->discount_applied,
        ];

        try {
            $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);
            $logData['payment_intent_initial_status'] = $paymentIntent->status;

            $paymentIntent = $paymentIntent->confirm();
            $logData['payment_intent_resulting_status'] = $paymentIntent->status;

            Log::channel('stripe_purchase_finalize_success')
                ->info("Client purchased finalized", $logData);
        } catch (ApiErrorException $e) {
            Log::channel('stripe_purchase_finalize_failure')->info(
                "Client purchase finalize failed",
                array_merge(['message' => $e->getMessage()], $logData)
            );

            throw new Exception((string)$e, $e->getCode(), $e);
        }

        $paymentIntentData = new PaymentIntentDto(
            $paymentIntent->status,
            $paymentIntent->client_secret,
            $paymentIntent->confirmation_method,
            $paymentIntent->next_action
        );

        $purchaseData = fractal($purchase, new PurchaseTransformer())
            ->parseIncludes($request->getIncludes())
            ->toArray();

        return array_merge($purchaseData, $paymentIntentData->toArray());
    }

}
