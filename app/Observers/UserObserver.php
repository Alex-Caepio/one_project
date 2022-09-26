<?php

namespace App\Observers;

use App\Actions\Practitioners\DeletePractitioner;
use App\Events\AccountDeleted;
use App\Events\AccountTerminatedByAdmin;
use App\Events\BusinessProfileLive;
use App\Events\BusinessProfileUnpublished;
use App\Helpers\UserRightsHelper;
use App\Models\RescheduleRequest;
use App\Models\ScheduleFreeze;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    /**
     * Mark all of the articles and services.
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
                } elseif ($user->is_published) {
                    $user->published_at = now();
                    event(new BusinessProfileLive($user));
                }
            }
        }
    }

    /**
     * Deletes left data.
     */
    public function deleting(User $user): void
    {
        RescheduleRequest::where('user_id', $user->id)->delete();
        ScheduleFreeze::where('user_id', $user->id)->delete();

        if ($user->account_type === User::ACCOUNT_PRACTITIONER) {
            run_action(DeletePractitioner::class, $user, '');
        }

        if (!Auth::user()->is_admin) {
            event(new AccountDeleted($user));
        } else {
            event(new AccountTerminatedByAdmin($user));
        }
    }
}
