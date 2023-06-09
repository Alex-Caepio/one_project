<?php

namespace App\Http\Controllers;

use App\Actions\Promo\CalculatePromoPrice;
use App\Actions\Schedule\PurchaseInstallment;
use App\Actions\Stripe\GetViablePaymentMethod;
use App\Actions\Stripe\TransferFundsWithCommissions;
use App\DTO\Schedule\PaymentIntentDto;
use App\Events\AppointmentBooked;
use App\Events\ServicePurchased;
use App\Filters\PurchaseFilters;
use App\Helpers\BookingHelper;
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
use App\Transformers\BookingShowTransformer;
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

    public function show(Request $request, Booking $booking) {
        return fractal($booking, new BookingShowTransformer())
            ->parseIncludes($request->getIncludes())->respond();
    }

    public function purchase(
        PurchaseScheduleRequest $request,
        Schedule $schedule,
        StripeClient $stripe
    ): array {
        /** @var Price $price */
        $price = $schedule->prices()->where('id', $request->get('price_id'))->first();
        $cost = $price->cost * $request->amount;
        $practitioner = $schedule->service->user;

        $promo = null;
        $discount = $discountPerAppointment = 0;

        if ($request->filled('promo_code')) {
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
            $purchase->comment = $request->filled('comment') ? $request->comment : null;
            $purchase->save();

            if ($schedule->service->service_type_id === Service::TYPE_APPOINTMENT) {
                /** @var string[] $availabilities */
                $availabilities = $request->get('availabilities');

                if ($discount > 0) {
                    $discountPerAppointment = round($discount / count($availabilities), 2, PHP_ROUND_HALF_DOWN);
                }
                $costPerAppointment = round($cost / count($availabilities), 2, PHP_ROUND_HALF_DOWN);

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
                    $booking->amount = 1;
                    $booking->discount = $discountPerAppointment;
                    $booking->is_installment = $isInstallment;
                    $booking->is_fully_paid = !$isInstallment;
                    $booking->reference = BookingHelper::generateReference();
                    $booking->refund_terms = $schedule->refund_terms;
                    $booking->save();
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
                $booking->reference = BookingHelper::generateReference();
                $booking->refund_terms = $schedule->refund_terms;
                $booking->save();
            }

            if ($cost && !$price->is_free) {
                $paymentIntentData = $isInstallment
                    ? $this->payInInstallments($request, $schedule, $price, $practitioner, $cost, $purchase, $booking)
                    : $this->payInstant($request, $schedule, $price, $stripe, $purchase, $practitioner, $booking);

                // if 3ds required
                // requires_source_action https://stripe.com/docs/payments/payment-intents/migration?lang=curl
                if (in_array($paymentIntentData->getStatus(), [PaymentIntent::STATUS_REQUIRES_ACTION, 'requires_source_action'])) {
                    $this->connection->rollBack();
                    return $paymentIntentData->toArray();
                }
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
                ->error("Common Purchase Error", [
                    'user_id' => $request->user()->id,
                    'price_id' => $price->id,
                    'service_id' => $schedule->service->id,
                    'practitioner_id' => $schedule->service->user_id,
                    'schedule_id' => $schedule->id,
                    // 'payment_intent' => $paymentIntent->id ?? null,
                    'message' => $e->getMessage(),
                ]);
            $this->connection->rollBack();
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getMessage());
        }

        if ($schedule->service->service_type_id === Service::TYPE_APPOINTMENT) {
            event(new AppointmentBooked($booking));
        }

        if ($purchase->is_deposit) {
            $booking->installmentComplete();
        }

        if (in_array($schedule->service->service_type_id, [Service::TYPE_BESPOKE, Service::TYPE_APPOINTMENT])) {
            foreach ($purchase->bookings as $book) {
                BookingSnapshotService::create($book);
            }
        }

        $this->connection->commit();

        event(new ServicePurchased($booking));

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
        $depositCost = $schedule->deposit_amount * $request->amount;

        try {
            /** @var PaymentIntentDto $responseData */
            $responseData = run_action(
                PurchaseInstallment::class,
                $schedule,
                $request,
                $payment_method_id,
                $cost,
                $purchase,
                $booking
            );

            // Needed to config through 3ds
            if ($responseData->getChargeId() === null) {
                return $responseData;
            }

        } catch (ApiErrorException $e) {
            Log::channel('stripe_installment_fail')
                ->error('The client could not purchase installment', [
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
                $purchase,
                $responseData->getChargeId(),
                $booking
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
                ->error("The practitioner could not received transfer", [
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
        $practitioner,
        $booking
    ): PaymentIntentDto {
        $payment_method_id = run_action(GetViablePaymentMethod::class, $practitioner, $request->payment_method_id);
        $paymentIntent = null;
        try {
            $client = $request->user();

            if (!empty($booking)) {
                $reference = $booking->reference;
            } else {
                $reference = implode(', ', $purchase->bookings->pluck('reference')->toArray());
            }

            if (!empty($purchase->promocode)) {
                $applied_to = $purchase->promocode->promotion->applied_to;
            } else {
                $applied_to = '';
            }

            if ($request->input('payment_intent')) {
                $paymentIntent = $stripe->paymentIntents->retrieve($request->input('payment_intent'));
            } else {
                $paymentIntent = $stripe->paymentIntents->create(
                    [
                        'amount' => intval($purchase->price * 100),
                        'currency' => config('app.platform_currency'),
                        'payment_method_types' => ['card'],
                        'customer' => Auth::user()->stripe_customer_id,
                        'payment_method' => $payment_method_id,
                        'metadata' => [
                            'Practitioner business email' => $practitioner->business_email,
                            'Practitioner business name' => $practitioner->business_name,
                            'Practitioner stripe id' => $practitioner->stripe_customer_id,
                            'Practitioner connected account id' => $practitioner->stripe_account_id,
                            'Tom Commission' => $practitioner->getCommission() . '%',
                            'Application Fee' =>
                                round($purchase->price * $practitioner->getCommission() / 100, 2, PHP_ROUND_HALF_DOWN)
                                . '(' . config('app.platform_currency') . ')',
                            'Client first name' => $client->first_name,
                            'Client last name' => $client->last_name,
                            'Client stripe id' => $client->stripe_customer_id,
                            'Booking reference' => $reference,
                            'Promoted by' => $applied_to,
                        ],
                    ]
                );

                /** @var PaymentIntent $paymentIntent */
                $paymentIntent =
                    $stripe->paymentIntents->confirm($paymentIntent->id, ['payment_method' => $payment_method_id]);
            }

            // For 3ds need to be confirmed
            Log::channel('stripe_purchase_instant')
                ->info("Instant payment", $paymentIntent->toArray());

            if (in_array($paymentIntent->status, [PaymentIntent::STATUS_REQUIRES_ACTION, 'requires_source_action'])) {
                    return new PaymentIntentDto(
                    PaymentIntent::STATUS_REQUIRES_ACTION,
                    $paymentIntent->client_secret,
                    $paymentIntent->confirmation_method,
                    $paymentIntent->next_action,
                    null
                );
            }

            $purchase->stripe_id = $paymentIntent->id;
            $purchase->save();

            $chargeId = $paymentIntent->charges->data ? $paymentIntent->charges->data[0]['id'] : null;
            if($chargeId === null) {
                throw new Exception('Cannot handle instant payment');
            }
        } catch (ApiErrorException $e) {
            Log::channel('stripe_purchase_schedule_error')
                ->error("Client could not purchase schedule", [
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
                'charge_id' => $chargeId,
            ]);

        try {
            run_action(
                TransferFundsWithCommissions::class,
                $purchase->price,
                $practitioner,
                $schedule,
                $client,
                $purchase,
                $chargeId,
                $booking
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
                ->error("The practitioner could not received transfer", [
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
            $paymentIntent->next_action,
            $chargeId
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
            Log::channel('stripe_purchase_finalize_failure')->warning(
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
