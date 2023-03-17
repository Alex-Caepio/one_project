<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
            'permission' => 0777
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'permission' => 0777
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
            'permission' => 0777
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
            'permission' => 0777
        ],

        'stripe_plans_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_plan_errors.log'),
            'permission' => 0777
        ],

        'stripe_plans_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_plan_success.log'),
            'permission' => 0777
        ],

        'stripe_plans_info' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_plan_info.log'),
            'permission' => 0777
        ],

        'stripe_client_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_client_error.log'),
            'permission' => 0777
        ],

        'stripe_insufficient_balance' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_insufficient_balance.log'),
            'permission' => 0777
        ],

        'stripe_client_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_client_success.log'),
            'permission' => 0777
        ],

        'stripe_price_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_price_error.log'),
            'permission' => 0777
        ],

        'stripe_price_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_price_success.log'),
            'permission' => 0777
        ],

        'stripe_product_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_product_error.log'),
            'permission' => 0777
        ],

        'stripe_product_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_product_success.log'),
            'permission' => 0777
        ],

        'stripe_purchase_schedule_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_purchase_schedule_error.log'),
            'permission' => 0777
        ],

        'stripe_purchase_schedule_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_purchase_schedule_success.log'),
            'permission' => 0777
        ],

        'stripe_payment_method_update_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_payment_method_update_error.log'),
            'permission' => 0777
        ],

        'stripe_transfer_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_transfer_success.log'),
            'permission' => 0777
        ],

        'stripe_transfer_fail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_transfer_fail.log'),
            'permission' => 0777
        ],

        'stripe_refund_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_refund_success.log'),
            'permission' => 0777
        ],

        'stripe_refund_info' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_refund_info.log'),
            'permission' => 0777
        ],

        'stripe_refund_fail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_refund_fail.log'),
            'permission' => 0777
        ],

        'stripe_webhooks_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_webhooks_error.log'),
            'permission' => 0777
        ],

        'stripe_webhooks_success' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_webhooks_success.log'),
            'permission' => 0777
        ],

        'stripe_webhooks_info' => [
            'driver' => 'daily',
            'path' => storage_path('logs/stripe_webhooks_info.log'),
            'permission' => 0777
        ],

        'emails' => [
            'path' => storage_path('logs/emails.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'google_authorisation_failed' => [
            'path' => storage_path('logs/google_authorization_failed.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'google_authorisation_success' => [
            'path' => storage_path('logs/google_authorization_success.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'google_calendar_failed' => [
            'path' => storage_path('logs/google_calendar_failed.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'google_calendar_success' => [
            'path' => storage_path('logs/google_calendar_success.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'promotion_status_update' => [
            'path' => storage_path('logs/promotion_status_update.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'console_commands_handler' => [
            'path' => storage_path('logs/console_commands_handler.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'practitioner_commissions_success' => [
            'path' => storage_path('logs/practitioner_commissions_success.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'practitioner_commissions_error' => [
            'path' => storage_path('logs/practitioner_commissions_error.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'practitioner_cancel_error' => [
            'path' => storage_path('logs/practitioner_cancel_error.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'stripe_installment_fail' => [
            'path' => storage_path('logs/stripe_installment_fail.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

        'stripe_installment_success' => [
            'path' => storage_path('logs/stripe_installment_success.log'),
            'driver' => 'daily',
            'permission' => 0777
        ],

    ],

];
