@extends('layouts.admin')

@section('title', 'Users - SignGyaan Admin')
@section('page-title', 'Users')
@section('description', 'Manage SignGyaan learner and administrator accounts and review learning activity.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Platform management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Users</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Review learner accounts, administrator access and saved learning activity.</p>
                </div>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Total users</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $totalUsers }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Learners</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $learnerCount }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Administrators</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $adminCount }}</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">With saved progress</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $usersWithProgress }}</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_12rem_14rem_auto] xl:items-end">
                    <div>
                        <label for="user-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search users</label>
                        <input id="user-search" type="search" name="q" value="{{ request('q') }}" placeholder="Name or email" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>

                    <div>
                        <label for="user-role" class="mb-2 block text-sm font-semibold text-sign-primary">Role</label>
                        <select id="user-role" name="role" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All roles</option>
                            <option value="learner" @selected(request('role') === 'learner')>Learner</option>
                            <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                        </select>
                    </div>

                    <div>
                        <label for="user-activity" class="mb-2 block text-sm font-semibold text-sign-primary">Learning activity</label>
                        <select id="user-activity" name="activity" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All users</option>
                            <option value="tracked" @selected(request('activity') === 'tracked')>Has saved progress</option>
                            <option value="none" @selected(request('activity') === 'none')>No saved progress</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Filter</button>
                        @if (request()->hasAny(['q', 'role', 'activity']))
                            <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-12 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                @if ($users->count())
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                            <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                <tr>
                                    <th class="px-5 py-4">User</th>
                                    <th class="px-5 py-4">Role</th>
                                    <th class="px-5 py-4">Learning</th>
                                    <th class="px-5 py-4">Last activity</th>
                                    <th class="px-5 py-4">Joined</th>
                                    <th class="px-5 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sign-border">
                                @foreach ($users as $managedUser)
                                    <tr class="align-top">
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-3">
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sign-soft font-heading font-semibold text-sign-primary" aria-hidden="true">{{ strtoupper(substr(trim($managedUser->name), 0, 1)) }}</span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-sign-primary">{{ $managedUser->name }}</p>
                                                    <p class="mt-1 break-all text-xs text-sign-muted">{{ $managedUser->email }}</p>
                                                    @if ($managedUser->is(auth()->user()))
                                                        <span class="mt-1 inline-flex text-[11px] font-semibold text-sign-cyan-dark">Current account</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $managedUser->role === 'admin',
                                                'bg-gray-100 text-sign-muted' => $managedUser->role !== 'admin',
                                            ])>{{ $managedUser->role === 'admin' ? 'Administrator' : 'Learner' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="font-semibold text-sign-text">{{ $managedUser->learning_progress_count }} {{ \Illuminate\Support\Str::plural('course', $managedUser->learning_progress_count) }}</p>
                                            <p class="mt-1 text-xs text-sign-muted">Tracked progress records</p>
                                        </td>
                                        <td class="px-5 py-4 text-sign-muted">
                                            {{ $managedUser->learning_progress_max_last_accessed_at ? \Illuminate\Support\Carbon::parse($managedUser->learning_progress_max_last_accessed_at)->diffForHumans() : 'No learning activity' }}
                                        </td>
                                        <td class="px-5 py-4 text-sign-muted">{{ $managedUser->created_at?->format('d M Y') }}</td>
                                        <td class="px-5 py-4">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.users.edit', $managedUser) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary transition hover:bg-sign-soft">Manage</a>
                                                @unless ($managedUser->is(auth()->user()))
                                                    <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Delete this user account? Their saved learning progress will also be removed.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-50">Delete</button>
                                                    </form>
                                                @endunless
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="divide-y divide-sign-border lg:hidden">
                        @foreach ($users as $managedUser)
                            <article class="p-5">
                                <div class="flex items-start gap-3">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sign-soft font-heading font-semibold text-sign-primary" aria-hidden="true">{{ strtoupper(substr(trim($managedUser->name), 0, 1)) }}</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $managedUser->name }}</h3>
                                            <span @class([
                                                'rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $managedUser->role === 'admin',
                                                'bg-gray-100 text-sign-muted' => $managedUser->role !== 'admin',
                                            ])>{{ $managedUser->role === 'admin' ? 'Administrator' : 'Learner' }}</span>
                                        </div>
                                        <p class="mt-1 break-all text-xs text-sign-muted">{{ $managedUser->email }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-xl bg-sign-soft p-3">
                                        <p class="text-xs font-semibold text-sign-muted">Tracked courses</p>
                                        <p class="mt-1 font-semibold text-sign-primary">{{ $managedUser->learning_progress_count }}</p>
                                    </div>
                                    <div class="rounded-xl bg-sign-soft p-3">
                                        <p class="text-xs font-semibold text-sign-muted">Joined</p>
                                        <p class="mt-1 font-semibold text-sign-primary">{{ $managedUser->created_at?->format('d M Y') }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap justify-end gap-2 border-t border-sign-border pt-4">
                                    <a href="{{ route('admin.users.edit', $managedUser) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-sign-border px-3 py-2 text-xs font-semibold text-sign-primary">Manage</a>
                                    @unless ($managedUser->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Delete this user account? Their saved learning progress will also be removed.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700">Delete</button>
                                        </form>
                                    @endunless
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center sm:p-12">
                        <h3 class="font-heading text-2xl font-semibold text-sign-primary">No users found</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Try clearing the current search or filters.</p>
                    </div>
                @endif
            </div>

            @if ($users->hasPages())
                <div class="mt-6">{{ $users->links() }}</div>
            @endif
        </div>
    </section>
@endsection
