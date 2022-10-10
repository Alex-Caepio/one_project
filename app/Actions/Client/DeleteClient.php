<?php

namespace App\Actions\Client;

use App\Actions\User\DeleteUser;
use App\Models\User;

class DeleteClient
{
    public function execute(User $user, string $reason): void
    {
        run_action(DeleteUser::class, $user, $reason);
        $user->delete();
        run_action(CancelClientBookings::class, $user);
    }
}
