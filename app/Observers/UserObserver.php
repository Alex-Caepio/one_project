<?php
namespace App\Observers;

use App\Models\Article;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserObserver {

    /**
     * Mark all of the articles and services
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function saving(User $user): void {
        if ($user->isPractitioner() && $user->isDirty('is_published') && !$user->is_published) {
            Article::where('user_id', $user->id)->published()->update(['is_published' => false, 'published_at' => null]);
            Service::where('user_id', $user->id)->published()->update(['is_published' => false]);
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
            Article::where('user_id', $user->id)->update(['deleted_at' => date('Y-m-d H:i:s'), 'is_published' => false, 'published_at' => null]);
            Service::where('user_id', $user->id)->update(['deleted_at' => date('Y-m-d H:i:s'), 'is_published' => false]);
        }
    }
}
