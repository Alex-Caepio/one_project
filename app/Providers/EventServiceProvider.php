<?php

namespace App\Providers;

use App\Events\AccountDeleted;
use App\Events\AccountTerminatedByAdmin;
use App\Events\AccountUpgradedToPractitioner;
use App\Events\AppointmentBooked;
use App\Events\ArticlePublished;
use App\Events\ArticleUnpublished;
use App\Events\BookingCancelledByPractitioner;
use App\Events\BookingCancelledByClient;
use App\Events\BookingCancelledToClient;
use App\Events\BookingConfirmation;
use App\Events\BookingDeposit;
use App\Events\BookingEventVirtualWithDeposit;
use App\Events\BookingReminder;
use App\Events\BookingRescheduleAcceptedByClient;
use App\Events\BookingRescheduleClientToSelectAppt;
use App\Events\BookingRescheduleOfferedByPractitioner;
use App\Events\BusinessProfileLive;
use App\Events\BusinessProfileUnpublished;
use App\Events\ChangeOfSubscription;
use App\Events\ClientRescheduledFyi;
use App\Events\ContractualServiceUpdateDeclinedBookingCancelled;
use App\Events\InstalmentPaymentReminder;
use App\Events\PasswordChanged;
use App\Events\PasswordReset;
use App\Events\PurchaseCancelledByPractitioner;
use App\Events\RescheduleRequestDeclinedByClient;
use App\Events\RescheduleRequestNoReplyFromClient;
use App\Events\ServiceListingLive;
use App\Events\ServicePurchased;
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Events\ServiceUnpublished;
use App\Events\ServiceUpdated;
use App\Events\ServiceUpdatedByPractitionerContractual;
use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Events\ServiceUpdatedNonContractual;
use App\Events\SubscriptionConfirmation;
use App\Events\UserRegistered;
use App\Listeners\AppointmentBookedEventHandler;
use App\Listeners\Emails\AccountDeletedEmail;
use App\Listeners\Emails\AccountTerminatedByAdminEmail;
use App\Listeners\Emails\AccountUpgradedToPractitionerEmail;
use App\Listeners\Emails\ArticlePublishedEmail;
use App\Listeners\Emails\ArticleUnpublishedEmail;
use App\Listeners\Emails\BookingCancelledByClientEmail;
use App\Listeners\Emails\BookingCancelledByPractitionerEmail;
use App\Listeners\Emails\BookingCancelledToClientEmail;
use App\Listeners\Emails\BookingConfirmationEmail;
use App\Listeners\Emails\BookingDepositEmail;
use App\Listeners\Emails\BookingEventVirtualWithDepositEmail;
use App\Listeners\Emails\BookingReminderEmail;
use App\Listeners\Emails\BookingRescheduleAcceptedByClientEmail;
use App\Listeners\Emails\BookingRescheduleClientToSelectApptEmail;
use App\Listeners\Emails\BookingRescheduleOfferedByPractitionerEmail;
use App\Listeners\Emails\BusinessProfileLiveEmail;
use App\Listeners\Emails\BusinessProfileUnpublishedEmail;
use App\Listeners\Emails\ChangeOfSubscriptionEmail;
use App\Listeners\Emails\ClientRescheduledFyiEmail;
use App\Listeners\Emails\ContractualServiceUpdateDeclinedBookingCancelledEmail;
use App\Listeners\Emails\InstalmentPaymentReminderEmail;
use App\Listeners\Emails\PasswordChangedEmail;
use App\Listeners\Emails\PasswordResetEmail;
use App\Listeners\Emails\PurchaseCancelledByPractitionerEmail;
use App\Listeners\Emails\RescheduleRequestDeclinedByClientEmail;
use App\Listeners\Emails\RescheduleRequestNoReplyFromClientEmail;
use App\Listeners\Emails\ServiceListingLiveEmail;
use App\Listeners\Emails\ServiceScheduleCancelledEmail;
use App\Listeners\Emails\ServiceScheduleLiveEmail;
use App\Listeners\Emails\ServiceUnpublishedEmail;
use App\Listeners\Emails\ServiceUpdatedByPractitionerContractualEmail;
use App\Listeners\Emails\ServiceUpdatedByPractitionerNonContractualEmail;
use App\Listeners\Emails\ServiceUpdatedNonContractualEmail;
use App\Listeners\Emails\SubscriptionConfirmationEmail;
use App\Listeners\Emails\WelcomeVerification;
use App\Listeners\SendServiceNotification;
use App\Services\ServicePurchasedEventHandler;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider {
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UserRegistered::class                                   => [
            WelcomeVerification::class
        ],
        Registered::class                                       => [
            SendEmailVerificationNotification::class,
        ],
        AccountDeleted::class                                   => [
            AccountDeletedEmail::class
        ],
        AccountTerminatedByAdmin::class                         => [
            AccountTerminatedByAdminEmail::class
        ],
        AccountUpgradedToPractitioner::class                    => [
            AccountUpgradedToPractitionerEmail::class
        ],
        ArticlePublished::class                                 => [
            ArticlePublishedEmail::class
        ],
        ArticleUnpublished::class                               => [
            ArticleUnpublishedEmail::class
        ],
        BookingCancelledByPractitioner::class                   => [
            BookingCancelledByPractitionerEmail::class,
        ],
        BookingCancelledToClient::class                         => [
            BookingCancelledToClientEmail::class,
        ],
        BookingCancelledByClient::class                         => [
            BookingCancelledByClientEmail::class,
        ],
        BookingConfirmation::class                              => [
            BookingConfirmationEmail::class
        ],
        BookingDeposit::class                                   => [
            BookingDepositEmail::class
        ],
        BookingEventVirtualWithDeposit::class                   => [
            BookingEventVirtualWithDepositEmail::class
        ],
        BookingReminder::class                                  => [
            BookingReminderEmail::class
        ],
        BookingRescheduleAcceptedByClient::class                => [
            BookingRescheduleAcceptedByClientEmail::class
        ],
        BookingRescheduleClientToSelectAppt::class              => [
            BookingRescheduleClientToSelectApptEmail::class
        ],
        BookingRescheduleOfferedByPractitioner::class           => [
            BookingRescheduleOfferedByPractitionerEmail::class
        ],
        BusinessProfileLive::class                              => [
            BusinessProfileLiveEmail::class
        ],
        BusinessProfileUnpublished::class                       => [
            BusinessProfileUnpublishedEmail::class
        ],
        ChangeOfSubscription::class                             => [
            ChangeOfSubscriptionEmail::class
        ],
        ClientRescheduledFyi::class                             => [
            ClientRescheduledFyiEmail::class
        ],
        ContractualServiceUpdateDeclinedBookingCancelled::class => [
            ContractualServiceUpdateDeclinedBookingCancelledEmail::class
        ],
        InstalmentPaymentReminder::class                        => [
            InstalmentPaymentReminderEmail::class
        ],
        PasswordChanged::class                                  => [
            PasswordChangedEmail::class
        ],
        PasswordReset::class                                    => [
            PasswordResetEmail::class
        ],
        PurchaseCancelledByPractitioner::class                  => [
            PurchaseCancelledByPractitionerEmail::class
        ],
        RescheduleRequestDeclinedByClient::class                => [
            RescheduleRequestDeclinedByClientEmail::class
        ],
        RescheduleRequestNoReplyFromClient::class               => [
            RescheduleRequestNoReplyFromClientEmail::class
        ],
        ServiceListingLive::class                               => [
            ServiceListingLiveEmail::class
        ],
        ServiceScheduleCancelled::class                         => [
            ServiceScheduleCancelledEmail::class
        ],
        ServiceScheduleLive::class                              => [
            ServiceScheduleLiveEmail::class
        ],
        ServiceUnpublished::class                               => [
            ServiceUnpublishedEmail::class
        ],
        ServiceUpdated::class => [
            SendServiceNotification::class,
        ],
        ServiceUpdatedNonContractual::class                     => [
            ServiceUpdatedNonContractualEmail::class
        ],
        ServiceUpdatedByPractitionerContractual::class          => [
            ServiceUpdatedByPractitionerContractualEmail::class
        ],
        ServiceUpdatedByPractitionerNonContractual::class       => [
            ServiceUpdatedByPractitionerNonContractualEmail::class
        ],

        SubscriptionConfirmation::class => [
            SubscriptionConfirmationEmail::class
        ],

        AppointmentBooked::class => [
            AppointmentBookedEventHandler::class
        ],

        ServicePurchased::class => [
            ServicePurchasedEventHandler::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        parent::boot();

        //
    }
}
