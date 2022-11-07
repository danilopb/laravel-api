<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckLimitHits
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (\Auth::check()) {
            $auth = auth()->user();
            $notExceedAttempt = RateLimiter::attempt($this->throttleKey($auth->email), config('auth.api.max_attempts'), function() {
                return true;
            }, config('auth.api.seconds_reset_attempts'));

            if (!$notExceedAttempt) {
                $seconds = RateLimiter::availableIn($this->throttleKey($auth));
                $message = trans('auth.throttle', ['seconds' => $seconds]);
                return response()->json(['message' => $message], 429);
            }
        }
        return $next($request);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param string $email
     * @return string
     */
    public function throttleKey(string $email): string
    {
        return Str::lower($email).'|'.request()->ip();
    }
}
