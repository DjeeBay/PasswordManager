<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorEnabledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user->hasTwoFactorEnabled() && !$this->recentlyConfirmed($request)) {
            return $request->expectsJson()
                ? response()->json(['message' => trans('two-factor::messages.required')], 403)
                : redirect()->back();
        }

        return $next($request);
    }

    protected function recentlyConfirmed(Request $request): bool
    {
        $key = config('two-factor.confirm.key');

        return $request->session()->get("$key.confirm.expires_at") >= now()->getTimestamp();
    }
}
