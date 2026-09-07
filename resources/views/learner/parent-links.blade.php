<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Manage Parent access requests for your SignGyaan Learner account">
    <title>Parent Access | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-10 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Learner account</span>
                </span>
            </a>
            <div class="flex gap-2">
                <a href="{{ route('learner.profile.show') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">My Profile</a>
                <a href="{{ route('dashboard') }}" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-5xl space-y-6 px-5 py-7 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-gradient-to-br from-blue-800 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">Privacy & family access</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Parent Access Requests</h1>
            <p class="mt-3 max-w-3xl text-blue-50">You control which Parent or Guardian accounts can view your SignGyaan learning summary. New requests stay private until you approve them.</p>
        </section>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-900" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="requests-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Connections</p>
                    <h2 id="requests-heading" class="mt-1 text-xl font-black">Parent & Guardian Access</h2>
                </div>
                <span class="text-sm font-bold text-slate-500">{{ $links->where('status', 'pending')->count() }} pending</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($links as $link)
                    <article class="rounded-2xl border border-slate-200 p-4 sm:p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="font-black text-slate-950">{{ $link->parent->name }}</h3>
                                    <span class="rounded-full px-2.5 py-1 text-xs font-black uppercase tracking-wide {{ $link->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($link->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($link->status) }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">Relationship: {{ ucfirst($link->relationship) }}</p>
                                @if ($link->parent->parentProfile?->communication_mode)
                                    <p class="mt-1 text-xs font-semibold text-cyan-700">Communication: {{ strtoupper($link->parent->parentProfile->communication_mode) }}</p>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2">
                                @if ($link->status === 'pending')
                                    <form method="POST" action="{{ route('learner.parent-links.respond', $link) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="decision" value="approve">
                                        <button class="rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-black text-white hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('learner.parent-links.respond', $link) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="decision" value="decline">
                                        <button class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Decline</button>
                                    </form>
                                @elseif ($link->status === 'approved')
                                    <form method="POST" action="{{ route('learner.parent-links.destroy', $link) }}" onsubmit="return confirm('Remove this Parent’s access?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-black text-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-100">Remove access</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="font-black text-slate-800">No Parent access requests</p>
                        <p class="mt-1 text-sm text-slate-500">When a Parent requests a secure link to your account, it will appear here.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm leading-6 text-blue-900">
            <p class="font-black text-blue-950">Privacy note</p>
            <p class="mt-1">Only approved Parent links can open your family learning summary. Declining or removing a link blocks that access immediately.</p>
        </aside>
    </main>
</body>
</html>
