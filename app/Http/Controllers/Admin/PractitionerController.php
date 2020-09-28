<?php

namespace App\Http\Controllers\Admin;

use App\Events\AccountDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PractitionerDestroyRequest;
use App\Http\Requests\Admin\PractitionerShowRequest;
use App\Http\Requests\Admin\PractitionerUpdateRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Request;
use App\Mail\VerifyEmail;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\StripeClient;

class PractitionerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginator = User::where('account_type', 'practitioner')->paginate($request->getLimit());
        $practitioner = $paginator->getCollection();
        return fractal($practitioner, new UserTransformer())->parseIncludes($request->getIncludes())
            ->withPaginationHeaders($paginator);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request)
    {
        $user = new User();
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $customer = $stripe->customers->create([
            'email' => $request->get('email'),
        ]);

        $user->forceFill([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'stripe_id' => $customer->id,
            'is_admin' => null,
            'account_type' => 'practitioner'
        ]);
        $user->save();

        $token = $user->createToken('access-token');
        $user->withAccessToken($token);

        $this->sendVerificationEmail($user);

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    protected function sendVerificationEmail($user)
    {
        $linkApi = URL::temporarySignedRoute('verify-email', now()->addMinute(60), [
            'user' => $user->id,
            'email' => $user->email
        ]);

        $linkFrontend = config('app.frontend_password_reset_link') . '?' . explode('?', $linkApi)[1];

        Mail::to([
            'email' => $user->email
        ])->send(new VerifyEmail($linkFrontend));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $practitioner, PractitionerShowRequest $request)
    {
        return fractal($practitioner, new UserTransformer())->parseIncludes($request->getIncludes())
            ->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(PractitionerUpdateRequest $request, User $practitioner)
    {
        $practitioner->update($request->all());
        return fractal($practitioner, new UserTransformer())->respond();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $practitioner, PractitionerDestroyRequest $request)
    {
        $practitioner->delete();
        event(new AccountDeleted($practitioner));
        return response(null, 204);
    }
}
