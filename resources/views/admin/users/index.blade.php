@extends('admin.layout')

@section('title', 'Users & Roles')

@section('content')
    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Access Management</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Users & Roles</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Search accounts, review access, change roles, and activate or deactivate users.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white shadow-sm hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Create user</a>
    </div>

    <section class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="User account summary">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Total users</p><p class="mt-2 text-3xl font-black">{{ $stats['total'] }}</p></article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Active</p><p class="mt-2 text-3xl font-black">{{ $stats['active'] }}</p></article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Inactive</p><p class="mt-2 text-3xl font-black">{{ $stats['inactive'] }}</p></article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"><p class="text-sm font-bold text-slate-500">Active admins</p><p class="mt-2 text-3xl font-black">{{ $stats['admins'] }}</p></article>
    </section>

    <section class="mt-7 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="filter-heading">
        <h2 id="filter-heading" class="text-lg font-black">Search & filter</h2>
        <form method="GET" action="{{ route('admin.users.index') }}" class="mt-4 grid gap-4 lg:grid-cols-[minmax(220px,1fr)_180px_180px_auto]">
            <div>
                <label for="search" class="text-sm font-bold text-slate-700">Name or email</label>
                <input id="search" name="search" value="{{ $filters['search'] }}" type="search" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100" placeholder="Search users">
            </div>
            <div>
                <label for="role" class="text-sm font-bold text-slate-700">Role</label>
                <select id="role" name="role" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                    <option value="">All roles</option>
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" @selected($filters['role'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="text-sm font-bold text-slate-700">Status</label>
                <select id="status" name="status" class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-cyan-100">
                    <option value="">All statuses</option>
                    <option value="active" @selected($filters['status'] === 'active')>Active</option>
                    <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="rounded-xl bg-slate-950 px-4 py-3 text-sm font-black text-white focus:outline-none focus:ring-4 focus:ring-cyan-200">Apply</button>
                <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-cyan-200">Reset</a>
            </div>
        </form>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="users-heading">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 id="users-heading" class="text-lg font-black">User accounts</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-black uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Joined</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $managedUser)
                        <tr class="align-middle">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.users.show', $managedUser) }}" class="font-black text-slate-950 hover:text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-100">{{ $managedUser->name }}</a>
                                <p class="mt-1 text-xs text-slate-500">{{ $managedUser->email }}</p>
                            </td>
                            <td class="px-5 py-4"><span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-black text-blue-700">{{ $roles[$managedUser->role] ?? ucfirst($managedUser->role) }}</span></td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-black {{ $managedUser->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ $managedUser->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $managedUser->created_at?->format('d M Y') }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $managedUser) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-cyan-100">Edit</a>
                                    @if (! auth()->user()->is($managedUser))
                                        <form method="POST" action="{{ route('admin.users.status', $managedUser) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded-lg px-3 py-2 text-xs font-black focus:outline-none focus:ring-4 focus:ring-cyan-100 {{ $managedUser->is_active ? 'bg-rose-50 text-rose-700 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">{{ $managedUser->is_active ? 'Deactivate' : 'Activate' }}</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-sm font-semibold text-slate-500">No users match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $users->links() }}</div>
        @endif
    </section>
@endsection
