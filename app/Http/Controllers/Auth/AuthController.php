<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\GetUsersPermissions;
use App\Actions\Practitioners\UnpublishPractitioner;
use App\Actions\Practitioners\UpdateMediaPractitioner;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Actions\User\CreateUserFromRequest;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PublishPractitionerRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UnpublishPractitionerRequest;
use App\Http\Requests\Auth\UpdateBusinessRequest;
use App\Http\Requests\Auth\UpdateMediaRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Http\Requests\Auth\VerificationRequest;
use App\Models\Country;
use App\Models\Keyword;
use App\Models\User;
use App\Traits\hasMediaItems;
use App\Transformers\UserTransformer;
use DB;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\Account;
use Stripe\StripeClient;


class AuthController extends Controller {
    use hasMediaItems;

    public function register(RegisterRequest $request, StripeClient $stripe) {
        try {

            $stripeCustomer = run_action(CreateStripeUserByEmail::class, $request->email);

            $user = run_action(CreateUserFromRequest::class, $request, [
                'stripe_customer_id' => $stripeCustomer->id
            ]);

        } catch (\Exception $e) {

            Log::channel('stripe_client_error')->info("Client could not registered in stripe", [
                'first_name'         => $request->first_name,
                'last_name'          => $request->last_name,
                'business_email'     => $request->business_email,
                'email'              => $request->email,
                'stripe_customer_id' => $stripeCustomer->id ?? null,
                'message'            => $e->getMessage(),
            ]);

            return abort(500);
        }

        Log::channel('stripe_client_success')->info("Client registered in stripe", [
            'user_id'            => $user->id,
            'first_name'         => $request->first_name,
            'last_name'          => $request->last_name,
            'business_email'     => $request->business_email,
            'email'              => $request->email,
            'stripe_customer_id' => $stripeCustomer->id,
        ]);

        event(new UserRegistered($user));

        return fractal($user, new UserTransformer())->respond();
    }

    public function login(LoginRequest $request) {
        $user = User::where('email', $request->get('email'))->first();

        $permissions = run_action(GetUsersPermissions::class, $user);
        $user->withAccessToken($user->createToken('access-token', $permissions));

        return fractal($user, new UserTransformer())->parseIncludes('access_token')->respond();
    }

    public function profile(Request $request) {
        return fractal($request->user(), new UserTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    // update simple details
    public function update(UpdateRequest $request, StripeClient $stripe) {
        $user = $request->user();

        if ($user->email !== $request->get('email')) {
            $stripe->customers->update($user->stripe_customer_id, ['email' => $request->get('email')]);
        }

        $user->update($request->all($request->getValidatorKeys()));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
        }

        return fractal($user, new UserTransformer())->respond();
    }

    //update practitioner business details
    public function updateBusiness(UpdateBusinessRequest $request, StripeClient $stripe) {
        $user = $request->user();

        $requestData = $request->all($request->getValidatorKeys());

        $isPublished = $request->getBoolFromRequest('is_published');
        if ($isPublished !== null) {
            $user->is_published = $isPublished;
        }

        //first filled business country
        if (!$user->business_country_id && !$user->stripe_account_id) {
            try {
                $country = Country::findOrFail((int)$request->get('business_country_id'));
                $stripeAccount = $stripe->accounts->create([
                                                               'country'      => $country->iso,
                                                               'type'         => Account::TYPE_CUSTOM,
                                                               'capabilities' => [
                                                                   Account::CAPABILITY_CARD_PAYMENTS => [
                                                                       'requested' => true,
                                                                   ],
                                                                   Account::CAPABILITY_TRANSFERS     => [
                                                                       'requested' => true,
                                                                   ]
                                                               ],
                                                               'email'        => $user->email,
                                                           ]);
                $user->stripe_account_id = $stripeAccount->id;
                $user->business_published_at = now();
                Log::channel('stripe_client_success')->info("New account has been registered in stripe", [
                    'user_id'           => $user->id,
                    'email'             => $user->email,
                    'stripe_account_id' => $user->stripe_account_id,
                ]);

            } catch (\Exception $e) {
                Log::channel('stripe_client_error')->info("New Account could not registered in stripe", [
                    'user_id'    => $user->id,
                    'email'      => $user->email,
                    'message'    => $e->getMessage(),
                    'country_id' => (int)$request->get('business_country_id')
                ]);
                return abort(500, $e->getMessage());
            }
        }

        $user->update($requestData);

        return fractal($user, new UserTransformer())->respond();
    }

    //update practitioner media and profile info
    public function updateMedia(UpdateMediaRequest $request) {
        $user = $request->user();
        run_action(UpdateMediaPractitioner::class, $user, $request);
        return fractal($user, new UserTransformer())->respond();
    }

    public function avatar(Request $request) {
        $path = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('avatar')->move($path, $fileName);
    }

    public function background(Request $request) {
        $path = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('background')->move($path, $fileName);
    }

    public function verifyEmail(Request $request) {
        if (!$request->hasValidSignature() || !$request->user || !$request->email) {
            abort(401);
        }

        $user = User::where('id', $request->user)->where('email', $request->email)->firstOrFail();

        $user->forceFill(['email_verified_at' => now(), 'status' => User::STATUS_ACTIVE]);
        $user->save();

        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())->parseIncludes('access_token')->respond();
    }

    public function resendVerification(VerificationRequest $request) {
        event(new UserRegistered(User::where('email', $request->email)->first()));
        return response(null, 204);
    }

    protected function invalidate() {
        throw ValidationException::withMessages([
                                                    'email' => ['The provided credentials are incorrect']
                                                ]);
    }

    public function delete(Request $request) {
        $request->user()->delete();

        return response(null, 204);
    }

    public function show($slug, Request $request) {
        $user = User::where('slug', $slug)->with($request->getIncludes())->firstOrFail();

        return fractal($user, new UserTransformer())->parseIncludes($request->getIncludes())->respond();
    }

    public function publish(PublishPractitionerRequest $request) {
        Auth::user()->is_published = true;
        Auth::user()->save();
        return response(null, 204);
    }

    public function unpublish(UnpublishPractitionerRequest $request) {
        run_action(UnpublishPractitioner::class, Auth::user(), $request);
        return response(null, 204);
    }

}
