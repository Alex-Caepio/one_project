<?php

namespace App\Actions\User;

use App\Models\User;

/**
 * Deletes a user and cancels his bookings.
 */
class DeleteUser
{
    public function execute(User $user, string $reason): void
    {
        $user->update(['termination_message' => $reason]);
        $user->delete();
    }
}
