<?php

declare(strict_types=1);

namespace App\DTO\Schedule;

use Stripe\StripeObject;

/**
 * ToDo: split into 2 services: DTO builder and DTO itself
 */
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

    /**
     * Url used for iframe to confirm the payment using 3ds 1 flow
     */
    private ?string $redirectUrl = null;

    /**
     * Identifies that this 3ds flow do not suppose to use url redirects
     * In this case we need to show a popup with instructions to go to bank app in order to confirm the operation
     * External confirmation is used for 3ds flow v2
     */
    private ?bool $is3dsConfirmationExternal = null;

    /**
     * @deprecated StripeObject contains data, which should be removed from response. remove it after debug
     */
    private ?StripeObject $nextAction;

    public function __construct(string $status, ?StripeObject $nextAction)
    {
        $this->status = $status;
        $this->nextAction = $nextAction;

        if (!$nextAction) {
            return;
        }

        $nextActionArray = $nextAction->toArray();
        $this->nextActionType = $nextActionArray['type'] ?? null;
        $nextActionTypeObject = $this->nextActionType && isset($nextActionArray[$this->nextActionType])
            ? $nextActionArray[$this->nextActionType] : null;

        $this->redirectUrl = $nextActionTypeObject[self::STRIPE_REDIRECT_URL_KEY] ?? null;
        $threeDsCheckType = $nextActionTypeObject['type'] ?? null;

        if ($this->redirectUrl && $threeDsCheckType === self::THREE_D_SECURE_URL_REDIRECT_TYPE) {
            $this->is3dsConfirmationExternal = false;

            return;
        }

        $this->is3dsConfirmationExternal = true;
    }

    public function toArray(): array
    {
        $data = [
            'status' => $this->status,
        ];

        if ($this->is3dsConfirmationExternal !== null) {
            $data['is_3ds_confirmation_external'] = $this->is3dsConfirmationExternal;
        }

        if ($this->nextActionType) {
            $data['next_action_type'] = $this->nextActionType;
        }

        if ($this->redirectUrl) {
            $data['3ds_redirect_url'] = $this->redirectUrl;
        }

        if ($this->nextAction) {
            $data['next_action'] = $this->nextAction->toArray();
        }

        return $data;
    }
}
