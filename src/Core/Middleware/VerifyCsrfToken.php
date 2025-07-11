<?php

namespace VnCoder\Core\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerifyCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        // Check if the request is a GET request or if it's a safe method (like HEAD or OPTIONS)
        if ($request->method() !== 'GET' && !in_array($request->method(), ['HEAD', 'OPTIONS'])) {
            // Validate CSRF token
            $token = $request->input('__token') ?: $request->header('X-CSRF-TOKEN');
            if (!$token || !Str::equals($token, $request->session()->token())) {
                // CSRF token mismatch
                return response('CSRF token mismatch.', 400);
            }
        }

        return $next($request);
    }
}
