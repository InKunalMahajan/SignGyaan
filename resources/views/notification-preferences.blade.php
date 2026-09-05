@extends('layouts.app')

@section('title', 'Notification Preferences - SignGyaan')
@section('description', 'Choose which SignGyaan in-app notifications you want to receive.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10">
        <x-container>
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Account preferences</p>
                <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Notification Preferences</h1>
                <p class="mt-3 text-sm leading-6 text-sign-muted">Choose which in-app updates SignGyaan should send you. Existing notifications stay in your notification history.</p>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14">
        <x-container>
            <div class="mx-auto max-w-3xl">
                @if (session('notification_status'))
                    <div role="status" class="mb-6 rounded-2xl border border-sign-border bg-sign-soft px-5 py-4 text-sm font-semibold text-sign-primary">
                        {{ session('notification_status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('profile.notifications.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                        <div class="flex items-start gap-4">
                            <input type="hidden" name="enabled" value="0">
                            <input
                                id="notifications-enabled"
                                type="checkbox"
                                name="enabled"
                                value="1"
                                @checked(old('enabled', $user->notificationPreference('enabled', true)))
                                class="mt-1 h-5 w-5 rounded border-sign-border text-sign-primary focus:ring-sign-cyan"
                            >
                            <div>
                                <label for="notifications-enabled" class="font-heading text-xl font-semibold text-sign-primary">Enable in-app notifications</label>
                                <p class="mt-1 text-sm leading-6 text-sign-muted">Turn this off to stop all new in-app notifications. You can turn it back on at any time.</p>
                            </div>
                        </div>
                    </div>

                    <fieldset class="rounded-2xl border border-sign-border p-5 sm:rounded-3xl sm:p-6">
                        <legend class="px-2 font-heading text-xl font-semibold text-sign-primary">Notification types</legend>
                        <p class="mb-5 text-sm leading-6 text-sign-muted">Select the updates that are useful to you.</p>

                        <div class="space-y-5">
                            @php
                                $options = [
                                    'learning' => ['Learning progress', 'Saved learning place, next lesson and course learning updates.'],
                                    'assessment' => ['Assessments', 'Assessment results, pass or review updates, and retry availability.'],
                                    'milestone' => ['Achievements & milestones', 'Course completion and future learning milestone updates.'],
                                    'general' => ['General updates', 'Important SignGyaan learner notices that do not fit another category.'],
                                ];
                            @endphp

                            @foreach ($options as $key => [$label, $description])
                                <div class="flex items-start gap-4 border-t border-sign-border pt-5 first:border-t-0 first:pt-0">
                                    <input type="hidden" name="{{ $key }}" value="0">
                                    <input
                                        id="notification-{{ $key }}"
                                        type="checkbox"
                                        name="{{ $key }}"
                                        value="1"
                                        @checked(old($key, $user->notificationPreference($key, true)))
                                        class="mt-1 h-5 w-5 rounded border-sign-border text-sign-primary focus:ring-sign-cyan"
                                    >
                                    <div>
                                        <label for="notification-{{ $key }}" class="text-sm font-semibold text-sign-primary">{{ $label }}</label>
                                        <p class="mt-1 text-sm leading-6 text-sign-muted">{{ $description }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </fieldset>

                    @if ($errors->notifications->any())
                        <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                            Please check your notification preferences and try again.
                        </div>
                    @endif

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <a href="{{ route('notifications.index') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">← Back to Notifications</a>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-6 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark focus:outline-none focus:ring-4 focus:ring-sign-light">
                            Save Preferences
                        </button>
                    </div>
                </form>
            </div>
        </x-container>
    </section>
@endsection
