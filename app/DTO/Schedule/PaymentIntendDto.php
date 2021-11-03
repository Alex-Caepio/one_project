<?php

declare(strict_types=1);

namespace App\DTO\Schedule;

use Stripe\StripeObject;

class PaymentIntendDto
{
    private string $status;

    /**
     * Type of the next action to perform
     * Possible values: redirect_to_url, use_stripe_sdk, alipay_handle_redirect, or oxxo_display_details.
     *
     * @see https://stripe.com/docs/api/payment_intents/object#payment_intent_object-next_action-type
     */
    private ?string $type = null;

    /**
     * The URL you must redirect your customer to in order to authenticate the payment.
     *
     * @see https://stripe.com/docs/api/payment_intents/object#payment_intent_object-next_action-redirect_to_url
     */
    private ?string $url = null;

    /**
     * If the customer does not exit their browser while authenticating,
     * they will be redirected to this specified URL after completion.
     *
     * @see https://stripe.com/docs/api/payment_intents/object#payment_intent_object-next_action-redirect_to_url
     */
    private ?string $returnUrl = null;

    /**
     * @deprecated StripeObject contains data, which should be removed from response
     */
    private ?StripeObject $nextAction;

    public function __construct(string $status, ?StripeObject $nextAction)
    {
        $this->status = $status;
        $this->nextAction = $nextAction;

        if (!$nextAction instanceof StripeObject) {
            return;
        }

        if ($nextAction->offsetExists('type')) {
            $data['type'] = $nextAction->offsetGet('type');
        }

        if ($nextAction->offsetExists('redirect_to_url')) {
            $redirectToUrl = $nextAction->offsetGet('redirect_to_url');

            if (is_array($redirectToUrl) && isset($redirectToUrl['return_url'])) {
                $data['return_url'] = $redirectToUrl['return_url'];
            }

            if (is_array($redirectToUrl) && isset($redirectToUrl['url'])) {
                $this->url = $redirectToUrl['url'];
            }
        }
    }

    public function toArray(): array
    {
        $data = ['status' => $this->status];

        if ($this->type) {
            $data['type'] = $this->type;
        }

        if ($this->url) {
            $data['url'] = $this->url;
        }

        if ($this->returnUrl) {
            $data['return_url'] = $this->returnUrl;
        }

        if ($this->nextAction) {
            $data['next_action'] = $this->nextAction->toArray();

            // debug info
            $data['next_action_keys'] = $this->nextAction->keys();
            $data['next_action_values'] = $this->nextAction->values();
        }

        return $data;
    }
}
