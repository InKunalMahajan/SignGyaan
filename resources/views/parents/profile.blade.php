<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Manage your SignGyaan Parent profile and linked learners">
    <title>Parent Profile | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-10 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Parent account</span>
                </span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl space-y-6 px-5 py-7 sm:px-8 lg:py-10">
        <section class="rounded-3xl bg-gradient-to-br from-blue-800 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">Family learning</p>
            <h1 class="mt-3 text-3xl font-black tracking-tight sm:text-4xl">Parent Profile & Learner Linking</h1>
            <p class="mt-3 max-w-3xl text-blue-50">Manage your account and request secure access to a Learner. A Learner must approve every new connection before learning information becomes visible.</p>
        </section>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-900" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900" role="alert">
                <p class="font-black">Please check the form.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="profile-heading">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Account</p>
                <h2 id="profile-heading" class="mt-1 text-xl font-black">My Parent Profile</h2>
                <form method="POST" action="{{ route('parents.profile.update') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                    @csrf
                    @method('PUT')

                    <label class="sm:col-span-1">
                        <span class="text-sm font-black text-slate-700">Full name</span>
                        <input name="name" value="{{ old('name', $user->name) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                    </label>
                    <label class="sm:col-span-1">
                        <span class="text-sm font-black text-slate-700">Email</span>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                    </label>
                    <label>
                        <span class="text-sm font-black text-slate-700">Phone / WhatsApp <span class="font-semibold text-slate-400">(optional)</span></span>
                        <input name="phone" value="{{ old('phone', $profile->phone) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                    </label>
                    <label>
                        <span class="text-sm font-black text-slate-700">Preferred language</span>
                        <select name="preferred_language" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            @foreach ($languages as $value => $label)
                                <option value="{{ $value }}" @selected(old('preferred_language', $profile->preferred_language) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="sm:col-span-2">
                        <span class="text-sm font-black text-slate-700">Communication preference</span>
                        <select name="communication_mode" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            @foreach ($communicationModes as $value => $label)
                                <option value="{{ $value }}" @selected(old('communication_mode', $profile->communication_mode) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="sm:col-span-2">
                        <button class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Save profile</button>
                    </div>
                </form>
            </section>

            <aside class="rounded-2xl border border-cyan-100 bg-cyan-50 p-5 sm:p-6">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Secure linking</p>
                <h2 class="mt-1 text-xl font-black text-cyan-950">Connect a Learner</h2>
                <p class="mt-2 text-sm leading-6 text-cyan-900/80">Enter the exact email used by the Learner’s SignGyaan account. They will see your request and must approve it.</p>
                <form method="POST" action="{{ route('parents.learners.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <label class="block">
                        <span class="text-sm font-black text-cyan-950">Learner email</span>
                        <input type="email" name="learner_email" value="{{ old('learner_email') }}" required placeholder="learner@example.com" class="mt-2 w-full rounded-xl border border-cyan-200 bg-white px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                    </label>
                    <label class="block">
                        <span class="text-sm font-black text-cyan-950">Relationship</span>
                        <select name="relationship" class="mt-2 w-full rounded-xl border border-cyan-200 bg-white px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            @foreach ($relationships as $value => $label)
                                <option value="{{ $value }}" @selected(old('relationship', 'parent') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <button class="w-full rounded-xl bg-cyan-700 px-4 py-3 text-sm font-black text-white hover:bg-cyan-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Send link request</button>
                </form>
            </aside>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="learners-heading">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Family access</p>
                    <h2 id="learners-heading" class="mt-1 text-xl font-black">Linked Learners</h2>
                </div>
                <span class="text-sm font-bold text-slate-500">{{ $links->where('status', 'approved')->count() }} approved</span>
            </div>

            <div class="mt-5 space-y-3">
                @forelse ($links as $link)
                    <article class="flex flex-col gap-4 rounded-2xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-black text-slate-950">{{ $link->learner->name }}</h3>
                                <span class="rounded-full px-2.5 py-1 text-xs font-black uppercase tracking-wide {{ $link->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($link->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">{{ ucfirst($link->status) }}</span>
                            </div>
                            <p class="mt-1 text-sm text-slate-500">{{ ucfirst($link->relationship) }} · Requested {{ $link->created_at->diffForHumans() }}</p>
                            @if ($link->status === 'pending')
                                <p class="mt-1 text-xs font-semibold text-amber-700">Waiting for Learner approval.</p>
                            @elseif ($link->status === 'declined')
                                <p class="mt-1 text-xs font-semibold text-slate-500">Request declined. You may send a new request if needed.</p>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if ($link->status === 'approved')
                                <a href="{{ route('parents.learners.show', $link) }}" class="rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">View learner</a>
                            @endif
                            <form method="POST" action="{{ route('parents.learners.destroy', $link) }}" onsubmit="return confirm('Remove this Learner link?')">
                                @csrf
                                @method('DELETE')
                                <button class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Remove</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center">
                        <p class="font-black text-slate-800">No Learners linked yet</p>
                        <p class="mt-1 text-sm text-slate-500">Use the secure linking form above to send your first request.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="password-heading">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Security</p>
            <h2 id="password-heading" class="mt-1 text-xl font-black">Change Password</h2>
            <form method="POST" action="{{ route('parents.profile.password.update') }}" class="mt-5 grid gap-4 md:grid-cols-3">
                @csrf
                @method('PUT')
                <label>
                    <span class="text-sm font-black text-slate-700">Current password</span>
                    <input type="password" name="current_password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                </label>
                <label>
                    <span class="text-sm font-black text-slate-700">New password</span>
                    <input type="password" name="password" required autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                </label>
                <label>
                    <span class="text-sm font-black text-slate-700">Confirm password</span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-cyan-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                </label>
                <div class="md:col-span-3">
                    <button class="rounded-xl bg-slate-950 px-5 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Change password</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
