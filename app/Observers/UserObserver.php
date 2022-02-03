<?php

namespace App\Observers;

use App\Actions\Cancellation\CancelBooking;
use App\Events\AccountDeleted;
use App\Events\AccountTerminatedByAdmin;
use App\Events\BusinessProfileLive;
use App\Events\BusinessProfileUnpublished;
use App\Helpers\UserRightsHelper;
use App\Models\Article;
use App\Models\Booking;
use App\Models\RescheduleRequest;
use App\Models\ScheduleFreeze;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class UserObserver
{

    /**
     * Mark all of the articles and services
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function saving(User $user): void
    {
        if ($user->isPractitioner()) {

            if (!$user->business_email) {
                $user->business_email = $user->email;
            }

            if (!$user->business_country_id && $user->country_id) {
                $user->business_country_id = $user->country_id;
            }

            if ($user->isDirty('is_published')) {
                if (!$user->is_published && !$user->wasRecentlyCreated) {
                    event(new BusinessProfileUnpublished($user));
                    UserRightsHelper::unpublishPractitioner($user);
                } elseif ($user->is_published) {
                    $user->published_at = now();
                    event(new BusinessProfileLive($user));
                }
            }
        }
    }


    /**
     * Delete all user articles and services
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function deleting(User $user): void
    {
        if ($user->isPractitioner()) {
            UserRightsHelper::unpublishPractitioner($user, true);

            Article::where('user_id', $user->id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'is_published' => false,
                'published_at' => null
            ]);

            Service::where('user_id', $user->id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'is_published' => false
            ]);
        }
        RescheduleRequest::where('user_id', $user->id)->delete();
        ScheduleFreeze::where('user_id', $user->id)->delete();

        foreach ($user->bookings()->active()->get() as $booking) {
            try {
                run_action(CancelBooking::class, $booking, false, User::ACCOUNT_CLIENT);
            } catch (\Exception $e) {
                Log::channel('practitioner_cancel_error')->info('[[Cancellation on unpublish failed]]: ', [
                    'user_id' => $booking->user_id ?? null,
                    'practitioner_id' => $booking->practitioner_id ?? null,
                    'booking_id' => $booking->id ?? null,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        if (!Auth::user()->is_admin) {
            event(new AccountDeleted($user));
        } else {
            event(new AccountTerminatedByAdmin($user));
        }
    }
}
