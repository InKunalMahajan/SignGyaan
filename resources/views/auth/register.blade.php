<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Create a SignGyaan learner, parent, or teacher account">
    <title>Create account | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(420px,560px)_minmax(0,1fr)]">
        <section class="flex items-center justify-center px-5 py-10 sm:px-8 lg:px-12">
            <div class="w-full max-w-lg">
                <a href="{{ route('dashboard.guest') }}" class="mb-8 inline-flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                    <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black tracking-wide text-white">SG</span>
                    <span>
                        <span class="block font-black text-slate-950">SignGyaan</span>
                        <span class="block text-xs font-medium text-slate-500">Accessible learning</span>
                    </span>
                </a>

                <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">Get started</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Create your account</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">Choose the role that matches how you will use SignGyaan. Admin accounts are created separately for security.</p>

                <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="name" class="text-sm font-black text-slate-800">Full name</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-base outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('name')
                            <p class="mt-2 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <fieldset>
                        <legend class="text-sm font-black text-slate-800">I am joining as</legend>
                        <div class="mt-2 grid gap-3 sm:grid-cols-3">
                            @foreach ($roles as $roleValue => $roleLabel)
                                <label class="cursor-pointer rounded-xl border border-slate-300 bg-white p-4 transition has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 has-[:checked]:ring-2 has-[:checked]:ring-blue-100">
                                    <input type="radio" name="role" value="{{ $roleValue }}" class="size-4 text-blue-700 focus:ring-cyan-400" @checked(old('role', 'learner') === $roleValue)>
                                    <span class="mt-3 block text-sm font-black text-slate-900">{{ $roleLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('role')
                            <p class="mt-2 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <div>
                        <label for="email" class="text-sm font-black text-slate-800">Email address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-base outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        @error('email')
                            <p class="mt-2 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="password" class="text-sm font-black text-slate-800">Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-base outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        </div>
                        <div>
                            <label for="password_confirmation" class="text-sm font-black text-slate-800">Confirm password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3.5 text-base outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-100">
                        </div>
                    </div>
                    @error('password')
                        <p class="-mt-2 text-sm font-semibold text-red-700" role="alert">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="w-full rounded-xl bg-blue-700 px-5 py-3.5 text-sm font-black text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">
                        Create account
                    </button>
                </form>

                <p class="mt-7 text-sm text-slate-600">Already registered? <a href="{{ route('login') }}" class="font-black text-blue-700 underline decoration-2 underline-offset-4 focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign in</a></p>
            </div>
        </section>

        <section class="hidden bg-gradient-to-br from-slate-950 via-blue-950 to-blue-800 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <p class="text-sm font-black uppercase tracking-[0.18em] text-cyan-200">Role-based experience</p>

            <div class="max-w-2xl">
                <h2 class="text-5xl font-black leading-tight tracking-tight">One SignGyaan account. The right workspace automatically.</h2>
                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-5">
                        <p class="font-black">Learner</p>
                        <p class="mt-2 text-sm leading-6 text-blue-100">Courses, lessons, quizzes, progress, and achievements.</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-5">
                        <p class="font-black">Parents</p>
                        <p class="mt-2 text-sm leading-6 text-blue-100">Learning progress, results, goals, and teacher updates.</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-5">
                        <p class="font-black">Teacher</p>
                        <p class="mt-2 text-sm leading-6 text-blue-100">Classes, lessons, ISL videos, assessments, and learner insights.</p>
                    </div>
                </div>
            </div>

            <p class="text-sm text-blue-100">Admin registration is intentionally disabled on this public page.</p>
        </section>
    </main>
</body>
</html>
