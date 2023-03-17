<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;

class EmailAuthenticate extends Authenticate {

    /**
     * Handle an unauthenticated user.
     *
     * @param \App\Http\Requests\Request $request
     * @param array $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards): void {
        throw new AuthenticationException('You are not allowed to perform this action');
    }

}
