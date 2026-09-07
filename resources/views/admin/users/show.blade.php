@extends('admin.layout')

@section('title', 'User Details')

@section('content')
    <div class="max-w-5xl">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-black text-blue-700 hover:text-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-100">← Back to users</a>
                <p class="mt-5 text-xs font-black uppercase tracking-[0.16em] text-cyan-700">User Details</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight">{{ $managedUser->name }}</h1>
                <p class="mt-2 text-sm text-slate-500">{{ $managedUser->email }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.edit', $managedUser) }}" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Edit user</a>
                @if (! auth()->user()->is($managedUser))
                    <form method="POST" action="{{ route('admin.users.status', $managedUser) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-xl px-5 py-3 text-sm font-black focus:outline-none focus:ring-4 focus:ring-cyan-200 {{ $managedUser->is_active ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">{{ $managedUser->is_active ? 'Deactivate account' : 'Activate account' }}</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="mt-7 grid gap-5 lg:grid-cols-3">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2" aria-labelledby="account-heading">
                <h2 id="account-heading" class="text-lg font-black">Account information</h2>
                <dl class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div><dt class="text-xs font-black uppercase tracking-wide text-slate-400">Full name</dt><dd class="mt-1 font-bold text-slate-900">{{ $managedUser->name }}</dd></div>
                    <div><dt class="text-xs font-black uppercase tracking-wide text-slate-400">Email</dt><dd class="mt-1 font-bold text-slate-900">{{ $managedUser->email }}</dd></div>
                    <div><dt class="text-xs font-black uppercase tracking-wide text-slate-400">Role</dt><dd class="mt-1"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-black text-blue-700">{{ $roles[$managedUser->role] ?? ucfirst($managedUser->role) }}</span></dd></div>
                    <div><dt class="text-xs font-black uppercase tracking-wide text-slate-400">Status</dt><dd class="mt-1"><span class="rounded-full px-2.5 py-1 text-xs font-black {{ $managedUser->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span></dd></div>
                    <div><dt class="text-xs font-black uppercase tracking-wide text-slate-400">Created</dt><dd class="mt-1 font-bold text-slate-900">{{ $managedUser->created_at?->format('d M Y, h:i A') }}</dd></div>
                    <div><dt class="text-xs font-black uppercase tracking-wide text-slate-400">Last updated</dt><dd class="mt-1 font-bold text-slate-900">{{ $managedUser->updated_at?->format('d M Y, h:i A') }}</dd></div>
                </dl>
            </section>

            <aside class="rounded-2xl border border-blue-100 bg-blue-50 p-6">
                <p class="text-xs font-black uppercase tracking-[0.14em] text-blue-500">Access guidance</p>
                <h2 class="mt-3 text-lg font-black text-blue-950">Role permissions</h2>
                <p class="mt-2 text-sm leading-6 text-blue-900/80">This user's role controls which dashboard they can access. Inactive accounts cannot sign in or continue using protected pages.</p>
                @if (auth()->user()->is($managedUser))
                    <div class="mt-5 rounded-xl border border-blue-100 bg-white/70 p-4 text-sm text-blue-950">
                        <p class="font-black">Your account</p>
                        <p class="mt-1 text-blue-900/80">Self-deactivation and self-demotion are blocked for safety.</p>
                    </div>
                @endif
            </aside>
        </div>
    </div>
@endsection
