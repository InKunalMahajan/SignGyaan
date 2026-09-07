<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Manage your SignGyaan learner profile and accessibility preferences">
    <title>My Profile | SignGyaan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-950 antialiased">
    <a href="#main-content" class="sr-only z-50 rounded-lg bg-slate-950 px-4 py-3 text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Skip to main content</a>

    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-cyan-200">
                <span class="grid size-11 place-items-center rounded-2xl bg-gradient-to-br from-blue-700 to-cyan-500 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-lg font-black">SignGyaan</span>
                    <span class="block text-xs font-semibold text-slate-500">Learner account</span>
                </span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.role', 'learner') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-black text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-cyan-200">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Sign out</button>
                </form>
            </div>
        </div>
    </header>

    <main id="main-content" class="mx-auto max-w-6xl space-y-6 px-5 py-8 sm:px-8 lg:py-10">
        <section class="overflow-hidden rounded-3xl bg-gradient-to-br from-blue-800 via-blue-700 to-cyan-600 p-6 text-white shadow-lg sm:p-8">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-100">My account</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Learner Profile & Settings</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-blue-50 sm:text-base">Keep your learning information, communication preferences, accessibility settings, profile photo, and password up to date.</p>
                </div>
                <div class="shrink-0">
                    @if ($profile->avatar_url)
                        <img src="{{ $profile->avatar_url }}" alt="Profile photo for {{ $user->name }}" class="size-24 rounded-3xl border-4 border-white/30 object-cover shadow-lg">
                    @else
                        <div class="grid size-24 place-items-center rounded-3xl border-4 border-white/30 bg-white/15 text-3xl font-black shadow-lg" aria-label="Profile initials">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </div>
        </section>

        @if (session('status'))
            <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-900">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-900">
                <p class="font-black">Please fix the following:</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(300px,.65fr)]">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7" aria-labelledby="profile-heading">
                <div class="border-b border-slate-100 pb-5">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Profile</p>
                    <h2 id="profile-heading" class="mt-1 text-2xl font-black tracking-tight">Personal learning settings</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">These details help SignGyaan personalise your learner experience.</p>
                </div>

                <form method="POST" action="{{ route('learner.profile.update') }}" class="mt-6 space-y-8">
                    @csrf
                    @method('PUT')

                    <fieldset>
                        <legend class="text-base font-black text-slate-950">Account & education</legend>
                        <div class="mt-4 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="name" class="text-sm font-black text-slate-700">Full name</label>
                                <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            </div>
                            <div>
                                <label for="email" class="text-sm font-black text-slate-700">Email address</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            </div>
                            <div>
                                <label for="education_level" class="text-sm font-black text-slate-700">Education level</label>
                                <input id="education_level" name="education_level" value="{{ old('education_level', $profile->education_level) }}" placeholder="e.g. Junior College" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            </div>
                            <div>
                                <label for="class_grade" class="text-sm font-black text-slate-700">Class / grade</label>
                                <input id="class_grade" name="class_grade" value="{{ old('class_grade', $profile->class_grade) }}" placeholder="e.g. FYJC" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="institution" class="text-sm font-black text-slate-700">School / college / institution</label>
                                <input id="institution" name="institution" value="{{ old('institution', $profile->institution) }}" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border-t border-slate-100 pt-7">
                        <legend class="text-base font-black text-slate-950">Language & communication</legend>
                        <div class="mt-4 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="preferred_language" class="text-sm font-black text-slate-700">Preferred language</label>
                                <select id="preferred_language" name="preferred_language" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                                    @foreach ($languages as $value => $label)
                                        <option value="{{ $value }}" @selected(old('preferred_language', $profile->preferred_language) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="communication_mode" class="text-sm font-black text-slate-700">Preferred learning communication</label>
                                <select id="communication_mode" name="communication_mode" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                                    @foreach ($communicationModes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('communication_mode', $profile->communication_mode) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="border-t border-slate-100 pt-7">
                        <legend class="text-base font-black text-slate-950">Accessibility preferences</legend>
                        <p class="mt-2 text-sm text-slate-500">Save how you prefer learning content and interface information to be presented.</p>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <input type="hidden" name="captions_enabled" value="0">
                                <input type="checkbox" name="captions_enabled" value="1" @checked((bool) old('captions_enabled', $profile->captions_enabled)) class="mt-1 size-4 rounded border-slate-300 text-blue-700 focus:ring-cyan-300">
                                <span><span class="block text-sm font-black">Captions on</span><span class="mt-1 block text-xs leading-5 text-slate-500">Prefer captions when video captions are available.</span></span>
                            </label>
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <input type="hidden" name="high_contrast" value="0">
                                <input type="checkbox" name="high_contrast" value="1" @checked((bool) old('high_contrast', $profile->high_contrast)) class="mt-1 size-4 rounded border-slate-300 text-blue-700 focus:ring-cyan-300">
                                <span><span class="block text-sm font-black">Higher contrast</span><span class="mt-1 block text-xs leading-5 text-slate-500">Prefer stronger visual contrast for controls and text.</span></span>
                            </label>
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <input type="hidden" name="reduced_motion" value="0">
                                <input type="checkbox" name="reduced_motion" value="1" @checked((bool) old('reduced_motion', $profile->reduced_motion)) class="mt-1 size-4 rounded border-slate-300 text-blue-700 focus:ring-cyan-300">
                                <span><span class="block text-sm font-black">Reduce motion</span><span class="mt-1 block text-xs leading-5 text-slate-500">Prefer fewer non-essential animations and movements.</span></span>
                            </label>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <label for="text_size" class="text-sm font-black">Text size preference</label>
                                <select id="text_size" name="text_size" class="mt-3 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                                    @foreach ($textSizes as $value => $label)
                                        <option value="{{ $value }}" @selected(old('text_size', $profile->text_size) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Save profile & preferences</button>
                </form>
            </section>

            <div class="space-y-6">
                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="photo-heading">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Profile photo</p>
                    <h2 id="photo-heading" class="mt-1 text-xl font-black">Avatar</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">JPG, PNG, or WebP. Maximum 2 MB.</p>

                    <form method="POST" action="{{ route('learner.profile.avatar.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="avatar" class="sr-only">Choose profile photo</label>
                            <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" required class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2.5 file:font-black file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Upload photo</button>
                    </form>

                    @if ($profile->avatar_path)
                        <form method="POST" action="{{ route('learner.profile.avatar.destroy') }}" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded-xl border border-rose-200 bg-white px-4 py-3 text-sm font-black text-rose-700 hover:bg-rose-50 focus:outline-none focus:ring-4 focus:ring-rose-100">Remove photo</button>
                        </form>
                    @endif
                </section>

                <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="security-heading">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Security</p>
                    <h2 id="security-heading" class="mt-1 text-xl font-black">Change password</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Enter your current password before setting a new one.</p>

                    <form method="POST" action="{{ route('learner.profile.password.update') }}" class="mt-5 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="current_password" class="text-sm font-black text-slate-700">Current password</label>
                            <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                        </div>
                        <div>
                            <label for="password" class="text-sm font-black text-slate-700">New password</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                        </div>
                        <div>
                            <label for="password_confirmation" class="text-sm font-black text-slate-700">Confirm new password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                        </div>
                        <button type="submit" class="w-full rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Change password</button>
                    </form>
                </section>

                <aside class="rounded-3xl border border-blue-100 bg-blue-50 p-5 sm:p-6">
                    <p class="text-sm font-black text-blue-950">Your privacy</p>
                    <p class="mt-2 text-sm leading-6 text-blue-900/80">Only your account and authorised platform features should use these preferences. Your password is never displayed on this page.</p>
                </aside>
            </div>
        </div>
    </main>
</body>
</html>
