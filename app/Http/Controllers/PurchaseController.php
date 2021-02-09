<?php

namespace App\Http\Controllers;

use App\Actions\Promo\CalculatePromoPrice;
use App\Actions\Stripe\GetViablePaymentMethod;
use App\Actions\Stripe\TransferFundsWithCommissions;
use App\Filters\PurchaseFilters;
use App\Http\Requests\PromotionCode\ValidatePromocodeRequest;
use App\Http\Requests\Request;
use App\Http\Requests\Schedule\PurchaseScheduleRequest;
use App\Models\Booking;
use App\Models\Plan;
use App\Models\PractitionerCommission;
use App\Models\PromotionCode;
use App\Models\Purchase;
use App\Models\Schedule;
use App\Models\ScheduleFreeze;
use App\Transformers\PurchaseTransformer;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
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
        $price       = $schedule->prices()->find($request->get('price_id'));
        $cost        = $price->cost;
        $practitoner = $schedule->service->user;

        $promo = null;
        if ($request->has('promo_code')) {
            $promo = PromotionCode::where('name', $request->get('promo_code'))->with('promotion')->first();
            if ($promo instanceof PromotionCode) {
                $cost = run_action(CalculatePromoPrice::class, $promo, $cost);
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
        $purchase->save();

        if ($schedule->service->service_type_id === 'appointment') {
            $availabilities = $request->get('availabilities');
            foreach ($availabilities as $availability) {
                $booking                  = new Booking();
                $booking->user_id         = $request->user()->id;
                $booking->practitioner_id = $schedule->service->user_id;
                $booking->price_id        = $request->get('price_id');
                $booking->schedule_id     = $schedule->id;
                $booking->availability_id = $availability['availability_id'];
                $booking->datetime_from   = $availability['datetime_from'];
                $datetimeTo               = (new Carbon($booking->datetime_from))->addMinutes($price->duration);
                $booking->datetime_to     = $datetimeTo->format('Y-m-d H:i:s');
                $booking->cost            = $cost;
                $booking->purchase_id     = $purchase->id;
                $booking->save();
            }
        } else {
            $booking              = new Booking();
            $booking->user_id     = $request->user()->id;
            $booking->practitioner_id = $schedule->service->user_id;
            $booking->price_id    = $request->get('price_id');
            $booking->schedule_id = $schedule->id;
            $booking->cost        = $cost;
            $booking->purchase_id = $purchase->id;
            $booking->save();
        }

        ScheduleFreeze::where('schedule_id', $schedule->id)
            ->where('user_id', $request->user()->id)
            ->delete();


        try {
            $payment_method_id = run_action(GetViablePaymentMethod::class, $practitoner, $request->payment_method_id);

            $paymentIntent =  $stripe->paymentIntents->create([
                'amount'               => $cost,
                'currency'             => $price->name,
                'payment_method_types' => ['card'],
                'customer'             => Auth::user()->stripe_customer_id,
                'payment_method'       => $payment_method_id
            ]);

            $paymentIntent       = $stripe->paymentIntents->confirm($paymentIntent->id, ['payment_method' => $payment_method_id]);
            $purchase->stripe_id = $paymentIntent->id;
            $purchase->save();

        } catch (\Stripe\Exception\ApiErrorException $e) {

            Log::channel('stripe_purchase_schedule_error')->info("Client could not purchase schedule", [
                'user_id'        => $request->user()->id,
                'price_id'       => $price->id,
                'service_id'     => $schedule->service->id,
                'schedule_id'    => $schedule->id,
                'payment_intent' => $paymentIntent->id,
                'payment_method' => $payment_method_id,
                'message'        => $e->getMessage(),
            ]);

            return abort( 500);
        }

        try {
            run_action(TransferFundsWithCommissions::class, $cost, $practitoner);
            //log transfer + transfers table record
        } catch (\Stripe\Exception\ApiErrorException $e) {
            //log transfer + transfers table record
        }

        Log::channel('stripe_purchase_schedule_success')->info("Client purchase schedule", [
            'user_id'        => $request->user()->id,
            'price_id'       => $price->id,
            'service_id'     => $schedule->service->id,
            'schedule_id'    => $schedule->id,
            'payment_intent' => $paymentIntent->id,
            'payment_method' => $payment_method_id,
        ]);

        return response(null, 200);

//        if ($schedule->isSoldOut()){
//            $stripe->charges->create([
//                'amount' => $newSchedule,
//                'currency' => 'usd',
//                'customer' => $user->stripe_id,
//                'description' => 'My First Test Charge (created for API docs)',
//            ]);
//            $schedule->users()->save($user);
//        }
    }

    public function validatePromocode(ValidatePromocodeRequest $request, Schedule $schedule)
    {
        $name         = $request->get('promo_code');
        $scheduleCost = $schedule->cost;
        $promo        = PromotionCode::where('name', $name)->first();
        return run_action(CalculatePromoPrice::class, $promo, $scheduleCost);
    }

}
