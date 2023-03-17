<?php

namespace App\Http\Requests;

use App\Actions\Stripe\AccountHandler;
use App\Actions\Stripe\InvoiceHandler;
use App\Actions\Stripe\SubscriptionHandler;
use App\Actions\Stripe\PaymentIntentHandler;

class StripeRequest {

    public const EVENT_HANDLERS = [
        'customer'       => [
            'subscription' => SubscriptionHandler::class
        ],
        'invoice' => InvoiceHandler::class,
        'account' => AccountHandler::class,
        'payment_intent' => PaymentIntentHandler::class,
    ];

    private string $_type;
    private string $_account;
    private array $_data;


    /**
     * Event Types
     */
    private string $_resourceName;
    private string $_eventName;
    private ?string $_subEventName;

    public function __construct(Request $request) {
        $this->_type = $request->get('type', '');
        $this->_account = $request->get('account', '');
        $data = $request->get('data', []);
        if (isset($data['object'])) {
            $this->_data = $data['object'];
        } else {
            $this->_data = [];
        }

        if (!empty($this->_type)) {
            [$this->_resourceName, $this->_eventName, $this->_subEventName] =
                array_pad(explode('.', $this->_type), 3, null);
        }
    }

    /**
     * @return mixed|string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * @return object
     */
    public function getObject() {
        return $this->_data;
    }

    /**
     * @return object
     */
    public function getAccount() {
        return $this->_account;
    }

    /**
     * @return string
     */
    public function getResourceName(): string {
        return $this->_resourceName;
    }

    /**
     * @return string
     */
    public function getEventName(): string {
        return $this->_eventName;
    }

    /**
     * @return string|null
     */
    public function getSubEventName(): ?string {
        return $this->_subEventName;
    }

    /**
     * Get Handler from Event Maps
     *
     * @return string|null
     */
    public function getRequestHandler(): ?string {
        if (isset(self::EVENT_HANDLERS[$this->_resourceName])) {
            if (!is_array(self::EVENT_HANDLERS[$this->_resourceName])) {
                return self::EVENT_HANDLERS[$this->_resourceName];
            }

            if (isset(self::EVENT_HANDLERS[$this->_resourceName][$this->_eventName])) {
                if (!is_array(self::EVENT_HANDLERS[$this->_resourceName][$this->_eventName])) {
                    return self::EVENT_HANDLERS[$this->_resourceName][$this->_eventName];
                }

                if ($this->_subEventName !== null &&
                    isset(self::EVENT_HANDLERS[$this->_resourceName][$this->_eventName][$this->_subEventName])) {
                    return self::EVENT_HANDLERS[$this->_resourceName][$this->_eventName][$this->_subEventName];
                }
            }
        }
        return null;
    }

}
