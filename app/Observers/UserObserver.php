<?php

namespace App\Observers;

use App\Events\AccountDeleted;
use App\Events\AccountTerminatedByAdmin;
use App\Events\BusinessProfileLive;
use App\Events\BusinessProfileUnpublished;
use App\Models\Article;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserObserver {

    /**
     * Mark all of the articles and services
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function saving(User $user): void {
        if ($user->isPractitioner() && $user->isDirty('is_published')) {
            if (!$user->is_published && !$user->wasRecentlyCreated) {
                Article::where('user_id', $user->id)->published()->update([
                                                                              'is_published' => false,
                                                                              'published_at' => null
                                                                          ]);
                Service::where('user_id', $user->id)->published()->update(['is_published' => false]);
                event(new BusinessProfileUnpublished($user));
            } elseif ($user->is_published) {
                event(new BusinessProfileLive($user));
            }
        }
    }


    /**
     * Delete all user articles and services
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function deleted(User $user): void {
        if ($user->isPractitioner()) {
            Article::where('user_id', $user->id)->update([
                                                             'deleted_at'   => date('Y-m-d H:i:s'),
                                                             'is_published' => false,
                                                             'published_at' => null
                                                         ]);
            Service::where('user_id', $user->id)->update([
                                                             'deleted_at'   => date('Y-m-d H:i:s'),
                                                             'is_published' => false
                                                         ]);
        }
        if (Auth::user()->is_admin !== true) {
            event(new AccountDeleted($user));
        } else {
            event(new AccountTerminatedByAdmin($user));
        }
    }
}
