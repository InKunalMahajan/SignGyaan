@extends('layouts.admin')

@section('title', 'Users - SignGyaan Admin')
@section('page-title', 'Users')
@section('description', 'Manage SignGyaan users, account status, verification and learning activity.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10" data-admin-users-dashboard>
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">User management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Users Dashboard</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Monitor user accounts, roles, account status, email verification and learning activity from one place.</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-sign-soft px-4 py-3 text-sm text-sign-muted">
                    <span class="font-semibold text-sign-primary">{{ $newUsersThisWeek }}</span> new {{ \Illuminate\Support\Str::plural('user', $newUsersThisWeek) }} in the last 7 days
                </div>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Total users</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $totalUsers }}</p>
                    <p class="mt-2 text-xs text-sign-muted">{{ $learnerCount }} learners · {{ $adminCount }} admins</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Active accounts</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $activeCount }}</p>
                    <p class="mt-2 text-xs text-sign-muted">{{ $suspendedCount }} suspended · {{ $disabledCount }} disabled</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Email verified</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $verifiedCount }}</p>
                    <p class="mt-2 text-xs text-sign-muted">{{ max(0, $totalUsers - $verifiedCount) }} awaiting verification</p>
                </div>
                <div class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                    <p class="text-sm font-semibold text-sign-muted">Learning activity</p>
                    <p class="mt-2 font-heading text-3xl font-semibold text-sign-primary">{{ $usersWithProgress }}</p>
                    <p class="mt-2 text-xs text-sign-muted">Users with saved progress</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-7 rounded-2xl border border-sign-border bg-white p-4 sm:rounded-3xl sm:p-5" role="search" aria-label="Filter users">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    <div class="xl:col-span-2">
                        <label for="user-search" class="mb-2 block text-sm font-semibold text-sign-primary">Search users</label>
                        <input id="user-search" type="search" name="q" value="{{ request('q') }}" placeholder="Search by name or email" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                    </div>

                    <div>
                        <label for="user-role" class="mb-2 block text-sm font-semibold text-sign-primary">Role</label>
                        <select id="user-role" name="role" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All roles</option>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="user-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status</label>
                        <select id="user-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All statuses</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="user-verification" class="mb-2 block text-sm font-semibold text-sign-primary">Email verification</label>
                        <select id="user-verification" name="verification" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="">All users</option>
                            <option value="verified" @selected(request('verification') === 'verified')>Verified</option>
                            <option value="unverified" @selected(request('verification') === 'unverified')>Unverified</option>
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

                    <div>
                        <label for="user-sort" class="mb-2 block text-sm font-semibold text-sign-primary">Sort by</label>
                        <select id="user-sort" name="sort" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                            <option value="newest" @selected(request('sort', 'newest') === 'newest')>Newest first</option>
                            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
                            <option value="name" @selected(request('sort') === 'name')>Name A–Z</option>
                            <option value="last_login" @selected(request('sort') === 'last_login')>Recent login</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2 border-t border-sign-border pt-4">
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sign-dark">Apply filters</button>
                    @if (request()->hasAny(['q', 'role', 'status', 'verification', 'activity', 'sort']))
                        <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear filters</a>
                    @endif
                </div>
            </form>

            <div class="mt-7 overflow-hidden rounded-2xl border border-sign-border bg-white sm:rounded-3xl">
                <div class="flex flex-col gap-2 border-b border-sign-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-heading text-xl font-semibold text-sign-primary">User accounts</h3>
                        <p class="mt-1 text-xs text-sign-muted">Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} matching users.</p>
                    </div>
                </div>

                @if ($users->count())
                    <div class="hidden overflow-x-auto lg:block">
                        <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                            <thead class="bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                <tr>
                                    <th scope="col" class="px-5 py-4">User</th>
                                    <th scope="col" class="px-5 py-4">Role</th>
                                    <th scope="col" class="px-5 py-4">Status</th>
                                    <th scope="col" class="px-5 py-4">Verification</th>
                                    <th scope="col" class="px-5 py-4">Learning</th>
                                    <th scope="col" class="px-5 py-4">Last login</th>
                                    <th scope="col" class="px-5 py-4 text-right">Actions</th>
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
                                                    <p class="mt-1 text-xs text-sign-muted">Joined {{ $managedUser->created_at?->format('d M Y') }}</p>
                                                    @if ($managedUser->is(auth()->user()))
                                                        <span class="mt-1 inline-flex text-[11px] font-semibold text-sign-cyan-dark">Current account</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-sign-light text-sign-primary' => $managedUser->isAdmin(),
                                                'bg-gray-100 text-sign-muted' => $managedUser->isLearner(),
                                            ])>{{ $roleOptions[$managedUser->role] ?? ucfirst($managedUser->role) }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span @class([
                                                'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-emerald-50 text-emerald-700' => $managedUser->isActive(),
                                                'bg-amber-50 text-amber-700' => $managedUser->isSuspended(),
                                                'bg-red-50 text-red-700' => $managedUser->isDisabled(),
                                            ])>{{ $statusOptions[$managedUser->status] ?? ucfirst($managedUser->status) }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            @if ($managedUser->email_verified_at)
                                                <span class="font-semibold text-emerald-700">Verified</span>
                                                <p class="mt-1 text-xs text-sign-muted">{{ $managedUser->email_verified_at->format('d M Y') }}</p>
                                            @else
                                                <span class="font-semibold text-amber-700">Unverified</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4">
                                            <p class="font-semibold text-sign-text">{{ $managedUser->learning_progress_count }} {{ \Illuminate\Support\Str::plural('course', $managedUser->learning_progress_count) }}</p>
                                            <p class="mt-1 text-xs text-sign-muted">{{ $managedUser->learning_progress_max_last_accessed_at ? \Illuminate\Support\Carbon::parse($managedUser->learning_progress_max_last_accessed_at)->diffForHumans() : 'No learning activity' }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-sign-muted">
                                            {{ $managedUser->last_login_at?->diffForHumans() ?? 'Not recorded' }}
                                        </td>
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
                                        <h3 class="font-heading text-xl font-semibold text-sign-primary">{{ $managedUser->name }}</h3>
                                        <p class="mt-1 break-all text-xs text-sign-muted">{{ $managedUser->email }}</p>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-sign-light px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $roleOptions[$managedUser->role] ?? ucfirst($managedUser->role) }}</span>
                                            <span @class([
                                                'rounded-full px-2.5 py-1 text-xs font-semibold',
                                                'bg-emerald-50 text-emerald-700' => $managedUser->isActive(),
                                                'bg-amber-50 text-amber-700' => $managedUser->isSuspended(),
                                                'bg-red-50 text-red-700' => $managedUser->isDisabled(),
                                            ])>{{ $statusOptions[$managedUser->status] ?? ucfirst($managedUser->status) }}</span>
                                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-sign-muted">{{ $managedUser->email_verified_at ? 'Verified email' : 'Unverified email' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                                    <div class="rounded-xl bg-sign-soft p-3">
                                        <dt class="text-xs font-semibold text-sign-muted">Tracked courses</dt>
                                        <dd class="mt-1 font-semibold text-sign-primary">{{ $managedUser->learning_progress_count }}</dd>
                                    </div>
                                    <div class="rounded-xl bg-sign-soft p-3">
                                        <dt class="text-xs font-semibold text-sign-muted">Last login</dt>
                                        <dd class="mt-1 font-semibold text-sign-primary">{{ $managedUser->last_login_at?->diffForHumans() ?? 'Not recorded' }}</dd>
                                    </div>
                                </dl>

                                <div class="mt-4 flex flex-wrap justify-end gap-2 border-t border-sign-border pt-4">
                                    <a href="{{ route('admin.users.edit', $managedUser) }}" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-sign-border px-4 py-2 text-xs font-semibold text-sign-primary">Manage</a>
                                    @unless ($managedUser->is(auth()->user()))
                                        <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" onsubmit="return confirm('Delete this user account? Their saved learning progress will also be removed.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-red-200 px-4 py-2 text-xs font-semibold text-red-700">Delete</button>
                                        </form>
                                    @endunless
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center sm:p-12">
                        <h3 class="font-heading text-2xl font-semibold text-sign-primary">No users found</h3>
                        <p class="mt-2 text-sm leading-6 text-sign-muted">Try changing or clearing the current search and filters.</p>
                        <a href="{{ route('admin.users.index') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border px-4 py-2.5 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Clear filters</a>
                    </div>
                @endif
            </div>

            @if ($users->hasPages())
                <nav class="mt-6" aria-label="User pagination">{{ $users->links() }}</nav>
            @endif
        </div>
    </section>
@endsection
