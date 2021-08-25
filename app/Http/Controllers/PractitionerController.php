<?php

namespace App\Http\Controllers;

use App\Filters\UserFiltrator;
use App\Models\User;
use App\Http\Requests\Request;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;


class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $practitioners = User::query()->where('account_type', User::ACCOUNT_PRACTITIONER);

        $userFiltrator = new UserFiltrator();
        $userFiltrator->apply($practitioners, $request, true);


        $loggedInUser = $request->user('sanctum');
        if ($request->getBoolFromRequest('upcoming') && $loggedInUser) {
            $practitioners->whereHas('practitioner_bookings', static function($subQuery) use($loggedInUser) {
                $subQuery->where('bookings.user_id', $loggedInUser->id);
            });
        }

        $includes = $request->getIncludes();
        $practitioners->with($includes);
        $paginator = $practitioners->paginate($request->getLimit());
        $user = $paginator->getCollection();

        return response(fractal($user, new UserTransformer())->parseIncludes($includes))
            ->withPaginationHeaders($paginator);
    }

    public function list()
    {
        $user = User::where('account_type', User::ACCOUNT_PRACTITIONER);
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
