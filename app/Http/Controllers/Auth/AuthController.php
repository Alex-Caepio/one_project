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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Stripe\StripeClient;


class AuthController extends Controller
{
    public function register(RegisterRequest $request, StripeClient $stripe)
    {
        $stripeCustomer = run_action(CreateStripeUserByEmail::class, $request->email);
        $stripeAccount  = $stripe->accounts->create([
            'type'  => 'standard',
            'email' => $request->email,
        ]);
        $user           = run_action(CreateUserFromRequest::class, $request, [
            'stripe_customer_id' => $stripeCustomer->id,
            'stripe_account_id'  => $stripeAccount->id
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
            ->respond();
    }

    public function update(UpdateRequest $request)
    {
        $user = $request->user();
        if ($request->filled('media_images'))
        {
            foreach ($request->media_images as $media_image)
            {
                if (Storage::disk(config('image.image_storage'))->missing(file_get_contents($media_image['url'])))
                {
                    $image = Storage::disk(config('image.image_storage'))
                        ->put("/images/users/{$user->id}/media_images/", file_get_contents($media_image['url']));
                    $media_image[] = Storage::url($image);
                }
            }
            $request->media_images = $media_image;
        }
        $user->update($request->all());
        if ($request->filled('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
            event(new PasswordChanged($user));
        }

        if ($request->filled('disciplines')) {
            if(!User::with('disciplines')->where('id', $request->disciplines)->get()) {
                $user->disciplines()->sync($request->get('disciplines'));
            }
        }

        if ($request->filled('focus_areas')) {
            if(!User::with('focus_areas')->where('id', $request->focus_areas)) {
                $user->focus_areas()->sync($request->get('focus_areas'));
            }
        }
        if ($request->filled('service_types')) {
            foreach ($request->service_types as $service_type) {
                if (!User::with('service_types')->where('id', $service_type)->get()) {
                    $user->service_types()->save($request->get($service_type));
                }
            }
        }
        if ($request->filled('keywords')) {
            $keywordsId = Keyword::whereIn('title', $request->keywords)->pluck('id');
            $user->keywords()->sync($keywordsId);
        }
        if ($request->has('media_images')){
            $user->media_images()->whereNotIn('url', $request->media_videos)->delete();
            $user->media_images()->createMany($request->get('media_images'));
        }
        if ($request->has('media_videos')) {
            $user->media_videos()->whereNotIn('url', $request->media_videos)->delete();
            $user->media_videos()->createMany($request->get('media_videos'));
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
