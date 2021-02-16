<?php

namespace App\Providers;

use App\Events\AccountDeleted;
use App\Events\AccountTerminatedByAdmin;
use App\Events\AccountUpgradedToPractitioner;
use App\Events\ArticlePublished;
use App\Events\ArticleUnpublished;
use App\Events\BookingCancelledByClient;
use App\Events\BookingConfirmation;
use App\Events\BookingEventVirtualWithDeposit;
use App\Events\BookingReminder;
use App\Events\BookingRescheduleAcceptedByClient;
use App\Events\BookingRescheduleClientToSelectAppt;
use App\Events\BookingRescheduleOfferedByPractitionerAppt;
use App\Events\BookingRescheduleOfferedByPractitionerDate;
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
use App\Events\ServiceScheduleCancelled;
use App\Events\ServiceScheduleLive;
use App\Events\ServiceUnpublished;
use App\Events\ServiceUpdatedByPractitionerContractual;
use App\Events\ServiceUpdatedByPractitionerNonContractual;
use App\Events\SubscriptionConfirmation;
use App\Events\UserRegistered;
use App\Listeners\Emails\AccountDeletedEmail;
use App\Listeners\Emails\AccountTerminatedByAdminEmail;
use App\Listeners\Emails\AccountUpgradedToPractitionerEmail;
use App\Listeners\Emails\ArticlePublishedEmail;
use App\Listeners\Emails\ArticleUnpublishedEmail;
use App\Listeners\Emails\BookingCancelledByClientEmail;
use App\Listeners\Emails\BookingConfirmationEmail;
use App\Listeners\Emails\BookingEventVirtualWithDepositEmail;
use App\Listeners\Emails\BookingReminderEmail;
use App\Listeners\Emails\BookingRescheduleAcceptedByClientEmail;
use App\Listeners\Emails\BookingRescheduleClientToSelectApptEmail;
use App\Listeners\Emails\BookingRescheduleOfferedByPractitionerApptEmail;
use App\Listeners\Emails\BookingRescheduleOfferedByPractitionerDateEmail;
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
use App\Listeners\Emails\SubscriptionConfirmationEmail;
use App\Listeners\Emails\WelcomeVerification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider {
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class                                       => [
            SendEmailVerificationNotification::class,
        ],
        UserRegistered::class                                   => [
            WelcomeVerification::class
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
        BookingCancelledByClient::class                         => [
            BookingCancelledByClientEmail::class,
        ],
        BookingConfirmation::class                              => [
            BookingConfirmationEmail::class
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
        BookingRescheduleOfferedByPractitionerAppt::class       => [
            BookingRescheduleOfferedByPractitionerApptEmail::class
        ],
        BookingRescheduleOfferedByPractitionerDate::class       => [
            BookingRescheduleOfferedByPractitionerDateEmail::class
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
        ServiceUpdatedByPractitionerContractual::class          => [
            ServiceUpdatedByPractitionerContractualEmail::class
        ],
        ServiceUpdatedByPractitionerNonContractual::class       => [
            ServiceUpdatedByPractitionerNonContractualEmail::class
        ],

        SubscriptionConfirmation::class => [
            SubscriptionConfirmationEmail::class
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
