@extends('layouts.admin')

@section('title', 'Academic Profile - SignGyaan Admin')
@section('page-title', 'Academic Profile')
@section('description', 'Manage a learner board, standard and academic year.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-4xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">User management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Academic Profile</h2>
                    <p class="mt-2 text-sm text-sign-muted">{{ $managedUser->name }} · {{ $managedUser->email }}</p>
                </div>
                <a href="{{ route('admin.users.edit', $managedUser) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">← Back to User</a>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status" aria-live="polite">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert" aria-live="assertive">
                    <p class="text-sm font-semibold text-red-800">Please check the academic profile details.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-7 grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem] lg:items-start">
                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="academic-profile-heading">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Curriculum profile</p>
                    <h3 id="academic-profile-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Board & Academic Details</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Use these details to prepare SignGyaan for board-specific learning, recommendations and progress reporting.</p>

                    <form method="POST" action="{{ route('admin.users.academic-profile.update', $managedUser) }}" class="mt-6 space-y-5">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="education_board" class="mb-2 block text-sm font-semibold text-sign-primary">Education board</label>
                            <select id="education_board" name="education_board" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                <option value="">Not selected</option>
                                @foreach ($boardOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('education_board', $managedUser->education_board) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('education_board')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="standard" class="mb-2 block text-sm font-semibold text-sign-primary">Class / Standard</label>
                            <select id="standard" name="standard" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                <option value="">Not selected</option>
                                @foreach ($standardOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('standard', $managedUser->standard) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('standard')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="academic_year" class="mb-2 block text-sm font-semibold text-sign-primary">Academic year</label>
                            <select id="academic_year" name="academic_year" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text outline-none transition focus:border-sign-cyan focus:ring-4 focus:ring-sign-light">
                                <option value="">Not selected</option>
                                @foreach ($academicYearOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('academic_year', $managedUser->academic_year) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('academic_year')<p class="mt-2 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex flex-col gap-3 border-t border-sign-border pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs leading-5 text-sign-muted">All three fields are optional until the learner's curriculum details are known.</p>
                            <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Save Academic Profile</button>
                        </div>
                    </form>
                </section>

                <aside class="rounded-2xl border border-sign-border bg-sign-soft p-5 sm:rounded-3xl" aria-label="Academic profile summary">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Current profile</p>
                    <dl class="mt-4 space-y-4 text-sm">
                        <div>
                            <dt class="text-xs text-sign-muted">Board</dt>
                            <dd class="mt-1 font-semibold text-sign-primary">{{ $boardOptions[$managedUser->education_board] ?? 'Not selected' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-sign-muted">Class / Standard</dt>
                            <dd class="mt-1 font-semibold text-sign-primary">{{ $standardOptions[$managedUser->standard] ?? 'Not selected' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-sign-muted">Academic year</dt>
                            <dd class="mt-1 font-semibold text-sign-primary">{{ $managedUser->academic_year ?: 'Not selected' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-5 rounded-xl bg-white p-4 text-sm leading-6 text-sign-muted">
                        @if ($managedUser->hasAcademicProfile())
                            Academic profile is complete.
                        @else
                            Academic profile is incomplete. It can be completed later without blocking account access.
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endsection
