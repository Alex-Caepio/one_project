<?php

namespace App\Actions\Stripe;

use App\Http\Requests\Request;
use App\Http\Requests\StripeRequest;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/****
 * Class SubscriptionHandler
 * 12. Invoice charge attempt fails
 * When you run a subscription service eventually you'll have customers who cancel their card without telling you.
 * When that happens Stripe will send you a series of events.
 * The key event is invoice.payment_failed, the rest just update various attributes on the corresponding Subscription
 * and Customer. Stripe will attempt payment three times. After the third failed attempt the
 * customer.subscription.updated event will be replaced with a customer.subscription.deleted event.
 *
 * invoice.payment_failed (Invoice )
 * charge.failed (Charge )
 * customer.subscription.updated (Subscription )
 * customer.updated (Customer )
 *
 * @package App\Actions\Stripe
 */
class SubscriptionHandler {

    private string $_requestSubscriptionId;
    private string $_requestCustomerId;

    private ?User $customer;

    /**
     * @param \App\Http\Requests\StripeRequest $request
     */
    public function execute(StripeRequest $request): void {
        Log::info('[[' . __METHOD__ . ']]: start handle Subscription Event: ' . $request->getSubEventName());
        $dataObject = $request->getObject();

        if (isset($dataObject['customer'], $dataObject['id'])) {
            $this->_requestCustomerId = $dataObject['customer'];
            $this->_requestSubscriptionId = $dataObject['id'];
            $this->retrieveCustomer();

            if ($this->customer instanceof User) {
                switch ($request->getSubEventName()) {
                    case 'deleted':
                        $this->handleDelete();
                        break;

                    case 'trial_will_end':
                    case 'created':
                    case 'updated':
                    default:
                        break;
                }
            } else {
                Log::channel('stripe_webhooks_error')->info('User was not found: ', [
                    'customer_id'     => $this->_requestCustomerId,
                    'subscription_id' => $this->_requestSubscriptionId
                ]);
            }
        }
    }


    /**
     * @return void
     */
    private function retrieveCustomer(): void {
        $this->customer = User::where('stripe_customer_id', $this->_requestCustomerId)
                              ->where('account_type', User::ACCOUNT_PRACTITIONER)->has('plan')->with('plan')->first();
    }


    private function handleDelete(): void {
        $logContent = [
            'user_id'           => $this->_requestCustomerId,
            'stripe_plan_id'    => $this->customer->stripe_plan_id,
            'plan_id'           => $this->customer->plan_id,
            'requested_plan_id' => $this->_requestSubscriptionId
        ];
        if ($this->customer->stripe_plan_id === $this->_requestSubscriptionId) {
            $this->customer->plan_id = null;
            $this->customer->stripe_plan_id = null;
            $this->customer->plan_from = null;
            $this->customer->plan_until = null;
            $this->customer->save();
            Log::channel('stripe_plans_success')->info('Plan successfully cancelled', $logContent);
        } else {
            Log::channel('stripe_webhooks_error')->info('Cancelled plan does not match existing plan: ', $logContent);
        }
    }

}
