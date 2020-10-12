<?php

namespace App\Actions\User;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateUserFromRequest
{
    public static $safeFields = ['first_name', 'last_name', 'email', 'password','account_type', 'emails_holistify_update'];

    public function execute(Request $request, array $overrides): User
    {
        $attributes = $request->only(self::$safeFields);
        if($attributes['password']){
            $attributes['password'] = Hash::make($attributes['password']);
        }

        $attributes = array_merge($attributes, $overrides);

        return run_action(CreateUser::class, $attributes);
    }
}
