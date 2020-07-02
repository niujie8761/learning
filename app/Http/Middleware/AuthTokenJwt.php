<?php
namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthTokenJwt
{
    public function handle($request, $next, $guard = null)
    {
        if ($guard) {
            $request->guard = $guard;
        }
        $user = $guard ? Auth::guard($guard)->user() : Auth::user();

        $id = $user->id;

        return $next($request);
    }
}
