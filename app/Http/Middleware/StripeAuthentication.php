<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StripeAuthentication {
    /**
     * Handle an incoming stripe request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if ($request->hasHeader('Stripe-Signature')) {
            Log::info('ENV: '.config('services.stripe.hook_sign'));
            $timestamp = $signature = null;
            foreach (explode(',', $request->header('Stripe-Signature')) as $part) {
                [$key, $value] = explode('=', $part);
                if ($key === 'v1')  {
                    $signature = $value;
                } elseif  ($key === 't') {
                    $timestamp = $value;
                }
            }
            if ($timestamp && $signature) {
                $content = $timestamp.'.'.$request->getContent();
                $signedPayload = hash_hmac('sha256', $content, config('services.stripe.hook_sign'));
                Log::info('Signed payload: '.$signedPayload);
                Log::info('Signature: '.$signature);
                if ($signedPayload === $signature) {
                    return $next($request);
                }
            }
        }
        return response(null, 403);
    }
}
