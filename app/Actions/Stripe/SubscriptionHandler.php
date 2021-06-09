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
 * The key event is invoice.payment_failed, the rest just update various attributes on the corresponding Subscription and Customer.
 * Stripe will attempt payment three times. After the third failed attempt the
 * customer.subscription.updated event will be replaced
 * with a customer.subscription.deleted event.

invoice.payment_failed (Invoice )
charge.failed (Charge )
customer.subscription.updated (Subscription )
customer.updated (Customer )
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
                    case 'created':
                        $this->handleCreate();
                        break;
                    case 'updated':
                        $this->handleUpdate();
                        break;
                    case 'deleted':
                        $this->handleDelete();
                        break;

                    case 'trial_will_end':
                    default:
                        break;
                }
            }
        }
    }


    /**
     * @return void
     */
    private function retrieveCustomer(): void {
        $this->customer = User::where('stripe_customer_id', $this->_requestCustomerId)->with('plan')->first();
    }

    private function handleCreate(): void {
        //
    }

    private function handleUpdate(): void {
        // Handle changes of Plan Date
        // Add Logs && Check Emails
        if ($this->customer->plan instanceof Plan) {
            $this->customer->stripe_plan_id = $this->_requestSubscriptionId;
            $this->customer->plan_from = null;
            $this->customer->plan_until = null;
            $this->customer->save();
            Log::info('Subscription has been updated');
        }
    }

    private function handleDelete(): void {
        // Handle cancellation of subscription
        // Add Logs Check Emails
        if ($this->customer->plan instanceof Plan && $this->customer->stripe_plan_id === $this->_requestSubscriptionId) {
            $this->customer->plan_id = null;
            $this->customer->stripe_plan_id = null;
            $this->customer->plan_from = null;
            $this->customer->plan_until = null;
            $this->customer->save();
            Log::info('Subscription has been cancelled');
        }
    }

}
