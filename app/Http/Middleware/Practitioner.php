<?php

namespace App\Http\Middleware;

use App\Models\Plan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Practitioner {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        $loggedUser = Auth::user();
        if (!$loggedUser->isPractitioner() || !$loggedUser->plan instanceof Plan) {
            Log::info('Middleware failed');
            return response(null, 403);
        }
        return $next($request);
    }
}
