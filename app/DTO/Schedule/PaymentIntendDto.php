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
    private $nextActionTypeObj = null;

    public function __construct(string $status, ?StripeObject $nextAction)
    {
        $this->status = $status;
        $this->nextAction = $nextAction;

        if (!$nextAction) {
            return;
        }

        $nextActionArray = $nextAction->toArray();
        $this->nextActionType = $nextActionArray['type'] ?? null;
        $this->nextActionTypeObj = $this->nextActionType && isset($nextActionArray[$this->nextActionType])
            ? $nextActionArray[$this->nextActionType] : null;
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

        if ($this->nextActionTypeObj) {
            $data['next_action_type_obj_type'] = gettype($this->nextActionTypeObj);
            $data['next_action_type_obj'] = var_export($this->nextActionTypeObj);
            $data['next_action_type_obj_class'] = is_object($this->nextActionTypeObj) ? get_class($this->nextActionTypeObj) : null;
            $data['next_action_type_value'] = $this->nextActionTypeObj;
        }

        return $data;
    }
}
