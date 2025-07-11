<?php

namespace VnCoder\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use VnCoder\Models\VnUser;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $checkLogin = VnUser::isLogin();
        if (!$checkLogin) {
            return redirect()->route('auth.login');
        }
        return $next($request);
    }
}
