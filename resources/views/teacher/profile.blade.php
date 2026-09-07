@extends('teacher.layout')

@section('title', 'My Profile')

@section('content')
    <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Teacher account</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">My Profile & Settings</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Keep your professional details, communication preferences, and account security up to date.</p>
        </div>
        <a href="{{ route('teacher.classes.index') }}" class="rounded-xl bg-blue-700 px-4 py-3 text-sm font-black text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Open My Classes</a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,.65fr)]">
        <form method="POST" action="{{ route('teacher.profile.update') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')

            <h2 class="text-xl font-black">Profile information</h2>
            <p class="mt-1 text-sm text-slate-500">Used in your Teacher workspace and future class communication.</p>

            <div class="mt-6 grid gap-5 sm:grid-cols-2">
                <label class="block sm:col-span-2">
                    <span class="text-sm font-black text-slate-700">Full name</span>
                    <input name="name" value="{{ old('name', $user->name) }}" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-black text-slate-700">Email</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>

                <label class="block">
                    <span class="text-sm font-black text-slate-700">Phone</span>
                    <input name="phone" value="{{ old('phone', $profile->phone) }}" maxlength="30" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>

                <label class="block">
                    <span class="text-sm font-black text-slate-700">Designation</span>
                    <input name="designation" value="{{ old('designation', $profile->designation) }}" maxlength="120" placeholder="Teacher / Lecturer / Trainer" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-black text-slate-700">Qualification</span>
                    <input name="qualification" value="{{ old('qualification', $profile->qualification) }}" maxlength="160" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>

                <label class="block">
                    <span class="text-sm font-black text-slate-700">Preferred language</span>
                    <select name="preferred_language" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                        @foreach ($languages as $value => $label)
                            <option value="{{ $value }}" @selected(old('preferred_language', $profile->preferred_language) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-black text-slate-700">Communication mode</span>
                    <select name="communication_mode" required class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                        @foreach ($communicationModes as $value => $label)
                            <option value="{{ $value }}" @selected(old('communication_mode', $profile->communication_mode) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-black text-slate-700">Short professional bio</span>
                    <textarea name="bio" rows="5" maxlength="1000" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">{{ old('bio', $profile->bio) }}</textarea>
                </label>
            </div>

            <button type="submit" class="mt-6 rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Save profile</button>
        </form>

        <div class="space-y-6">
            <section class="rounded-3xl border border-blue-100 bg-blue-50 p-6">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-blue-600">Teacher account</p>
                <h2 class="mt-2 text-lg font-black text-blue-950">{{ $user->name }}</h2>
                <p class="mt-1 text-sm text-blue-900/70">{{ $user->email }}</p>
                <p class="mt-4 rounded-xl bg-white/70 px-3 py-2 text-sm font-bold text-blue-900">Role: Teacher</p>
            </section>

            <form method="POST" action="{{ route('teacher.profile.password.update') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                @csrf
                @method('PUT')
                <h2 class="text-lg font-black">Change password</h2>
                <p class="mt-1 text-sm text-slate-500">Your current password is required.</p>

                <label class="mt-5 block">
                    <span class="text-sm font-black text-slate-700">Current password</span>
                    <input type="password" name="current_password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>
                <label class="mt-4 block">
                    <span class="text-sm font-black text-slate-700">New password</span>
                    <input type="password" name="password" required autocomplete="new-password" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>
                <label class="mt-4 block">
                    <span class="text-sm font-black text-slate-700">Confirm new password</span>
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="mt-2 w-full rounded-xl border-slate-300 focus:border-cyan-500 focus:ring-cyan-500">
                </label>

                <button type="submit" class="mt-5 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-black text-slate-800 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-cyan-200">Change password</button>
            </form>
        </div>
    </div>
@endsection
