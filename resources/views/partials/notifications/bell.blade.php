@php
    $notificationUser = auth()->user();
    $headerUnreadNotifications = $notificationUser->unreadNotifications()
        ->latest()
        ->limit(5)
        ->get();
    $headerUnreadCount = $notificationUser->unreadNotifications()->count();
@endphp

<div class="relative" data-notification-bell>
    <button
        type="button"
        @click="notificationOpen = ! notificationOpen; accountOpen = false; searchOpen = false; mobileMenuOpen = false"
        class="relative inline-flex h-11 w-11 items-center justify-center rounded-xl border border-sign-border bg-white text-sign-primary transition hover:border-sign-cyan hover:bg-sign-soft"
        :aria-expanded="notificationOpen"
        aria-controls="desktop-notification-menu"
        aria-label="Notifications{{ $headerUnreadCount > 0 ? ', '.$headerUnreadCount.' unread' : '' }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-5 w-5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>

        @if ($headerUnreadCount > 0)
            <span class="absolute -right-1 -top-1 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-sign-primary px-1 text-[10px] font-bold leading-none text-white" aria-hidden="true">
                {{ $headerUnreadCount > 99 ? '99+' : $headerUnreadCount }}
            </span>
        @endif
    </button>

    <div
        id="desktop-notification-menu"
        x-show="notificationOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        @click.outside="notificationOpen = false"
        class="absolute right-0 top-full mt-3 w-96 max-w-[calc(100vw-2rem)] overflow-hidden rounded-2xl border border-sign-border bg-white shadow-xl"
        aria-label="Notification preview"
    >
        <div class="flex items-center justify-between gap-3 border-b border-sign-border bg-sign-soft px-5 py-4">
            <div>
                <p class="font-heading text-lg font-semibold text-sign-primary">Notifications</p>
                <p class="mt-0.5 text-xs text-sign-muted">{{ $headerUnreadCount }} unread</p>
            </div>

            @if ($headerUnreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="min-h-11 rounded-lg px-3 text-xs font-semibold text-sign-primary transition hover:bg-white">
                        Mark all read
                    </button>
                </form>
            @endif
        </div>

        @if ($headerUnreadNotifications->isNotEmpty())
            <div class="max-h-96 divide-y divide-sign-border overflow-y-auto overscroll-contain">
                @foreach ($headerUnreadNotifications as $notification)
                    @php
                        $notificationData = $notification->data;
                        $notificationTitle = data_get($notificationData, 'title', 'Notification');
                        $notificationMessage = data_get($notificationData, 'message');
                        $notificationCategory = ucfirst((string) data_get($notificationData, 'category', 'general'));
                    @endphp

                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full px-5 py-4 text-left transition hover:bg-sign-soft focus:bg-sign-soft">
                            <span class="flex items-center justify-between gap-3">
                                <span class="text-xs font-semibold uppercase tracking-wide text-sign-cyan-dark">{{ $notificationCategory }}</span>
                                <span class="text-xs text-sign-muted">{{ $notification->created_at?->diffForHumans() }}</span>
                            </span>
                            <span class="mt-1.5 block text-sm font-semibold text-sign-primary">{{ $notificationTitle }}</span>
                            @if ($notificationMessage)
                                <span class="mt-1 block line-clamp-2 text-xs leading-5 text-sign-muted">{{ $notificationMessage }}</span>
                            @endif
                        </button>
                    </form>
                @endforeach
            </div>
        @else
            <div class="px-5 py-8 text-center">
                <p class="font-semibold text-sign-primary">You're all caught up</p>
                <p class="mt-1 text-sm text-sign-muted">New learning and assessment updates will appear here.</p>
            </div>
        @endif

        <div class="border-t border-sign-border p-2">
            <a href="{{ route('notifications.index') }}" class="flex min-h-11 items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">
                View all notifications →
            </a>
        </div>
    </div>
</div>
