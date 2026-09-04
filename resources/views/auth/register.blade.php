@extends('layouts.app')

@section('title', 'Create Account - SignGyaan')
@section('description', 'Create your SignGyaan account and begin your visual and ISL-supported learning journey.')

@section('content')
    <section data-auth-page class="min-h-[calc(100vh-5rem)] bg-sign-soft py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="mx-auto grid max-w-6xl overflow-hidden rounded-3xl border border-sign-border bg-white shadow-sm lg:grid-cols-[0.9fr_1.1fr]">

                {{-- Learning Welcome Panel --}}
                <aside data-auth-visual class="relative overflow-hidden bg-sign-dark px-6 py-10 text-white sm:px-10 sm:py-12 lg:flex lg:min-h-[46rem] lg:flex-col lg:justify-between lg:px-12 lg:py-14">
                    <div class="relative z-10">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 rounded-xl" aria-label="SignGyaan home">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white font-heading text-lg font-bold text-sign-primary" aria-hidden="true">S</span>
                            <span class="font-heading text-2xl font-semibold">SignGyaan</span>
                        </a>

                        <p class="mt-8 text-sm font-semibold uppercase tracking-wider text-sign-cyan sm:mt-10">Start your learning journey</p>
                        <h1 class="mt-3 max-w-xl font-heading text-3xl font-semibold leading-tight sm:text-4xl lg:text-5xl">Learn visually, clearly and at your own pace.</h1>
                        <p class="mt-5 max-w-lg text-sm leading-7 text-white/75 sm:text-base">Create your SignGyaan account to save course progress, continue lessons and keep your learning organised.</p>
                    </div>

                    <div data-auth-benefits class="relative z-10 mt-10 grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-sign-cyan" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6.75 18 4.5m0 0 2.25 2.25M18 4.5v6.75m-12-4.5L3.75 4.5m0 0L1.5 6.75M3.75 4.5v6.75m8.25 8.25a7.5 7.5 0 0 0 7.5-7.5v-.75m-15 0V12a7.5 7.5 0 0 0 7.5 7.5Z" /></svg></span>
                            <div><p class="text-sm font-semibold">Learn your way</p><p class="mt-1 text-xs leading-5 text-white/65">Move through subjects, courses and lessons step by step.</p></div>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-sign-cyan" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg></span>
                            <div><p class="text-sm font-semibold">Structured learning</p><p class="mt-1 text-xs leading-5 text-white/65">Clear units and lessons make progress easier to follow.</p></div>
                        </div>
                        <div class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 p-4">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-sign-cyan" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h8.25A3.75 3.75 0 0 0 16.5 15V9a3.75 3.75 0 0 0-3.75-3.75H4.5A2.25 2.25 0 0 0 2.25 7.5v9A2.25 2.25 0 0 0 4.5 18.75Z" /></svg></span>
                            <div><p class="text-sm font-semibold">Visual + ISL support</p><p class="mt-1 text-xs leading-5 text-white/65">Designed around visual understanding and sign-language support.</p></div>
                        </div>
                    </div>

                    <div class="pointer-events-none absolute -bottom-28 -right-20 h-72 w-72 rounded-full bg-sign-cyan/10" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute -left-16 top-40 h-40 w-40 rounded-full border border-white/10" aria-hidden="true"></div>
                </aside>

                {{-- Registration Form --}}
                <div data-auth-form class="flex items-center px-5 py-10 sm:px-10 sm:py-12 lg:px-14 lg:py-16">
                    <div class="mx-auto w-full max-w-lg">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Create your account</p>
                            <h2 class="mt-2 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl">Join SignGyaan</h2>
                            <p class="mt-3 text-sm leading-6 text-sign-muted sm:text-base">Enter your details below to create your learning account.</p>
                        </div>

                        @if ($errors->any())
                            <div data-error-summary class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="assertive">
                                <p class="text-sm font-semibold text-red-800">Please check your account details.</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5" novalidate>
                            @csrf

                            <div>
                                <label for="name" class="mb-2 block text-sm font-semibold text-sign-primary">Full name</label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus aria-describedby="@error('name') name-error @enderror" @if($errors->has('name')) aria-invalid="true" @endif @class(['min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition placeholder:text-sign-muted/60 focus:border-sign-cyan focus:ring-4 focus:ring-sign-light','border-red-300' => $errors->has('name'),'border-sign-border' => ! $errors->has('name')]) placeholder="Your full name">
                                @error('name')<p id="name-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="email" class="mb-2 block text-sm font-semibold text-sign-primary">Email address</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" inputmode="email" required aria-describedby="email-help @error('email') email-error @enderror" @if($errors->has('email')) aria-invalid="true" @endif @class(['min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition placeholder:text-sign-muted/60 focus:border-sign-cyan focus:ring-4 focus:ring-sign-light','border-red-300' => $errors->has('email'),'border-sign-border' => ! $errors->has('email')]) placeholder="you@example.com">
                                <p id="email-help" class="mt-2 text-xs leading-5 text-sign-muted">Use an email address you can access.</p>
                                @error('email')<p id="email-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                            </div>

                            <div x-data="{ showPassword: false }">
                                <label for="password" class="mb-2 block text-sm font-semibold text-sign-primary">Password</label>
                                <div class="relative">
                                    <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" required aria-describedby="password-help @error('password') password-error @enderror" @if($errors->has('password')) aria-invalid="true" @endif @class(['min-h-12 w-full rounded-xl border bg-white px-4 py-3 pr-14 text-base text-sign-text outline-none transition placeholder:text-sign-muted/60 focus:border-sign-cyan focus:ring-4 focus:ring-sign-light','border-red-300' => $errors->has('password'),'border-sign-border' => ! $errors->has('password')]) placeholder="Create a password">
                                    <button type="button" @click="showPassword = ! showPassword" class="absolute inset-y-0 right-0 flex min-h-12 w-12 items-center justify-center rounded-r-xl text-sm font-semibold text-sign-muted transition hover:text-sign-primary" :aria-label="showPassword ? 'Hide password' : 'Show password'" :aria-pressed="showPassword"><span x-text="showPassword ? 'Hide' : 'Show'"></span></button>
                                </div>
                                <p id="password-help" class="mt-2 text-xs leading-5 text-sign-muted">Use at least 8 characters.</p>
                                @error('password')<p id="password-error" class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                            </div>

                            <div x-data="{ showConfirmation: false }">
                                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-sign-primary">Confirm password</label>
                                <div class="relative">
                                    <input id="password_confirmation" name="password_confirmation" :type="showConfirmation ? 'text' : 'password'" autocomplete="new-password" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 pr-14 text-base text-sign-text outline-none transition placeholder:text-sign-muted/60 focus:border-sign-cyan focus:ring-4 focus:ring-sign-light" placeholder="Enter the same password again">
                                    <button type="button" @click="showConfirmation = ! showConfirmation" class="absolute inset-y-0 right-0 flex min-h-12 w-12 items-center justify-center rounded-r-xl text-sm font-semibold text-sign-muted transition hover:text-sign-primary" :aria-label="showConfirmation ? 'Hide password confirmation' : 'Show password confirmation'" :aria-pressed="showConfirmation"><span x-text="showConfirmation ? 'Hide' : 'Show'"></span></button>
                                </div>
                            </div>

                            <button type="submit" class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Create Account</button>
                        </form>

                        <div class="mt-6 rounded-2xl bg-sign-soft px-4 py-4 text-center">
                            <p class="text-sm text-sign-muted">Already have a SignGyaan account? <a href="{{ route('login') }}" class="font-semibold text-sign-primary underline decoration-sign-cyan/50 underline-offset-4 transition hover:text-sign-cyan-dark">Sign in</a></p>
                        </div>

                        <p class="mt-5 text-center text-xs leading-5 text-sign-muted">Your account keeps your saved learning progress connected across SignGyaan.</p>
                    </div>
                </div>
            </div>
        </x-container>
    </section>
@endsection
