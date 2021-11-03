<?php

declare(strict_types=1);

namespace App\DTO\Schedule;

use Stripe\StripeObject;

class PaymentIntendDto
{
    private const THREE_D_SECURE_URL_REDIRECT_TYPE = 'three_d_secure_redirect';
    private const STRIPE_REDIRECT_URL_KEY = 'stripe_js';

    private string $status;

    /**
     * Type of the next action to perform
     * Possible values: redirect_to_url, use_stripe_sdk, alipay_handle_redirect, or oxxo_display_details.
     *
     * @see https://stripe.com/docs/api/payment_intents/object#payment_intent_object-next_action-type
     */
    private ?string $nextActionType = null;

    private ?string $redirectUrl = null;

    private bool $is3DsUrlRedirect = false;

    /**
     * @deprecated StripeObject contains data, which should be removed from response. remove it after debug
     */
    private ?StripeObject $nextAction;

    /**
     * @deprecated remove it after debug
     */
    private ?array $nextActionTypeArray = null;

    public function __construct(string $status, ?StripeObject $nextAction)
    {
        $this->status = $status;
        $this->nextAction = $nextAction;

        if (!$nextAction) {
            return;
        }

        $this->nextActionType = $nextAction->offsetGet('type');

        $nextActionType = $this->nextActionType ?? $nextAction->offsetGet($this->nextActionType);
        $this->nextActionTypeArray = json_decode(json_encode($nextActionType, JSON_THROW_ON_ERROR), true);
        if ( isset($this->nextActionTypeArray['type']) && $this->nextActionTypeArray['type'] === self::THREE_D_SECURE_URL_REDIRECT_TYPE ) {
            $this->is3DsUrlRedirect = true;
            $this->redirectUrl = $this->nextActionTypeArray[self::STRIPE_REDIRECT_URL_KEY];
        }
    }

    public function toArray(): array
    {
        $data = [
            'status' => $this->status,
            'is_3ds_url_redirect' => $this->is3DsUrlRedirect,
        ];

        if ($this->nextActionType) {
            $data['next_action_type'] = $this->nextActionType;
        }

        if ($this->redirectUrl) {
            $data['3ds_redirect_url'] = $this->redirectUrl;
        }

        if ($this->nextAction) {
            $data['next_action'] = $this->nextAction->toArray();
        }

        if ($this->nextActionTypeArray) {
            $data['next_action_type_value'] = $this->nextActionTypeArray;
        }

        return $data;
    }
}
