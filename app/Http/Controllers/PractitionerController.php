<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\Request;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;


class PractitionerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('account_type', User::ACCOUNT_PRACTITIONER);

        if ($request->filled('discipline_id')) {
            $query->whereHas('disciplines', function($q) use ($request){
                $q->where('discipline_id', $request->discipline_id);
            });
        }

        if ($request->filled('is_published')) {
            $query->where('is_published', $request->getBoolFromRequest('is_published'));
        }

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('business_city', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%");
                }
            );
        }

        $loggedInUser = $request->user('sanctum');
        if ($request->getBoolFromRequest('upcoming') && $loggedInUser) {
            $query->whereHas('practitioner_bookings', static function($subQuery) use($loggedInUser) {
                $subQuery->where('bookings.user_id', $loggedInUser->id);
            });
        }

        $paginator = $query->paginate($request->getLimit());
        $user = $paginator->getCollection();

        return response(fractal($user, new UserTransformer())->parseIncludes($request->getIncludes()))
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
