<?php

namespace App\Http\Controllers;

use App\Actions\Promo\CalculatePromoPrice;
use App\Actions\Schedule\PurchaseInstallment;
use App\Actions\Stripe\GetViablePaymentMethod;
use App\Actions\Stripe\TransferFundsWithCommissions;
use App\Filters\PurchaseFilters;
use App\Http\Requests\PromotionCode\ValidatePromocodeRequest;
use App\Http\Requests\Request;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Models\Booking;
use App\Models\PromotionCode;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\ScheduleFreeze;
use App\Transformers\PromocodeCalculateTransformer;
use App\Transformers\PurchaseTransformer;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PurchaseController extends Controller
{

    /**
     * @param \App\Http\Requests\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): Response
    {
        $query = Purchase::query();

        $purchaseFilter = new PurchaseFilters();
        $purchaseFilter->apply($query, $request);

        $includes  = $request->getIncludes();
        $paginator = $query->with($includes)->paginate($request->getLimit());

        return response(fractal($paginator->getCollection(),
            new PurchaseTransformer())->parseIncludes($request->getIncludes()))->withPaginationHeaders($paginator);

    }

    public function purchase(PurchaseScheduleRequest $request, Schedule $schedule, StripeClient $stripe)
    {
        $price        = $schedule->prices()->find($request->get('price_id'));
        $cost         = $price->cost * $request->amount;
        $practitioner = $schedule->service->user;
        DB::beginTransaction();

        $promo = null;
        if ($request->has('promo_code')) {
            $promo = PromotionCode::where('name', $request->get('promo_code'))->with('promotion')->first();
            if ($promo instanceof PromotionCode) {
                $cost = run_action(CalculatePromoPrice::class, $promo, $request->amount, $price->cost);
            }
        }
        $schedule->load('service');
        $purchase                 = new Purchase();
        $purchase->schedule_id    = $schedule->id;
        $purchase->service_id     = $schedule->service->id;
        $purchase->price_id       = $price->id;
        $purchase->user_id        = Auth::id();
        $purchase->promocode_id   = $promo instanceof PromotionCode ? $promo->id : null;
        $purchase->price_original = $price->cost;
        $purchase->price          = $cost;
        $purchase->is_deposit     = false;
        $purchase->amount         = $request->amount;
        $purchase->save();

        if ($schedule->service->service_type_id === 'appointment') {
            $availabilities = $request->get('availabilities');
            foreach ($availabilities as $availability) {
                $booking                  = new Booking();
                $booking->user_id         = $request->user()->id;
                $booking->practitioner_id = $schedule->service->user_id;
                $booking->price_id        = $request->get('price_id');
                $booking->schedule_id     = $schedule->id;
                $booking->datetime_from   = $availability['datetime_from'];
                $datetimeTo               = (new Carbon($booking->datetime_from))->addMinutes($price->duration);
                $booking->datetime_to     = $datetimeTo->format('Y-m-d H:i:s');
                $booking->cost            = $cost;
                $booking->purchase_id     = $purchase->id;
                $booking->amount          = $request->amount;
                $booking->save();
            }
        } else {
            $booking                  = new Booking();
            $booking->user_id         = $request->user()->id;
            $booking->practitioner_id = $schedule->service->user_id;

            $booking->datetime_from = $schedule->start_date ?: Carbon::now();
            $booking->datetime_to   = $schedule->end_date ?: Carbon::now();

            $booking->price_id    = $request->get('price_id');
            $booking->schedule_id = $schedule->id;
            $booking->cost        = $cost;
            $booking->purchase_id = $purchase->id;
            $booking->amount      = $request->amount;
            $booking->save();
        }

        if ($cost && !$price->is_free) {
            if ($request->instalments && $schedule->deposit_accepted) {
                $this->payInInstallments($request, $schedule, $price, $practitioner, $cost, $purchase);
            } else {
                $this->payInstant($request, $schedule, $price, $cost, $stripe, $purchase, $practitioner);
            }
        }


        ScheduleFreeze::where('schedule_id', $schedule->id)->where('user_id', $request->user()->id)->delete();

        return fractal($purchase, new PurchaseTransformer())->parseIncludes($request->getIncludes())->toArray();

    }

    public function validatePromocode(ValidatePromocodeRequest $request, Schedule $schedule)
    {
        $promo = PromotionCode::where('name', $request->get('promo_code'))->with('promotion')->first();
        $price = $schedule->prices()->find($request->get('price_id'));
        if (!$price) {
            abort(500, 'Price not found');
        }
        return fractal((object)['promocode' => $promo, 'amount' => $request->amount, 'price' => $price->cost],
            new PromocodeCalculateTransformer());
    }

    protected function payInInstallments($request, $schedule, $price, $practitioner, $cost, $purchase): void
    {
        $payment_method_id = run_action(GetViablePaymentMethod::class, $practitioner, $request->payment_method_id);
        $depositCost = $schedule->deposit_amount * 100 * $request->amount;

        try {
            run_action(PurchaseInstallment::class, $price, $request, $payment_method_id, $request->instalments, $cost);
            DB::commit();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::channel('stripe_installment_fail')
                ->info('The client could not purchase installment', [
                    'user_id'        => $request->user()->id,
                    'price_id'       => $price->id,
                    'service_id'     => $schedule->service->id,
                    'schedule_id'    => $schedule->id,
                    'payment_method' => $payment_method_id,
                    'amount'         => $request->amount,
                    'message'        => $e->getMessage(),
                ]);
            abort(500);
        }

        try {
            run_action(TransferFundsWithCommissions::class, $depositCost, $practitioner, $schedule, $request->user(), $purchase);

            Log::channel('stripe_transfer_success')->info("The practitioner received transfer", [
                'user_id'        => $request->user()->id,
                'practitioner'   => $practitioner->id,
                'price_id'       => $price->id,
                'service_id'     => $schedule->service->id,
                'schedule_id'    => $schedule->id,
                'payment_method' => $payment_method_id,
                'amount'         => $request->amount,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::channel('stripe_transfer_fail')->info("The practitioner could not received transfer", [
                'user_id'        => $request->user()->id,
                'practitioner'   => $practitioner->id,
                'price_id'       => $price->id,
                'service_id'     => $schedule->service->id,
                'schedule_id'    => $schedule->id,
                'payment_method' => $payment_method_id,
                'amount'         => $request->amount,
                'message'        => $e->getMessage(),
            ]);
            abort(500);
        }
    }

    protected function payInstant($request, $schedule, $price, $cost, $stripe, $purchase, $practitioner)
    {
        $payment_method_id = run_action(GetViablePaymentMethod::class, $practitioner, $request->payment_method_id);
        $paymentIntent = null;
        try {
            $client = $request->user();
            $refference = implode(', ', $purchase->bookings->pluck('reference')->toArray());
            $paymentIntent = $stripe->paymentIntents->create([
                'amount'               => $cost * 100,
                'currency'             => config('app.platform_currency'),
                'payment_method_types' => ['card'],
                'customer'             => Auth::user()->stripe_customer_id,
                'payment_method'       => $payment_method_id,
                'metadata'    => [
                    'Practitioner business email'       => $practitioner->business_email,
                    'Practitioner busines name'         => $practitioner->business_name,
                    'Practitioner stripe id'            => $practitioner->stripe_customer_id,
                    'Practitioner connected account id' => $practitioner->stripe_account_id,
                    'Client first name'                 => $client->first_name,
                    'Client last name'                  => $client->last_name,
                    'Client stripe id'                  => $client->stripe_customer_id,
                    'Booking refference'                => $refference
                ]
            ]);

            $paymentIntent       =
                $stripe->paymentIntents->confirm($paymentIntent->id, ['payment_method' => $payment_method_id]);
            $purchase->stripe_id = $paymentIntent->id;
            $purchase->save();
            DB::commit();
        } catch (\Stripe\Exception\ApiErrorException $e) {

            Log::channel('stripe_purchase_schedule_error')->info("Client could not purchase schedule", [
                'user_id'        => $request->user()->id,
                'price_id'       => $price->id,
                'service_id'     => $schedule->service->id,
                'schedule_id'    => $schedule->id,
                'payment_intent' => $paymentIntent->id ?? null,
                'payment_method' => $payment_method_id,
                'amount'         => $request->amount,
                'message'        => $e->getMessage(),
            ]);
            DB::rollBack();

            return abort(500);
        }

        Log::channel('stripe_purchase_schedule_success')->info("Client purchased schedule", [
            'user_id'        => $request->user()->id,
            'price_id'       => $price->id,
            'service_id'     => $schedule->service->id,
            'schedule_id'    => $schedule->id,
            'payment_intent' => $paymentIntent->id,
            'payment_method' => $payment_method_id,
            'amount'         => $request->amount,
        ]);

        try {
            run_action(TransferFundsWithCommissions::class, $cost, $practitioner, $schedule, $client, $purchase);

            Log::channel('stripe_transfer_success')->info("The practitioner received transfer", [
                'user_id'        => $request->user()->id,
                'practitioner'   => $practitioner->id,
                'price_id'       => $price->id,
                'service_id'     => $schedule->service->id,
                'schedule_id'    => $schedule->id,
                'payment_intent' => $paymentIntent->id ?? null,
                'payment_method' => $payment_method_id,
                'amount'         => $request->amount,
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {

            Log::channel('stripe_transfer_fail')->info("The practitioner could not received transfer", [
                'user_id'        => $request->user()->id,
                'practitioner'   => $practitioner->id,
                'price_id'       => $price->id,
                'service_id'     => $schedule->service->id,
                'schedule_id'    => $schedule->id,
                'payment_intent' => $paymentIntent->id ?? null,
                'payment_method' => $payment_method_id,
                'amount'         => $request->amount,
                'message'        => $e->getMessage(),
            ]);

        }
        return true;
    }

}
