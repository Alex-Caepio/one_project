<?php

namespace App\Http\Middleware;

use App\Models\Plan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return response(null, 403);
        }
        return $next($request);
    }
}
