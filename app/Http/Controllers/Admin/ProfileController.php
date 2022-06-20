<?php

namespace App\Http\Controllers\Admin;

use App\Events\PasswordChanged;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\Http\Requests\Admin\AdminUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        return fractal(Auth::user(), new UserTransformer())->respond();
    }

    public function update(AdminUpdateRequest $request)
    {
        $user = $request->user();
        $user->forceFill($request->safeOnly());
        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
            event(new PasswordChanged($user));
        }
        $user->save();
        return fractal($user, new UserTransformer())->respond();
    }
}
