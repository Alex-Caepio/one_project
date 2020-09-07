<?php


namespace App\Actions\Admin;


use App\Actions\User\CreateUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateAdminFromRequest
{
    public static $safeFields = ['first_name', 'last_name', 'email', 'password','is_admin','account_type'];

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
