<?php

declare(strict_types=1);

namespace App\DTO\Schedule;

use Stripe\StripeObject;

class PaymentIntentDto
{
    private const THREE_D_SECURE_URL_REDIRECT_TYPE = 'three_d_secure_redirect';
    private const STRIPE_REDIRECT_URL_KEY = 'stripe_js';

    /**
     * Status of transaction, returned from stripe API
     */
    private string $status;

    private ?string $clientSecret;

    private ?string $confirmationMethod;

    /**
     * Url used for iframe to confirm the payment using 3ds flow version 1
     */
    private ?string $redirectUrl = null;

    /**
     * If value is true - 3ds version 2 is used. Show popup with information to check bank app (no iframe)
     * If value is false - 3ds version 1 is used. Iframe is used to perform 3ds check.
     * If value is null - 3ds is not required or optional. This key will not be included in response in this case
     */
    private ?bool $is3dsConfirmationExternal = null;

    public function __construct(
        string        $status,
        ?string       $clientSecret,
        ?string       $confirmationMethod,
        ?StripeObject $nextAction
    ) {
        $this->status = $status;
        $this->clientSecret = $clientSecret;
        $this->confirmationMethod = $confirmationMethod;

        if (!$nextAction) {
            return;
        }

        $nextActionArray = $nextAction->toArray();
        $nextActionType = $nextActionArray['type'] ?? null;
        $nextActionTypeObject = $nextActionType && isset($nextActionArray[$nextActionType])
            ? $nextActionArray[$nextActionType] : null;

        $this->redirectUrl = $nextActionTypeObject[self::STRIPE_REDIRECT_URL_KEY] ?? null;
        $threeDsCheckType = $nextActionTypeObject['type'] ?? null;

        $this->is3dsConfirmationExternal = $this->check3dsConfirmationExternal($threeDsCheckType, $this->redirectUrl);
    }

    public function toArray(): array
    {
        $data = [
            'status' => $this->status,
        ];

        if ($this->clientSecret) {
            $data['client_secret'] = $this->clientSecret;
        }

        if ($this->confirmationMethod) {
            $data['confirmation_method'] = $this->confirmationMethod;
        }

        if ($this->is3dsConfirmationExternal !== null) {
            $data['is_3ds_confirmation_external'] = $this->is3dsConfirmationExternal;
        }

        if ($this->redirectUrl) {
            $data['3ds_redirect_url'] = $this->redirectUrl;
        }

        return $data;
    }

    private function check3dsConfirmationExternal(?string $threeDsCheckType, ?string $redirectUrl): bool
    {
        return !($redirectUrl && $threeDsCheckType === self::THREE_D_SECURE_URL_REDIRECT_TYPE);
    }
}
