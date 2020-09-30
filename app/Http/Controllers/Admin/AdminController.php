<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateAdminFromRequest;
use App\Actions\Admin\UpdateAdminFromRequest;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Actions\User\CreateUserFromRequest;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminDestroyRequest;
use App\Http\Requests\Admin\AdminShowRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Request;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\StripeClient;

class AdminController extends Controller
{
    public function index(Request $request)
    {

        $paginator = User::where('is_admin', true)->paginate($request->getLimit());
        $user = $paginator->getCollection();
        return response(fractal($user, new UserTransformer())->parseIncludes($request->getIncludes()))
            ->withPaginationHeaders($paginator);
    }

    public function indexProfile(Request $request)
    {
        $admin = User::where('id', Auth::id())->get();
        return response(fractal($admin, new UserTransformer())->parseIncludes($request->getIncludes()));
    }

    public function store(RegisterRequest $request)
    {
        $customer = run_action(CreateStripeUserByEmail::class, $request->email);
        $user = run_action(CreateAdminFromRequest::class, $request, ['stripe_id' => $customer->id]);

        $token = $user->createToken('access-token');
        $user->withAccessToken($token);

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    public function show(User $admin, AdminShowRequest $request)
    {

        return response(fractal($admin, new UserTransformer())->parseIncludes($request->getIncludes()));
    }

    public function update(AdminUpdateRequest $request)
    {
       run_action(UpdateAdminFromRequest::class,$request);
    }

    public function destroy(User $admin, AdminDestroyRequest $request)
    {
        $admin->delete();
        return response(null, 204);
    }
}
