<?php

namespace App\Actions\User;

use App\Models\User;

class CreateUser
{
    public function execute(array $attributes): User
    {
        $user = new User();
        $user->forceFill($attributes);
        $user->save();

        return $user;
    }
}
