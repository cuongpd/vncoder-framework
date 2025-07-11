<?php

namespace VnCoder\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use VnCoder\Backend\Models\Admin;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $checkLogin = Admin::isLogin();
        if (!$checkLogin) {
            return redirect()->route('backend.login');
        }
        return $next($request);
    }
}
