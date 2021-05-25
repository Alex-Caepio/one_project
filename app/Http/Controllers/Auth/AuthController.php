<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\GetUsersPermissions;
use App\Actions\Cancellation\CancelBooking;
use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Actions\User\CreateUserFromRequest;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PublishRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Keyword;
use App\Models\User;
use App\Traits\hasMediaItems;
use App\Transformers\UserTransformer;
use DB;
use App\Http\Requests\Request;
use http\Env\Response;
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

        } catch (\Stripe\Exception\ApiErrorException $e) {

            Log::channel('stripe_client_error')->info("Client could not registered in stripe", [
                'first_name'         => $request->first_name,
                'last_name'          => $request->last_name,
                'business_email'     => $request->business_email,
                'email'              => $request->email,
                'stripe_customer_id' => $stripeCustomer->id,
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

    public function update(UpdateRequest $request, StripeClient $stripe) {
        $user = $request->user();

        if ($request->cancel_bookings_on_unpublish && !$request->is_published && !$user->is_published) {
            $bookings = Booking::where('user_id', $user->id)->active()->get();

            foreach ($bookings as $booking) {
                run_action(CancelBooking::class, $booking);
            }
        }


        if ($request->filled('email') && auth()->user()->email !== $request->get('email')) {
            $stripe->customers->update(auth()->user()->stripe_customer_id, ['email' => $request->get('email')]);
        }

        $requestData = $request->all();

        // first filling of STRIPE country
        if (auth()->user()->isPractitioner()) {

            $user->is_published = $request->getBoolFromRequest('is_published') === true;

            if ($request->filled('business_country_id') && !auth()->user()->business_country_id) {
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
                } catch (\Exception $e) {
                    Log::channel('stripe_client_error')->info("New Account could not registered in stripe", [
                        'user_id'    => $user->id,
                        'email'      => $user->email,
                        'message'    => $e->getMessage(),
                        'country_id' => (int)$request->get('business_country_id')
                    ]);
                    return abort(500, $e->getMessage());
                }
                Log::channel('stripe_client_success')->info("New account has been registered in stripe", [
                    'user_id'           => $user->id,
                    'email'             => $request->email,
                    'stripe_account_id' => $user->stripe_account_id,
                ]);
            }
        }

        $user->update($requestData);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
//            event(new PasswordChanged($user));
        }

        if ($request->filled('disciplines')) {
            $user->disciplines()->sync($request->disciplines);
        }

        if ($request->filled('focus_areas')) {
            $user->focus_areas()->sync($request->focus_areas);
        }

        if ($request->filled('service_types')) {
            $user->service_types()->sync($request->service_types);
        }

        if ($request->filled('keywords')) {
            $user->keywords()->whereNotIn('title', $request->keywords)->delete();
            foreach ($request->keywords as $keyword) {
                $ids = Keyword::firstOrCreate(['title' => $keyword])->pluck('id');
                $keywordIds = collect($ids);
            }

            if (isset($keywordIds) && !empty($keywordIds)) {
                $user->keywords()->sync($keywordIds);
            }
        }

        if ($request->filled('media_images')) {
//            foreach ($request->media_images as $mediaImage)
//            {
//                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($mediaImage)))
//                {
//                    $image = Storage::disk(config('image.image_storage'))
//                        ->put("/images/users/{$user->id}/media_images/", file_get_contents($mediaImage));
//                    $image_urls[] = Storage::url($image);
//                }
//            }
//            $request->media_images = $image_urls;
            $this->syncImages($request->media_images, $user);
        }

        if ($request->filled('media_videos')) {
            $this->syncVideos($request->media_videos, $user);
        }

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

    public function resendVerification(Request $request) {
        $this->sendVerificationEmail($request->user());
        response(null, 200);
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

    public function stripeConnected(Request $request) {
        $user = Auth::user();
        if ($user->stripe_account_id && !$user->connected_at) {
            $user->connected_at = now();
            $user->save();
        }

        return response(null, 204);
    }

}
