@extends('layouts.app')

@section('title', 'Create Account - SignGyaan')
@section('description', 'Create your SignGyaan learning account.')

@section('content')
    <section class="bg-sign-soft py-12 sm:py-16 lg:py-20">
        <x-container>
            <div class="mx-auto max-w-md rounded-3xl border border-sign-border bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Start learning</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary">Create your SignGyaan account</h1>
                <p class="mt-3 text-sm leading-6 text-sign-muted">Create an account so your learning experience can be connected to you.</p>

                <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-sign-primary">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus class="min-h-11 w-full rounded-xl border border-sign-border px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan">
                        @error('name')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-sign-primary">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="min-h-11 w-full rounded-xl border border-sign-border px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan">
                        @error('email')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-sign-primary">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="min-h-11 w-full rounded-xl border border-sign-border px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan">
                        <p class="mt-2 text-xs leading-5 text-sign-muted">Use at least 8 characters.</p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-sign-primary">Confirm password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="min-h-11 w-full rounded-xl border border-sign-border px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan">
                    </div>

                    <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">
                        Create Account
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-sign-muted">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-semibold text-sign-primary hover:text-sign-cyan-dark">Sign in</a>
                </p>
            </div>
        </x-container>
    </section>
@endsection
