<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\CreatesApplication;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class InvoiceHandlerTest extends TestCase
{
    use CreatesApplication;
    use WithoutMiddleware;

    public function test_installment_invoice()
    {
        $requestRawJson = <<<EOD
{
  "id": "evt_1Kg4dsLDhKaKSxIVU0cv0MYY",
  "object": "event",
  "api_version": "2020-08-27",
  "created": 1647943723,
  "data": {
    "object": {
      "id": "in_1Kg4doLDhKaKSxIVbW9fKais",
      "object": "invoice",
      "account_country": "GB",
      "account_name": "Holistify Dev",
      "account_tax_ids": null,
      "amount_due": 767,
      "amount_paid": 767,
      "amount_remaining": 0,
      "application_fee_amount": null,
      "attempt_count": 1,
      "attempted": true,
      "auto_advance": false,
      "automatic_tax": {
        "enabled": false,
        "status": null
      },
      "billing_reason": "subscription_update",
      "charge": "ch_3Kg4dpLDhKaKSxIV1440E7Gs",
      "collection_method": "charge_automatically",
      "created": 1647943720,
      "currency": "gbp",
      "custom_fields": null,
      "customer": "cus_L59Tm3on1JXFOs",
      "customer_address": null,
      "customer_email": "test@test.ru",
      "customer_name": null,
      "customer_phone": null,
      "customer_shipping": null,
      "customer_tax_exempt": "none",
      "customer_tax_ids": [
      ],
      "default_payment_method": null,
      "default_source": null,
      "default_tax_rates": [
      ],
      "description": null,
      "discount": null,
      "discounts": [
      ],
      "due_date": null,
      "ending_balance": 0,
      "footer": null,
      "hosted_invoice_url": "https://invoice.stripe.com/i/acct_1KMvGuLDhKaKSxIV/test_YWNjdF8xS012R3VMRGhLYUtTeElWLF9MTW9HTEo1RFd4dXFnVVlVaGtIRmpiT2wzOHVFM1ltLDM4NDg0NTI00200Fn1lHWmz?s=ap",
      "invoice_pdf": "https://pay.stripe.com/invoice/acct_1KMvGuLDhKaKSxIV/test_YWNjdF8xS012R3VMRGhLYUtTeElWLF9MTW9HTEo1RFd4dXFnVVlVaGtIRmpiT2wzOHVFM1ltLDM4NDg0NTI00200Fn1lHWmz/pdf?s=ap",
      "last_finalization_error": null,
      "lines": {
        "object": "list",
        "data": [
          {
            "id": "il_1Kg4doLDhKaKSxIVqrRZfgbI",
            "object": "line_item",
            "amount": 767,
            "currency": "gbp",
            "description": "1 × Test bespoke (at £7.67 / every 3 days)",
            "discount_amounts": [
            ],
            "discountable": true,
            "discounts": [
            ],
            "livemode": false,
            "metadata": {
              "Practitioner business email": "practitioner@test.ru",
              "Practitioner business name": "practitioner",
              "Practitioner stripe id": "cus_L4zQQNOzKdevPD",
              "Practitioner connected account id": "acct_1KOpUMQ0Ib0KBapd",
              "Client first name": "test",
              "Client last name": "test",
              "Client stripe id": "cus_L59Tm3on1JXFOs",
              "Booking reference": "FI4OD649",
              "Type": "Installment Purchase"
            },
            "period": {
              "end": 1648202920,
              "start": 1647943720
            },
            "plan": {
              "id": "price_1Kg4dKLDhKaKSxIVIlF6nRZp",
              "object": "plan",
              "active": true,
              "aggregate_usage": null,
              "amount": 767,
              "amount_decimal": "767",
              "billing_scheme": "per_unit",
              "created": 1647943690,
              "currency": "gbp",
              "interval": "day",
              "interval_count": 3,
              "livemode": false,
              "metadata": {
              },
              "nickname": null,
              "product": "prod_L5A6IJwHpVZxpq",
              "tiers_mode": null,
              "transform_usage": null,
              "trial_period_days": null,
              "usage_type": "licensed"
            },
            "price": {
              "id": "price_1Kg4dKLDhKaKSxIVIlF6nRZp",
              "object": "price",
              "active": true,
              "billing_scheme": "per_unit",
              "created": 1647943690,
              "currency": "gbp",
              "livemode": false,
              "lookup_key": null,
              "metadata": {
              },
              "nickname": null,
              "product": "prod_L5A6IJwHpVZxpq",
              "recurring": {
                "aggregate_usage": null,
                "interval": "day",
                "interval_count": 3,
                "trial_period_days": null,
                "usage_type": "licensed"
              },
              "tax_behavior": "unspecified",
              "tiers_mode": null,
              "transform_quantity": null,
              "type": "recurring",
              "unit_amount": 767,
              "unit_amount_decimal": "767"
            },
            "proration": false,
            "proration_details": {
              "credited_items": null
            },
            "quantity": 1,
            "subscription": "sub_1Kg4dLLDhKaKSxIV6klnG7WY",
            "subscription_item": "si_LMoFhsXENwYyHF",
            "tax_amounts": [
            ],
            "tax_rates": [
            ],
            "type": "subscription"
          }
        ],
        "has_more": false,
        "total_count": 1,
        "url": "/v1/invoices/in_1Kg4doLDhKaKSxIVbW9fKais/lines"
      },
      "livemode": false,
      "metadata": {
      },
      "next_payment_attempt": null,
      "number": "0058EA82-0542",
      "on_behalf_of": null,
      "paid": true,
      "paid_out_of_band": false,
      "payment_intent": "pi_3Kg4dpLDhKaKSxIV1R03KTon",
      "payment_settings": {
        "payment_method_options": null,
        "payment_method_types": null
      },
      "period_end": 1647943720,
      "period_start": 1647943720,
      "post_payment_credit_notes_amount": 0,
      "pre_payment_credit_notes_amount": 0,
      "quote": null,
      "receipt_number": null,
      "starting_balance": 0,
      "statement_descriptor": null,
      "status": "paid",
      "status_transitions": {
        "finalized_at": 1647943720,
        "marked_uncollectible_at": null,
        "paid_at": 1647943720,
        "voided_at": null
      },
      "subscription": "sub_1Kg4dLLDhKaKSxIV6klnG7WY",
      "subtotal": 767,
      "tax": null,
      "test_clock": null,
      "total": 767,
      "total_discount_amounts": [
      ],
      "total_tax_amounts": [
      ],
      "transfer_data": {
        "amount": 690,
        "destination": "acct_1KOpUMQ0Ib0KBapd"
      },
      "webhooks_delivered_at": 1647943720
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": "req_mCt7vEuDYah74n",
    "idempotency_key": "c80e3aaa-37e3-4a31-977f-d5bf68ac4130"
  },
  "type": "invoice.payment_succeeded"
}
EOD;
        $requestJson = json_decode($requestRawJson, true);
        $response = $this->post(route('stripe.webhook'), $requestJson);
        $response->assertStatus(200);
    }


    public function test_installment_subscription_updated()
    {
        $requestRawJson = <<<EOD
{
  "id": "evt_1Kg4dsLDhKaKSxIVD48MVTeo",
  "object": "event",
  "api_version": "2020-08-27",
  "created": 1647943723,
  "data": {
    "object": {
      "id": "sub_1Kg4dLLDhKaKSxIV6klnG7WY",
      "object": "subscription",
      "application_fee_percent": null,
      "automatic_tax": {
        "enabled": false
      },
      "billing_cycle_anchor": 1647943720,
      "billing_thresholds": null,
      "cancel_at": 1648807691,
      "cancel_at_period_end": false,
      "canceled_at": 1647943720,
      "collection_method": "charge_automatically",
      "created": 1647943691,
      "current_period_end": 1648202920,
      "current_period_start": 1647943720,
      "customer": "cus_L59Tm3on1JXFOs",
      "days_until_due": null,
      "default_payment_method": "pm_1Kc9jsLDhKaKSxIVh3ZyP4XK",
      "default_source": null,
      "default_tax_rates": [
      ],
      "discount": null,
      "ended_at": null,
      "items": {
        "object": "list",
        "data": [
          {
            "id": "si_LMoFhsXENwYyHF",
            "object": "subscription_item",
            "billing_thresholds": null,
            "created": 1647943691,
            "metadata": {
              "Practitioner business email": "practitioner@test.ru",
              "Practitioner business name": "practitioner",
              "Practitioner stripe id": "cus_L4zQQNOzKdevPD",
              "Practitioner connected account id": "acct_1KOpUMQ0Ib0KBapd",
              "Client first name": "test",
              "Client last name": "test",
              "Client stripe id": "cus_L59Tm3on1JXFOs",
              "Booking reference": "FI4OD649",
              "Type": "Installment Purchase"
            },
            "plan": {
              "id": "price_1Kg4dKLDhKaKSxIVIlF6nRZp",
              "object": "plan",
              "active": true,
              "aggregate_usage": null,
              "amount": 767,
              "amount_decimal": "767",
              "billing_scheme": "per_unit",
              "created": 1647943690,
              "currency": "gbp",
              "interval": "day",
              "interval_count": 3,
              "livemode": false,
              "metadata": {
              },
              "nickname": null,
              "product": "prod_L5A6IJwHpVZxpq",
              "tiers_mode": null,
              "transform_usage": null,
              "trial_period_days": null,
              "usage_type": "licensed"
            },
            "price": {
              "id": "price_1Kg4dKLDhKaKSxIVIlF6nRZp",
              "object": "price",
              "active": true,
              "billing_scheme": "per_unit",
              "created": 1647943690,
              "currency": "gbp",
              "livemode": false,
              "lookup_key": null,
              "metadata": {
              },
              "nickname": null,
              "product": "prod_L5A6IJwHpVZxpq",
              "recurring": {
                "aggregate_usage": null,
                "interval": "day",
                "interval_count": 3,
                "trial_period_days": null,
                "usage_type": "licensed"
              },
              "tax_behavior": "unspecified",
              "tiers_mode": null,
              "transform_quantity": null,
              "type": "recurring",
              "unit_amount": 767,
              "unit_amount_decimal": "767"
            },
            "quantity": 1,
            "subscription": "sub_1Kg4dLLDhKaKSxIV6klnG7WY",
            "tax_rates": [
            ]
          }
        ],
        "has_more": false,
        "total_count": 1,
        "url": "/v1/subscription_items?subscription=sub_1Kg4dLLDhKaKSxIV6klnG7WY"
      },
      "latest_invoice": "in_1Kg4doLDhKaKSxIVbW9fKais",
      "livemode": false,
      "metadata": {
        "Practitioner business email": "practitioner@test.ru",
        "Practitioner business name": "practitioner",
        "Practitioner stripe id": "cus_L4zQQNOzKdevPD",
        "Practitioner connected account id": "acct_1KOpUMQ0Ib0KBapd",
        "Client first name": "test",
        "Client last name": "test",
        "Client stripe id": "cus_L59Tm3on1JXFOs",
        "Booking reference": "FI4OD649",
        "Type": "Installment Purchase"
      },
      "next_pending_invoice_item_invoice": null,
      "pause_collection": null,
      "payment_settings": {
        "payment_method_options": null,
        "payment_method_types": null
      },
      "pending_invoice_item_interval": null,
      "pending_setup_intent": null,
      "pending_update": null,
      "plan": {
        "id": "price_1Kg4dKLDhKaKSxIVIlF6nRZp",
        "object": "plan",
        "active": true,
        "aggregate_usage": null,
        "amount": 767,
        "amount_decimal": "767",
        "billing_scheme": "per_unit",
        "created": 1647943690,
        "currency": "gbp",
        "interval": "day",
        "interval_count": 3,
        "livemode": false,
        "metadata": {
        },
        "nickname": null,
        "product": "prod_L5A6IJwHpVZxpq",
        "tiers_mode": null,
        "transform_usage": null,
        "trial_period_days": null,
        "usage_type": "licensed"
      },
      "quantity": 1,
      "schedule": null,
      "start_date": 1647943691,
      "status": "active",
      "test_clock": null,
      "transfer_data": {
        "amount_percent": 90,
        "destination": "acct_1KOpUMQ0Ib0KBapd"
      },
      "trial_end": 1647943719,
      "trial_start": 1647943691
    },
    "previous_attributes": {
      "billing_cycle_anchor": 1648202891,
      "canceled_at": 1647943691,
      "current_period_end": 1648202891,
      "current_period_start": 1647943691,
      "latest_invoice": "in_1Kg4dLLDhKaKSxIV7qyFq2YE",
      "status": "trialing",
      "trial_end": 1648202891
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": "req_mCt7vEuDYah74n",
    "idempotency_key": "c80e3aaa-37e3-4a31-977f-d5bf68ac4130"
  },
  "type": "customer.subscription.updated"
}
EOD;
        $requestJson = json_decode($requestRawJson, true);
        $response = $this->post(route('stripe.webhook'), $requestJson);
        $response->assertStatus(200);
    }


    public function test_installment_payment_intent()
    {
        $requestRawJson = <<<EOD
{
  "id": "evt_3Kg4dpLDhKaKSxIV1pUStCxR",
  "object": "event",
  "api_version": "2020-08-27",
  "created": 1647943723,
  "data": {
    "object": {
      "id": "pi_3Kg4dpLDhKaKSxIV1R03KTon",
      "object": "payment_intent",
      "amount": 767,
      "amount_capturable": 0,
      "amount_received": 767,
      "application": null,
      "application_fee_amount": null,
      "automatic_payment_methods": null,
      "canceled_at": null,
      "cancellation_reason": null,
      "capture_method": "automatic",
      "charges": {
        "object": "list",
        "data": [
          {
            "id": "ch_3Kg4dpLDhKaKSxIV1440E7Gs",
            "object": "charge",
            "amount": 767,
            "amount_captured": 767,
            "amount_refunded": 0,
            "application": null,
            "application_fee": null,
            "application_fee_amount": null,
            "balance_transaction": "txn_3Kg4dpLDhKaKSxIV1MIohsvB",
            "billing_details": {
              "address": {
                "city": null,
                "country": null,
                "line1": null,
                "line2": null,
                "postal_code": "111",
                "state": null
              },
              "email": null,
              "name": "test2",
              "phone": null
            },
            "calculated_statement_descriptor": "HOLISTIFY.ME",
            "captured": true,
            "created": 1647943721,
            "currency": "gbp",
            "customer": "cus_L59Tm3on1JXFOs",
            "description": "Subscription update",
            "destination": "acct_1KOpUMQ0Ib0KBapd",
            "dispute": null,
            "disputed": false,
            "failure_code": null,
            "failure_message": null,
            "fraud_details": {
            },
            "invoice": "in_1Kg4doLDhKaKSxIVbW9fKais",
            "livemode": false,
            "metadata": {
            },
            "on_behalf_of": null,
            "order": null,
            "outcome": {
              "network_status": "approved_by_network",
              "reason": null,
              "risk_level": "normal",
              "risk_score": 10,
              "seller_message": "Payment complete.",
              "type": "authorized"
            },
            "paid": true,
            "payment_intent": "pi_3Kg4dpLDhKaKSxIV1R03KTon",
            "payment_method": "pm_1Kc9jsLDhKaKSxIVh3ZyP4XK",
            "payment_method_details": {
              "card": {
                "brand": "visa",
                "checks": {
                  "address_line1_check": null,
                  "address_postal_code_check": "pass",
                  "cvc_check": null
                },
                "country": "US",
                "exp_month": 11,
                "exp_year": 2022,
                "fingerprint": "KO8t6ZwWQmhztOsq",
                "funding": "credit",
                "installments": null,
                "last4": "4242",
                "mandate": null,
                "network": "visa",
                "three_d_secure": null,
                "wallet": null
              },
              "type": "card"
            },
            "receipt_email": null,
            "receipt_number": null,
            "receipt_url": "https://pay.stripe.com/receipts/acct_1KMvGuLDhKaKSxIV/ch_3Kg4dpLDhKaKSxIV1440E7Gs/rcpt_LMoGnwx7d94FBv1N6qDnADKmzY7ryg0",
            "refunded": false,
            "refunds": {
              "object": "list",
              "data": [
              ],
              "has_more": false,
              "total_count": 0,
              "url": "/v1/charges/ch_3Kg4dpLDhKaKSxIV1440E7Gs/refunds"
            },
            "review": null,
            "shipping": null,
            "source": null,
            "source_transfer": null,
            "statement_descriptor": null,
            "statement_descriptor_suffix": null,
            "status": "succeeded",
            "transfer": "tr_3Kg4dpLDhKaKSxIV1rlL86Zb",
            "transfer_data": {
              "amount": 690,
              "destination": "acct_1KOpUMQ0Ib0KBapd"
            },
            "transfer_group": "group_pi_3Kg4dpLDhKaKSxIV1R03KTon"
          }
        ],
        "has_more": false,
        "total_count": 1,
        "url": "/v1/charges?payment_intent=pi_3Kg4dpLDhKaKSxIV1R03KTon"
      },
      "client_secret": "pi_3Kg4dpLDhKaKSxIV1R03KTon_secret_TH5Dz1PW9h0SRcXTGGT7pjgxd",
      "confirmation_method": "automatic",
      "created": 1647943721,
      "currency": "gbp",
      "customer": "cus_L59Tm3on1JXFOs",
      "description": "Subscription update",
      "invoice": "in_1Kg4doLDhKaKSxIVbW9fKais",
      "last_payment_error": null,
      "livemode": false,
      "metadata": {
      },
      "next_action": null,
      "on_behalf_of": null,
      "payment_method": "pm_1Kc9jsLDhKaKSxIVh3ZyP4XK",
      "payment_method_options": {
        "card": {
          "installments": null,
          "mandate_options": null,
          "network": null,
          "request_three_d_secure": "automatic"
        }
      },
      "payment_method_types": [
        "card"
      ],
      "processing": null,
      "receipt_email": null,
      "review": null,
      "setup_future_usage": null,
      "shipping": null,
      "source": null,
      "statement_descriptor": null,
      "statement_descriptor_suffix": null,
      "status": "succeeded",
      "transfer_data": {
        "amount": 690,
        "destination": "acct_1KOpUMQ0Ib0KBapd"
      },
      "transfer_group": "group_pi_3Kg4dpLDhKaKSxIV1R03KTon"
    }
  },
  "livemode": false,
  "pending_webhooks": 1,
  "request": {
    "id": "req_mCt7vEuDYah74n",
    "idempotency_key": "c80e3aaa-37e3-4a31-977f-d5bf68ac4130"
  },
  "type": "payment_intent.succeeded"
}
EOD;
        $requestJson = json_decode($requestRawJson, true);
        $response = $this->post(route('stripe.webhook'), $requestJson);
        $response->assertStatus(200);
    }
}
