<?php


namespace App\Traits;


use App\Http\Guard\EmailTokenGuard;
use App\Models\User;

trait RescheduleEmailLinks {

    protected function generatePersonalAccessToken(User $user, string $actionType = 'accept'): string {
        return $user->createToken(EmailTokenGuard::EMAIL_TOKEN_NAME, ['reschedule_request:'.$actionType])->plainTextToken;
    }

}
