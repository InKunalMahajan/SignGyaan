<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="mx-auto max-w-5xl px-5 py-8 sm:px-8">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Review alerts</p>
                <h1 class="mt-1 text-3xl font-black">Notifications</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $unreadCount }} unread notification{{ $unreadCount === 1 ? '' : 's' }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black">Dashboard</a>
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.read-all') }}">@csrf @method('PATCH')<button class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white">Mark all read</button></form>
                @endif
            </div>
        </div>

        @if(session('status'))<div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-900">{{ session('status') }}</div>@endif

        <section class="mt-8 space-y-3">
            @forelse($notifications as $notification)
                <article class="rounded-2xl border p-5 shadow-sm {{ is_null($notification->read_at) ? 'border-cyan-200 bg-cyan-50/50' : 'border-slate-200 bg-white' }}">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-black">{{ $notification->data['title'] ?? 'Review update' }}</h2>
                                @if(is_null($notification->read_at))<span class="rounded-full bg-blue-700 px-2.5 py-1 text-[11px] font-black text-white">Unread</span>@endif
                            </div>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $notification->data['message'] ?? '' }}</p>
                            <p class="mt-2 text-xs font-bold text-slate-400">{{ $notification->created_at?->diffForHumans() }}</p>
                        </div>
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">@csrf @method('PATCH')<button class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-black text-blue-700">{{ is_null($notification->read_at) ? 'Open & mark read' : 'Open' }}</button></form>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center"><p class="font-black">No notifications yet.</p><p class="mt-2 text-sm text-slate-500">Review submissions and decisions will appear here.</p></div>
            @endforelse
        </section>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </main>
</body>
</html>
