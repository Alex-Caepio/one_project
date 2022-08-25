<?php

namespace App\Actions\Practitioners;

use App\Actions\Article\UnpublishArticles;
use App\Actions\Service\UnpublishServices;
use App\Models\User;

/**
 * Unpublishes practitioner and his articles, services. It it is necessary then cancels his bookings.
 */
class UnpublishPractitioner
{
    public function execute(User $user, bool $cancelBookings): void
    {
        $user->is_published = false;
        $user->save();

        run_action(UnpublishArticles::class, $user);
        run_action(UnpublishServices::class, $user);

        if ($cancelBookings) {
            run_action(CancelPractitionerBookings::class, $user);
        }
    }
}
