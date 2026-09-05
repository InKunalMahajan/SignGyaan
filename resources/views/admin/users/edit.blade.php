@extends('layouts.admin')

@section('title', 'Manage User - SignGyaan Admin')
@section('page-title', 'Manage User')
@section('description', 'Review and update a SignGyaan user profile, account details and learning activity.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">User profile management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $managedUser->name }}</h2>
                    <p class="mt-2 break-all text-sm text-sign-muted">{{ $managedUser->email }}</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">← Back to Users</a>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status" aria-live="polite">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="polite">
                    <p class="text-sm font-semibold text-red-800">Please check the user details.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-7 grid gap-6 xl:grid-cols-[minmax(0,1fr)_21rem] xl:items-start">
                <div class="space-y-6">
                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="user-profile-heading">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Profile</p>
                                <h3 id="user-profile-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Profile & account details</h3>
                                <p class="mt-2 text-sm leading-6 text-sign-muted">Update the user's core profile information and platform role.</p>
                            </div>
                            @if ($managedUser->is(auth()->user()))
                                <span class="rounded-full bg-sign-light px-3 py-1.5 text-xs font-semibold text-sign-primary">Current account</span>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="mt-6">
                            @csrf
                            @method('PUT')

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label for="name" class="mb-2 block text-sm font-semibold text-sign-primary">Full name</label>
                                    <input id="name" name="name" type="text" value="{{ old('name', $managedUser->name) }}" maxlength="100" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                    @error('name')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="email" class="mb-2 block text-sm font-semibold text-sign-primary">Email address</label>
                                    <input id="email" name="email" type="email" value="{{ old('email', $managedUser->email) }}" maxlength="255" required autocomplete="off" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                    <p class="mt-2 text-xs leading-5 text-sign-muted">Email addresses must be unique across SignGyaan accounts.</p>
                                    @error('email')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="role" class="mb-2 block text-sm font-semibold text-sign-primary">Account role</label>
                                    <select id="role" name="role" required class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                        @foreach ($roleOptions as $value => $label)
                                            <option value="{{ $value }}" @selected(old('role', $managedUser->role) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('role')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-sign-primary">Account status</label>
                                    <div class="flex min-h-12 items-center rounded-xl border border-sign-border bg-sign-soft px-4 py-3 text-sm font-semibold text-sign-primary">
                                        {{ $statusOptions[$managedUser->status] ?? \Illuminate\Support\Str::headline($managedUser->status) }}
                                    </div>
                                    <p class="mt-2 text-xs leading-5 text-sign-muted">Status changes are handled in the Account Status module.</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="admin_note" class="mb-2 block text-sm font-semibold text-sign-primary">Internal admin note</label>
                                    <textarea id="admin_note" name="admin_note" rows="5" maxlength="2000" placeholder="Optional note visible only to administrators..." class="w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">{{ old('admin_note', $managedUser->admin_note) }}</textarea>
                                    <div class="mt-2 flex flex-col gap-1 text-xs text-sign-muted sm:flex-row sm:items-center sm:justify-between">
                                        <span>Use this for account-management context. Do not store passwords or sensitive secrets.</span>
                                        <span>Maximum 2,000 characters</span>
                                    </div>
                                    @error('admin_note')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                                </div>

                                <div class="sm:col-span-2 flex flex-col gap-3 border-t border-sign-border pt-5 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-xs leading-5 text-sign-muted">Password changes remain controlled by the account owner from Profile & Account.</p>
                                    <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Save Profile</button>
                                </div>
                            </div>
                        </form>
                    </section>

                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="user-learning-heading">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Learning activity</p>
                                <h3 id="user-learning-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Saved course progress</h3>
                            </div>
                            <span class="text-sm font-semibold text-sign-muted">{{ $managedUser->learningProgress->count() }} {{ \Illuminate\Support\Str::plural('course', $managedUser->learningProgress->count()) }}</span>
                        </div>

                        @if ($managedUser->learningProgress->isNotEmpty())
                            <div class="mt-6 grid gap-4 lg:grid-cols-2">
                                @foreach ($managedUser->learningProgress as $progress)
                                    <article class="rounded-2xl border border-sign-border bg-sign-soft p-5">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-sign-primary">{{ $progress->subject_name }}</span>
                                            <span class="text-xs font-semibold text-sign-cyan-dark">{{ $progress->progressPercent() }}%</span>
                                        </div>
                                        <h4 class="mt-3 font-heading text-xl font-semibold text-sign-primary">{{ $progress->course_title }}</h4>
                                        <p class="mt-2 text-sm text-sign-muted">{{ $progress->completedLessonsCount() }} of {{ $progress->total_lessons }} lessons completed</p>
                                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-white" role="progressbar" aria-label="{{ $progress->course_title }} progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress->progressPercent() }}">
                                            <div class="h-full rounded-full bg-sign-primary" style="width: {{ $progress->progressPercent() }}%"></div>
                                        </div>
                                        <div class="mt-4 grid gap-1 text-xs text-sign-muted">
                                            <p><span class="font-semibold text-sign-text">Current:</span> {{ \Illuminate\Support\Str::headline($progress->current_lesson_key) }}</p>
                                            <p><span class="font-semibold text-sign-text">Last accessed:</span> {{ $progress->last_accessed_at?->diffForHumans() ?? 'Not recorded' }}</p>
                                            <p><span class="font-semibold text-sign-text">Status:</span> {{ $progress->completed_at ? 'Completed' : 'In progress' }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-6 rounded-2xl border border-dashed border-sign-border bg-sign-soft p-6 text-center">
                                <h4 class="font-heading text-xl font-semibold text-sign-primary">No saved learning progress</h4>
                                <p class="mt-2 text-sm leading-6 text-sign-muted">This user has not saved or completed a course lesson yet.</p>
                            </div>
                        @endif
                    </section>
                </div>

                <aside class="space-y-5 xl:sticky xl:top-24" aria-label="User account summary">
                    <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl">
                        <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Account summary</p>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Role</dt>
                                <dd class="font-semibold text-sign-primary">{{ $roleOptions[$managedUser->role] ?? \Illuminate\Support\Str::headline($managedUser->role) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Status</dt>
                                <dd class="font-semibold text-sign-primary">{{ $statusOptions[$managedUser->status] ?? \Illuminate\Support\Str::headline($managedUser->status) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Email</dt>
                                <dd class="font-semibold text-sign-primary">{{ $managedUser->email_verified_at ? 'Verified' : 'Unverified' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Last login</dt>
                                <dd class="text-right font-semibold text-sign-primary">{{ $managedUser->last_login_at?->diffForHumans() ?? 'Not recorded' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Tracked courses</dt>
                                <dd class="font-semibold text-sign-primary">{{ $managedUser->learningProgress->count() }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Lessons completed</dt>
                                <dd class="font-semibold text-sign-primary">{{ $completedLessons }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Courses completed</dt>
                                <dd class="font-semibold text-sign-primary">{{ $completedCourses }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Assessments</dt>
                                <dd class="font-semibold text-sign-primary">{{ $managedUser->assessment_attempts_count }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Learning events</dt>
                                <dd class="font-semibold text-sign-primary">{{ $managedUser->learning_activities_count }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 rounded-xl bg-sign-soft px-4 py-3">
                                <dt class="text-sign-muted">Joined</dt>
                                <dd class="font-semibold text-sign-primary">{{ $managedUser->created_at?->format('d M Y') }}</dd>
                            </div>
                        </dl>
                    </section>

                    @if ($managedUser->suspended_at)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm leading-6 text-amber-900">
                            Suspended {{ $managedUser->suspended_at->diffForHumans() }}. Account-status controls will be managed separately in Step 14E.
                        </div>
                    @endif

                    @if ($managedUser->is(auth()->user()))
                        <div class="rounded-2xl border border-sign-cyan bg-sign-light p-5 text-sm leading-6 text-sign-primary">
                            This is your currently signed-in administrator account. SignGyaan prevents you from removing your own admin access or deleting this account here.
                        </div>
                    @elseif ($managedUser->isAdmin() && $adminCount <= 1)
                        <div class="rounded-2xl border border-sign-cyan bg-sign-light p-5 text-sm leading-6 text-sign-primary">
                            This is the final administrator account, so its administrator role and account cannot be removed.
                        </div>
                    @else
                        <section class="rounded-2xl border border-red-200 bg-white p-5 sm:rounded-3xl">
                            <p class="text-xs font-semibold uppercase tracking-wider text-red-700">Danger zone</p>
                            <h3 class="mt-2 font-heading text-xl font-semibold text-sign-primary">Delete account</h3>
                            <p class="mt-2 text-sm leading-6 text-sign-muted">Deleting this user also removes account-linked data according to existing database relationships. This action cannot be undone.</p>
                            <form method="POST" action="{{ route('admin.users.destroy', $managedUser) }}" class="mt-4" onsubmit="return confirm('Delete this user account? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-50">Delete User</button>
                            </form>
                        </section>
                    @endif
                </aside>
            </div>
        </div>
    </section>
@endsection
