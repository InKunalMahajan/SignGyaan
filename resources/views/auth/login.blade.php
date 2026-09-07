<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sign in to your SignGyaan learning dashboard">
    <title>Sign in | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(420px,560px)]">
        <section class="hidden bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <a href="{{ route('dashboard.guest') }}" class="inline-flex w-fit items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-white/30">
                <span class="grid size-12 place-items-center rounded-2xl bg-white/15 text-sm font-black tracking-wide">SG</span>
                <span>
                    <span class="block text-xl font-black">SignGyaan</span>
                    <span class="block text-sm text-blue-100">Accessible learning</span>
                </span>
            </a>

            <div class="max-w-xl">
                <p class="text-sm font-black uppercase tracking-[0.18em] text-cyan-100">Welcome back</p>
                <h1 class="mt-4 text-5xl font-black leading-tight tracking-tight">Your learning space is ready.</h1>
                <p class="mt-5 max-w-lg text-lg leading-8 text-blue-100">Sign in once and SignGyaan will automatically take you to the dashboard for your account role.</p>
            </div>

            <p class="text-sm text-blue-100">ISL-friendly learning · Clear navigation · Keyboard accessible</p>
        </section>

        <section class="flex items-center justify-center px-5 py-10 sm:px-8 lg:px-12">
            <div class="w-full max-w-md">
                <a href="{{ route('dashboard.guest') }}" class="mb-8 inline-flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200 lg:hidden">
                    <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black tracking-wide text-white">SG</span>
                    <span class="font-black text-slate-950">SignGyaan</span>
                </a>

                <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">Account access</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Sign in</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Use your registered email and password to open your dashboard.</p>

                <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="text-sm font-black text-slate-800">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                            autofocus
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-base outline-none transition placeholder:text-slate-400 focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                            placeholder="you@example.com"
                        >
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="text-sm font-black text-slate-800">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-base outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100"
                        >
                        @error('password')
                            <p class="mt-2 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 text-blue-700 focus:ring-cyan-400">
                        Keep me signed in on this device
                    </label>

                    <button type="submit" class="w-full rounded-xl bg-blue-700 px-5 py-3.5 text-sm font-black text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                        Sign in to SignGyaan
                    </button>
                </form>

                <div class="mt-7 rounded-2xl border border-slate-200 bg-white p-4 text-sm text-slate-600">
                    New to SignGyaan?
                    <a href="{{ route('register') }}" class="font-black text-blue-700 underline decoration-2 underline-offset-4 focus:outline-none focus:ring-4 focus:ring-cyan-200">Create an account</a>
                </div>

                <a href="{{ route('dashboard.guest') }}" class="mt-5 inline-flex rounded-lg text-sm font-bold text-slate-600 hover:text-slate-950 focus:outline-none focus:ring-4 focus:ring-cyan-200">← Continue as Guest</a>
            </div>
        </section>
    </main>
</body>
</html>
