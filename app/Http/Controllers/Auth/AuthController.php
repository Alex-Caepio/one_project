<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Stripe\CreateStripeUserByEmail;
use App\Actions\User\CreateUserFromRequest;
use App\Events\PasswordChanged;
use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PublishRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateRequest;
use App\Models\Keyword;
use App\Models\MediaVideo;
use App\Models\Schedule;
use App\Models\User;
use App\Transformers\UserTransformer;
use DB;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\StripeClient;


class AuthController extends Controller
{
    public function register(RegisterRequest $request, StripeClient $stripe)
    {
        try {

            $stripeCustomer = run_action(CreateStripeUserByEmail::class, $request->email);
            $stripeAccount = $stripe->accounts->create([
                'type' => 'standard',
                'email' => $request->email,
            ]);
            $user = run_action(CreateUserFromRequest::class, $request, [
                'stripe_customer_id' => $stripeCustomer->id,
                'stripe_account_id' => $stripeAccount->id
            ]);

        } catch (\Stripe\Exception\ApiErrorException $e) {

            Log::channel('stripe_client_error')->info("Client could not registered in stripe", [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'business_email' => $request->business_email,
                'email' => $request->email,
                'stripe_customer_id' => $stripeCustomer->id,
                'stripe_account_id'  => $stripeAccount->id,
            ]);

             return abort(500);
        }

        Log::channel('stripe_client_success')->info("Client registered in stripe", [
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'business_email' => $request->business_email,
            'email' => $request->email,
            'stripe_customer_id' => $stripeCustomer->id,
            'stripe_account_id'  => $stripeAccount->id,
        ]);

        event(new UserRegistered($user));

        return fractal($user, new UserTransformer())
            ->respond();
    }


    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->get('email'))->first();
        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    public function publish(PublishRequest $request)
    {
        $user               = $request->user();
        $user->is_published = true;
        $user->save();
        return fractal($request->user(), new UserTransformer())
            ->respond();
    }

    public function profile(Request $request)
    {
        return fractal($request->user(), new UserTransformer())
            ->parseIncludes($request->getIncludes())
            ->respond();
    }

    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        if ($request->filled('media_images')&& !empty($request->media_images))
        {
            foreach ($request->media_images as $mediaImage)
            {
                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($mediaImage['url'])))
                {
                    $image = Storage::disk(config('image.image_storage'))
                        ->put("/images/users/{$user->id}/media_images/", file_get_contents($mediaImage['url']));
                    $image_urls[]['url'] = Storage::url($image);
                }
            }
            $request->media_images = $image_urls;
        }
        $user->update($request->all());
        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
            event(new PasswordChanged($user));
        }

        if ($request->filled('disciplines')) {
            $user->disciplines()->sync($request->get('disciplines'));
        }

        if ($request->filled('focus_area')) {
            $user->focus_area()->sync($request->get('focus_area'));
        }

        if ($request->filled('service_types')) {
            $user->service_types()->sync($request->get('service_types'));
        }

        if ($request->filled('keywords')) {
            $keywordsId = Keyword::whereIn('title', $request->keywords)->pluck('id');
            $user->keywords()->sync($keywordsId);
        }

        if ($request->filled('media_images')&&!empty($request->media_images)){
            $user->media_images()->whereNotIn('url', $request->media_images)->delete();
            $urls = collect($request->media_images)->pluck('url');
            $recurringURL = $user->media_images()->whereIn('url', $urls)->pluck('url')->toArray();
            $newImages = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            foreach ($newImages as $url){
                $imageUrlToStore[]['url'] = $url;
            }

            $user->media_images()->createMany($imageUrlToStore);
        }

        if ($request->has('media_videos')) {
            $user->media_videos()->whereNotIn('url', $request->media_videos)->delete();
            $urls = collect($request->media_videos)->pluck('url');
            $recurringURL = $user->media_videos()->whereIn('url', $urls)->pluck('url')->toArray();
            $newVideos = $urls->filter(function($value) use ($recurringURL) {
                return !in_array($value, $recurringURL);
            });

            foreach ($newVideos as $url){
               $videoUrlToStore[]['url'] = $url;
            }

            $user->media_videos()->createMany($videoUrlToStore);
        }
        return fractal($user, new UserTransformer())->respond();
    }

    public function avatar(Request $request)
    {
        $path     = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('avatar')->move($path, $fileName);
    }

    public function background(Request $request)
    {
        $path     = public_path('\img\profile\\' . Auth::id() . '\\');
        $fileName = $request->file('image')->getClientOriginalName();
        $request->file('background')->move($path, $fileName);
    }

    public function verifyEmail(Request $request)
    {
        if (!$request->hasValidSignature() || !$request->user || !$request->email) {
            abort(401);
        }

        $user = User::where('id', $request->user)
            ->where('email', $request->email)
            ->firstOrFail();

        $user->forceFill(['email_verified_at' => now(), 'status' => User::STATUS_ACTIVE]);
        $user->save();

        $user->withAccessToken($user->createToken('access-token'));

        return fractal($user, new UserTransformer())
            ->parseIncludes('access_token')
            ->respond();
    }

    public function resendVerification(Request $request)
    {
        $this->sendVerificationEmail($request->user());
        response(null, 200);
    }

    protected function invalidate()
    {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect']
        ]);
    }

}
