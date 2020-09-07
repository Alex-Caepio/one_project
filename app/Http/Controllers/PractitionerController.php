<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;


class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $user = User::where('account_type', 'practitioner')->get();
        return fractal($user, new UserTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    public function list()
    {
        $user = User::where('account_type', 'practitioner');
        return $user->selectRaw('concat(first_name, " ", last_name) username, id')->pluck('username', 'id');
    }

    public function storeFavorite(User $user)
    {
        Auth::user()->favourite_practitioners()->attach($user->id);
        return response(null, 201);
    }

    public function deleteFavorite(User $user)
    {
        Auth::user()->favourite_practitioners()->detach($user->id);
        return response(null, 204);
    }

}
