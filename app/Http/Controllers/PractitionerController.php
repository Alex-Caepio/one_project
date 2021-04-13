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
        $query = User::query()->where('account_type', 'practitioner');

        if ($request->filled('discipline_id')) {
            $query->whereHas('disciplines', function($q) use ($request){
                $q->where('discipline_id', $request->discipline_id);
            });
        }

        if ($request->hasSearch()) {
            $search = $request->search();

            $query->where(
                function ($query) use ($search) {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('business_name', 'like', "%{$search}%");
                }
            );
        }

        $paginator = $query->paginate($request->getLimit());
        $user = $paginator->getCollection();;

        return response(fractal($user, new UserTransformer())->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
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
