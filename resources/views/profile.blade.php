@extends('layouts.app')

@section('title', 'Profile & Account - SignGyaan')
@section('description', 'Manage your SignGyaan profile, account details and password.')

@section('content')
    @php
        $initial = strtoupper(substr(trim($user->name), 0, 1));
        $joinedDate = optional($user->created_at)->format('d M Y');
    @endphp

    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10 lg:py-12">
        <x-container>
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="transition hover:text-sign-primary">Dashboard</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Profile & Account</span>
            </nav>

            <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-end">
                <div class="flex min-w-0 items-start gap-4 sm:items-center sm:gap-5">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-sign-primary font-heading text-2xl font-semibold text-white sm:h-20 sm:w-20 sm:text-3xl" aria-hidden="true">
                        {{ $initial }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Your account</p>
                        <h1 class="mt-1 font-heading text-3xl font-semibold tracking-tight text-sign-primary sm:text-4xl lg:text-5xl">
                            Profile & Account
                        </h1>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-sign-muted sm:text-base">
                            Keep your account information current and manage the password you use to sign in to SignGyaan.
                        </p>
                    </div>
                </div>

                <a href="{{ route('my-learning') }}" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light sm:w-auto lg:w-full">
                    View My Learning
                </a>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-8 sm:py-12 lg:py-16">
        <x-container>
            <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
                <div class="min-w-0 space-y-8">
                    {{-- Profile Details --}}
                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8" aria-labelledby="profile-details-heading">
                        <div class="max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Personal details</p>
                            <h2 id="profile-details-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Profile information</h2>
                            <p class="mt-3 text-sm leading-6 text-sign-muted">Your name is used across your learning dashboard. Your email is used to sign in.</p>
                        </div>

                        @if (session('profile_status'))
                            <div class="mt-6 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status">
                                {{ session('profile_status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.update') }}" class="mt-7 space-y-5">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="profile-name" class="mb-2 block text-sm font-semibold text-sign-primary">Full name</label>
                                <input
                                    id="profile-name"
                                    name="name"
                                    type="text"
                                    value="{{ old('name', $user->name) }}"
                                    autocomplete="name"
                                    required
                                    @class([
                                        'min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                                        'border-red-300' => $errors->profile->has('name'),
                                        'border-sign-border' => ! $errors->profile->has('name'),
                                    ])
                                >
                                @error('name', 'profile')
                                    <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="profile-email" class="mb-2 block text-sm font-semibold text-sign-primary">Email address</label>
                                <input
                                    id="profile-email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email', $user->email) }}"
                                    autocomplete="email"
                                    inputmode="email"
                                    required
                                    @class([
                                        'min-h-12 w-full rounded-xl border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                                        'border-red-300' => $errors->profile->has('email'),
                                        'border-sign-border' => ! $errors->profile->has('email'),
                                    ])
                                >
                                @error('email', 'profile')
                                    <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs leading-5 text-sign-muted">Changing this email also changes the address you use to sign in.</p>
                            </div>

                            <div class="flex justify-end border-t border-sign-border pt-5">
                                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark sm:w-auto">
                                    Save Profile
                                </button>
                            </div>
                        </form>
                    </section>

                    {{-- Password --}}
                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-8" aria-labelledby="password-heading">
                        <div class="max-w-2xl">
                            <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Security</p>
                            <h2 id="password-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary sm:text-3xl">Change password</h2>
                            <p class="mt-3 text-sm leading-6 text-sign-muted">Enter your current password first, then choose a new password with at least 8 characters.</p>
                        </div>

                        @if (session('password_status'))
                            <div class="mt-6 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status">
                                {{ session('password_status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('profile.password.update') }}" class="mt-7 space-y-5">
                            @csrf
                            @method('PUT')

                            <div x-data="{ show: false }">
                                <label for="current-password" class="mb-2 block text-sm font-semibold text-sign-primary">Current password</label>
                                <div class="relative">
                                    <input
                                        id="current-password"
                                        name="current_password"
                                        :type="show ? 'text' : 'password'"
                                        autocomplete="current-password"
                                        required
                                        @class([
                                            'min-h-12 w-full rounded-xl border bg-white px-4 py-3 pr-14 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                                            'border-red-300' => $errors->password->has('current_password'),
                                            'border-sign-border' => ! $errors->password->has('current_password'),
                                        ])
                                    >
                                    <button type="button" @click="show = ! show" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-sm font-semibold text-sign-muted transition hover:text-sign-primary" :aria-label="show ? 'Hide current password' : 'Show current password'">
                                        <span x-text="show ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                @error('current_password', 'password')
                                    <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ show: false }">
                                <label for="new-password" class="mb-2 block text-sm font-semibold text-sign-primary">New password</label>
                                <div class="relative">
                                    <input
                                        id="new-password"
                                        name="password"
                                        :type="show ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        required
                                        @class([
                                            'min-h-12 w-full rounded-xl border bg-white px-4 py-3 pr-14 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light',
                                            'border-red-300' => $errors->password->has('password'),
                                            'border-sign-border' => ! $errors->password->has('password'),
                                        ])
                                    >
                                    <button type="button" @click="show = ! show" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-sm font-semibold text-sign-muted transition hover:text-sign-primary" :aria-label="show ? 'Hide new password' : 'Show new password'">
                                        <span x-text="show ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                @error('password', 'password')
                                    <p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div x-data="{ show: false }">
                                <label for="password-confirmation" class="mb-2 block text-sm font-semibold text-sign-primary">Confirm new password</label>
                                <div class="relative">
                                    <input
                                        id="password-confirmation"
                                        name="password_confirmation"
                                        :type="show ? 'text' : 'password'"
                                        autocomplete="new-password"
                                        required
                                        class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 pr-14 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light"
                                    >
                                    <button type="button" @click="show = ! show" class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-sm font-semibold text-sign-muted transition hover:text-sign-primary" :aria-label="show ? 'Hide password confirmation' : 'Show password confirmation'">
                                        <span x-text="show ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end border-t border-sign-border pt-5">
                                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark sm:w-auto">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </section>
                </div>

                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="Account summary">
                    <div class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Account summary</p>
                        <dl class="mt-4 space-y-4 text-sm">
                            <div>
                                <dt class="text-xs text-sign-muted">Name</dt>
                                <dd class="mt-1 break-words font-semibold text-sign-primary">{{ $user->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-sign-muted">Email</dt>
                                <dd class="mt-1 break-all font-semibold text-sign-primary">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-sign-muted">Member since</dt>
                                <dd class="mt-1 font-semibold text-sign-primary">{{ $joinedDate ?: 'SignGyaan learner' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning activity</p>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center xl:grid-cols-1 xl:text-left">
                            <div class="rounded-xl bg-sign-soft p-3">
                                <p class="font-heading text-2xl font-semibold text-sign-primary">{{ $coursesStarted }}</p>
                                <p class="mt-1 text-xs text-sign-muted">Courses started</p>
                            </div>
                            <div class="rounded-xl bg-sign-soft p-3">
                                <p class="font-heading text-2xl font-semibold text-sign-primary">{{ $completedLessons }}</p>
                                <p class="mt-1 text-xs text-sign-muted">Lessons completed</p>
                            </div>
                            <div class="rounded-xl bg-sign-soft p-3">
                                <p class="font-heading text-2xl font-semibold text-sign-primary">{{ $completedCourses }}</p>
                                <p class="mt-1 text-xs text-sign-muted">Courses completed</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-sign-dark p-5 text-white sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan">Account session</p>
                        <p class="mt-3 text-sm leading-6 text-white/75">Sign out when you have finished using SignGyaan on a shared device.</p>
                        <form method="POST" action="{{ route('logout') }}" class="mt-5">
                            @csrf
                            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </aside>
            </div>
        </x-container>
    </section>
@endsection
