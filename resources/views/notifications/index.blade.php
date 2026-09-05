@extends('layouts.app')

@section('title', 'Notifications - SignGyaan')
@section('description', 'View your SignGyaan learning notifications and updates.')

@section('content')
    <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10">
        <x-container>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner updates</p>
                    <h1 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Notifications</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Learning reminders, assessment updates and milestones will appear here.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('profile.notifications') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-4 focus:ring-sign-light">
                        Preferences
                    </a>

                    @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-4 focus:ring-sign-light">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </x-container>
    </section>

    <section class="bg-white py-10 sm:py-14">
        <x-container>
            @if (session('status'))
                <div role="status" class="mb-6 rounded-2xl border border-sign-border bg-sign-soft px-5 py-4 text-sm font-semibold text-sign-primary">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-5 flex items-center justify-between gap-4">
                <p class="text-sm text-sign-muted">
                    <span class="font-semibold text-sign-primary">{{ $unreadCount }}</span> unread
                </p>
                <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Back to Dashboard →</a>
            </div>

            @if ($notifications->count())
                <ol class="space-y-4" aria-label="Notification list">
                    @foreach ($notifications as $notification)
                        @php
                            $data = is_array($notification->data) ? $notification->data : [];
                            $isUnread = is_null($notification->read_at);
                            $category = ucfirst((string) ($data['category'] ?? 'general'));
                            $title = $data['title'] ?? 'SignGyaan update';
                            $message = $data['message'] ?? '';
                            $actionLabel = $data['action_label'] ?? 'Open';
                        @endphp

                        <li>
                            <article class="rounded-2xl border p-5 sm:rounded-3xl sm:p-6 {{ $isUnread ? 'border-sign-cyan bg-sign-soft' : 'border-sign-border bg-white' }}">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-cyan-dark">{{ $category }}</span>
                                            @if ($isUnread)
                                                <span class="rounded-full bg-sign-primary px-3 py-1 text-xs font-semibold text-white">Unread</span>
                                            @else
                                                <span class="text-xs font-semibold text-sign-muted">Read</span>
                                            @endif
                                        </div>

                                        <h2 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $title }}</h2>
                                        @if ($message)
                                            <p class="mt-2 text-sm leading-6 text-sign-muted">{{ $message }}</p>
                                        @endif

                                        <time class="mt-3 block text-xs font-semibold text-sign-muted" datetime="{{ $notification->created_at->toIso8601String() }}">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </time>
                                    </div>

                                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="shrink-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark focus:outline-none focus:ring-4 focus:ring-sign-light sm:w-auto">
                                            {{ $isUnread ? $actionLabel : 'Open' }}
                                        </button>
                                    </form>
                                </div>
                            </article>
                        </li>
                    @endforeach
                </ol>

                <div class="mt-8">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-sign-border bg-sign-soft p-8 text-center sm:rounded-3xl sm:p-10">
                    <h2 class="font-heading text-2xl font-semibold text-sign-primary">No notifications yet</h2>
                    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">When SignGyaan has a useful learning update for you, it will appear here.</p>
                    <a href="{{ route('dashboard') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">Go to Dashboard</a>
                </div>
            @endif
        </x-container>
    </section>
@endsection
