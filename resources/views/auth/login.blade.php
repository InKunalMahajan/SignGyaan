@extends('layouts.app')

@section('title', 'Sign In - SignGyaan')
@section('description', 'Sign in to SignGyaan to continue your visual and ISL-supported learning journey.')

@section('content')
    <section class="min-h-[calc(100vh-5rem)] bg-sign-soft py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="mx-auto grid max-w-6xl overflow-hidden rounded-3xl border border-sign-border bg-white shadow-sm lg:grid-cols-[0.9fr_1.1fr]">

                {{-- Learning Welcome Panel --}}
                <aside class="relative overflow-hidden bg-sign-dark px-6 py-10 text-white sm:px-10 sm:py-12 lg:flex lg:min-h-[42rem] lg:flex-col lg:justify-between lg:px-12 lg:py-14">
                    <div class="relative z-10">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 rounded-xl" aria-label="SignGyaan home">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white font-heading text-lg font-bold text-sign-primary" aria-hidden="true">S</span>
                            <span class="font-heading text-2xl font-semibold">SignGyaan</span>
                        </a>

                        <p class="mt-10 text-sm font-semibold uppercase tracking-wider text-sign-cyan">Welcome back</p>
                        <h1 class="mt-3 max-w-xl font-heading text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl">
                            Continue learning at your own pace.
                        </h1>
                        <p class="mt-5 max-w-lg text-sm leading-7 text-white/75 sm:text-base">
                            Sign in to return to your courses, continue lessons and keep your learning journey organised in one place.
                        </p>
                    </div>

                    <div class="relative z-10 mt-10 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-sign-cyan" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold">Visual learning</p>
                                <p class="mt-1 text-xs leading-5 text-white/65">Clear explanations built for understanding.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-sign-cyan" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold">Step-by-step lessons</p>
                                <p class="mt-1 text-xs leading-5 text-white/65">Move through structured units with confidence.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-sign-cyan" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25A8.966 8.966 0 0 1 18 3.75c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold">ISL-supported</p>
                                <p class="mt-1 text-xs leading-5 text-white/65">Learn with visual and sign-language support.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pointer-events-none absolute -bottom-28 -right-20 h-72 w-72 rounded-full bg-sign-cyan/10" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute -left-16 top-40 h-40 w-40 rounded-full border border-white/10" aria-hidden="true"></div>
                </aside>

                {{-- Sign In Form --}}
                <div class="flex items-center px-5 py-10 sm:px-10 sm:py-12 lg:px-14 lg:py-16">
                    <div class="mx-auto w-full max-w-lg">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your account</p>
                            <h2 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl">Sign in to continue</h2>
                            <p class="mt-3 text-sm leading-6 text-sign-muted sm:text-base">
                                Enter your account details to return to SignGyaan.
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="mt-6 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-medium text-sign-primary" role="status">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
                                <p class="text-sm font-semibold text-red-800">Please check your sign-in details.</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5" novalidate>
                            @csrf

                            <div>
                                <label for="email" class="mb-2 block text-sm font-semibold text-sign-primary">Email address</label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sign-muted" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                    </span>
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ old('email') }}"
                                        autocomplete="email"
                                        inputmode="email"
                                        required
                                        autofocus
                                        aria-describedby="email-help @error('email') email-error @enderror"
                                        @class([
                                            'min-h-12 w-full rounded-xl border bg-white py-3 pl-12 pr-4 text-base text-sign-text outline-none transition placeholder:text-sign-muted/60 focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                                            'border-red-300' => $errors->has('email'),
                                            'border-sign-border' => ! $errors->has('email'),
                                        ])
                                        placeholder="you@example.com"
                                    >
                                </div>
                                <p id="email-help" class="mt-2 text-xs leading-5 text-sign-muted">Use the email address linked to your SignGyaan account.</p>
                                @error('email')
                                    <p id="email-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ showPassword: false }">
                                <div class="mb-2 flex items-center justify-between gap-4">
                                    <label for="password" class="block text-sm font-semibold text-sign-primary">Password</label>
                                </div>

                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-sign-muted" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 0 0-9 0v3.75m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75v-6a2.25 2.25 0 0 1 2.25-2.25Z" />
                                        </svg>
                                    </span>
                                    <input
                                        id="password"
                                        name="password"
                                        :type="showPassword ? 'text' : 'password'"
                                        autocomplete="current-password"
                                        required
                                        aria-describedby="@error('password') password-error @enderror"
                                        @class([
                                            'min-h-12 w-full rounded-xl border bg-white py-3 pl-12 pr-14 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                                            'border-red-300' => $errors->has('password'),
                                            'border-sign-border' => ! $errors->has('password'),
                                        ])
                                        placeholder="Enter your password"
                                    >
                                    <button
                                        type="button"
                                        @click="showPassword = ! showPassword"
                                        class="absolute inset-y-0 right-0 flex min-h-12 w-12 items-center justify-center rounded-r-xl text-sign-muted transition hover:text-sign-primary"
                                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                        :aria-pressed="showPassword"
                                    >
                                        <svg x-show="! showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        <svg x-show="showPassword" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 2.036 12.322a1.012 1.012 0 0 0 0 .639C3.423 17.49 7.36 20.5 12 20.5c1.955 0 3.785-.535 5.351-1.466M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639a10.488 10.488 0 0 1-2.09 4.132M6.228 6.228 3 3m3.228 3.228 3.65 3.65m8.242 8.242L21 21m-2.88-2.88-3.65-3.65m0 0a3 3 0 1 0-4.242-4.242m4.242 4.242-4.242-4.242" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p id="password-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-xl text-sm text-sign-muted">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    value="1"
                                    @checked(old('remember'))
                                    class="h-5 w-5 rounded border-sign-border text-sign-primary accent-sign-primary"
                                >
                                <span>
                                    <strong class="font-semibold text-sign-primary">Remember me</strong>
                                    <span class="block text-xs leading-5">Keep me signed in on this device.</span>
                                </span>
                            </label>

                            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sign-dark focus-visible:outline-sign-cyan">
                                Sign In
                            </button>
                        </form>

                        <div class="mt-7 border-t border-sign-border pt-6 text-center">
                            <p class="text-sm text-sign-muted">
                                New to SignGyaan?
                                <a href="{{ route('register') }}" class="font-semibold text-sign-primary underline decoration-sign-cyan/50 underline-offset-4 transition hover:text-sign-cyan-dark">
                                    Create an account
                                </a>
                            </p>
                        </div>

                        <div class="mt-6 rounded-2xl bg-sign-soft p-4 text-center">
                            <p class="text-xs leading-5 text-sign-muted">
                                Your account helps SignGyaan keep your learning experience organised. Saved course progress will be connected in the next user-learning steps.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </x-container>
    </section>
@endsection
