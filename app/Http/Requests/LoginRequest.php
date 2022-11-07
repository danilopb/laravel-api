<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password' => 'required|string',
//            'device_name' => 'required',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * @return void
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();
        if (!Auth::attempt($this->getCredentials())) {
            RateLimiter::hit($this->throttleKey(), config('auth.api.seconds_reset_attempts'));
            $message = trans('auth.failed');
            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $notExceedAttempt = RateLimiter::attempt(
            $this->throttleKey(),
            config('auth.api.max_attempts'),
            function() {
                return true;
            },
            config('auth.api.seconds_reset_attempts')
        );

        if ($notExceedAttempt) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey(): string
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }

    protected function getCredentials(): array
    {
        $value = $this->get('email');
        $fieldNameLogin = filter_var($value, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';
        return [
            $fieldNameLogin => $value,
            'password' => $this->get('password'),
        ];
    }
}
