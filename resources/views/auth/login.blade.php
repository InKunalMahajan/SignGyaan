@extends('layouts.app')

@section('title', 'Sign In - SignGyaan')
@section('description', 'Sign in to SignGyaan and continue your learning.')

@section('content')
    <section class="bg-sign-soft py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="mx-auto max-w-md rounded-3xl border border-sign-border bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Welcome back</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary">Sign in to SignGyaan</h1>
                <p class="mt-3 text-sm leading-6 text-sign-muted">Continue your learning with your SignGyaan account.</p>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-sign-primary">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="min-h-11 w-full rounded-xl border border-sign-border px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan">
                        @error('email')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-sign-primary">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="min-h-11 w-full rounded-xl border border-sign-border px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan">
                        @error('password')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm text-sign-muted">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-sign-border">
                        <span>Remember me</span>
                    </label>

                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                        Sign In
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-sign-muted">
                    New to SignGyaan?
                    <a href="{{ route('register') }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Create an account</a>
                </p>
            </div>
        </x-container>
    </section>
@endsection
