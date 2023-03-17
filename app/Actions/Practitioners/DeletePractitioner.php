<?php

namespace App\Actions\Practitioners;

use App\Actions\Article\DeleteArticles;
use App\Actions\Client\CancelClientBookings;
use App\Actions\Plan\CancelSubscription;
use App\Actions\Service\DeleteServices;
use App\Actions\User\DeleteUser;
use App\Models\User;

/**
 * Deletes a practitioner and his related entities, cancels his bookings and subscription.
 */
class DeletePractitioner
{
    public function execute(User $user, string $reason): void
    {
        run_action(DeleteUser::class, $user, $reason);
        run_action(CancelClientBookings::class, $user);
        run_action(CancelPractitionerBookings::class, $user);
        run_action(DeleteArticles::class, $user);
        run_action(DeleteServices::class, $user);
        run_action(CancelSubscription::class, $user);
    }
}
