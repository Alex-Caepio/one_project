<?php

namespace App\Actions\User;

use App\Models\User;

class CreateUser {
    public function execute(array $attributes): User {
        $user = new User();
        $user->forceFill($attributes);
        $user->accepted_client_agreement = true;
        if ($user->isPractitioner()) {
            $user->accepted_practitioner_agreement = true;
        }
        $user->save();

        return $user;
    }
}
