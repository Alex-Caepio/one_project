<?php

namespace App\Actions\User;

use App\Models\User;

class CreateUser {
    public function execute(array $attributes): User {
        $user = new User();
        $user->forceFill($attributes);
        if ($user->isPractitioner()) {
            $user->accepted_practitioner_agreement = true;
        } else {
            $user->accepted_client_agreement = true;
        }
        $user->save();

        return $user;
    }
}
