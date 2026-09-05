@extends('layouts.app')

@section('title', 'Notifications - SignGyaan')
@section('description', 'View your SignGyaan learning notifications and updates.')

@section('content')
    <div data-notification-center>
        <section class="border-b border-sign-border bg-sign-soft py-8 sm:py-10" aria-labelledby="notification-history-heading">
            <x-container>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Learner updates</p>
                        <h1 id="notification-history-heading" class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Notification History</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Review your learning reminders, assessment updates and other SignGyaan notifications.</p>
                    </div>

                    <div class="grid w-full gap-3 sm:w-auto sm:grid-cols-2">
                        <a href="{{ route('profile.notifications') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-4 focus:ring-sign-light">
                            Preferences
                        </a>

                        @if ($unreadCount > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft focus:outline-none focus:ring-4 focus:ring-sign-light">
                                    Mark all as read
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </x-container>
        </section>

        <section class="bg-white py-8 sm:py-12 lg:py-14" aria-labelledby="notification-list-heading">
            <x-container>
                <h2 id="notification-list-heading" class="sr-only">Your notifications</h2>

                @if (session('status'))
                    <div role="status" aria-live="polite" class="mb-6 rounded-2xl border border-sign-border bg-sign-soft px-5 py-4 text-sm font-semibold text-sign-primary">
                        {{ session('status') }}
                    </div>
                @endif

                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-sign-muted" aria-live="polite">
                        <span class="font-semibold text-sign-primary">{{ $unreadCount }}</span> unread notification{{ $unreadCount === 1 ? '' : 's' }}
                    </p>

                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">Back to Dashboard →</a>
                </div>

                <nav class="mb-8 overflow-x-auto overscroll-x-contain" aria-label="Notification history filters" data-notification-filters>
                    <div class="flex min-w-max gap-2 pb-2">
                        @foreach ($filters as $value => $label)
                            <a
                                href="{{ route('notifications.index', ['filter' => $value]) }}"
                                @class([
                                    'inline-flex min-h-11 items-center justify-center rounded-full border px-4 py-2 text-sm font-semibold transition focus:outline-none focus:ring-4 focus:ring-sign-light',
                                    'border-sign-primary bg-sign-primary text-white' => $activeFilter === $value,
                                    'border-sign-border bg-white text-sign-primary hover:border-sign-cyan hover:bg-sign-soft' => $activeFilter !== $value,
                                ])
                                @if ($activeFilter === $value) aria-current="page" @endif
                            >
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </nav>

                <p class="sr-only" role="status" aria-live="polite">
                    Showing {{ $notifications->count() }} notification{{ $notifications->count() === 1 ? '' : 's' }} in the {{ $filters[$activeFilter] ?? 'All' }} view.
                </p>

                @if ($notifications->count())
                    <ol class="space-y-4" aria-label="Notification history list">
                        @foreach ($notifications as $notification)
                            @php
                                $data = is_array($notification->data) ? $notification->data : [];
                                $isUnread = is_null($notification->read_at);
                                $category = ucfirst((string) ($data['category'] ?? 'general'));
                                $title = $data['title'] ?? 'SignGyaan update';
                                $message = $data['message'] ?? '';
                                $actionLabel = $data['action_label'] ?? 'Open';
                                $headingId = 'notification-'.$notification->id.'-title';
                            @endphp

                            <li>
                                <article aria-labelledby="{{ $headingId }}" class="rounded-2xl border p-4 sm:rounded-3xl sm:p-6 {{ $isUnread ? 'border-sign-cyan bg-sign-soft' : 'border-sign-border bg-white' }}">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-sign-cyan-dark">{{ $category }}</span>
                                                @if ($isUnread)
                                                    <span class="rounded-full bg-sign-primary px-3 py-1 text-xs font-semibold text-white"><span class="sr-only">Status: </span>Unread</span>
                                                @else
                                                    <span class="text-xs font-semibold text-sign-muted"><span class="sr-only">Status: </span>Read</span>
                                                @endif
                                            </div>

                                            <h3 id="{{ $headingId }}" class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $title }}</h3>
                                            @if ($message)
                                                <p class="mt-2 break-words text-sm leading-6 text-sign-muted">{{ $message }}</p>
                                            @endif

                                            <time class="mt-3 block text-xs font-semibold text-sign-muted" datetime="{{ $notification->created_at->toIso8601String() }}">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </time>
                                        </div>

                                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="w-full shrink-0 sm:w-auto">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark focus:outline-none focus:ring-4 focus:ring-sign-light sm:w-auto" aria-label="{{ $isUnread ? $actionLabel : 'Open' }}: {{ $title }}">
                                                {{ $isUnread ? $actionLabel : 'Open' }}
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            </li>
                        @endforeach
                    </ol>

                    <div class="mt-8 overflow-x-auto" aria-label="Notification history pagination">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 text-center sm:rounded-3xl sm:p-10" role="status">
                        <h3 class="font-heading text-2xl font-semibold text-sign-primary">No notifications in this view</h3>
                        <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-sign-muted">
                            @if ($activeFilter === 'all')
                                When SignGyaan has a useful learning update for you, it will appear here.
                            @else
                                Try another filter to review the rest of your notification history.
                            @endif
                        </p>
                        <a href="{{ route('notifications.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white">View All Notifications</a>
                    </div>
                @endif
            </x-container>
        </section>
    </div>
@endsection
