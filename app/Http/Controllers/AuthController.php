<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function createLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $rateLimitKey = 'login:'.hash('sha256', Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            abort(429, 'Too many login attempts. Please try again later.');
        }

        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($rateLimitKey, 60);

            throw ValidationException::withMessages([
                'email' => 'The email or password is incorrect, or this account is inactive.',
            ]);
        }

        RateLimiter::clear($rateLimitKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function createRegister(): View
    {
        return view('auth.register', [
            'roles' => [
                'learner' => 'Learner',
                'parents' => 'Parents',
                'teacher' => 'Teacher',
            ],
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $rateLimitKey = 'register:'.hash('sha256', (string) $request->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            abort(429, 'Too many registration attempts. Please try again later.');
        }

        RateLimiter::hit($rateLimitKey, 60);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['learner', 'parents', 'teacher'])],
            'password' => ['required', 'confirmed', Password::min(10)->letters()->numbers()],
        ]);

        $validated['is_active'] = true;

        $user = User::create($validated);

        RateLimiter::clear($rateLimitKey);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dashboard.guest');
    }
}
