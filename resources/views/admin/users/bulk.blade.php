@extends('layouts.admin')

@section('title', 'Bulk User Management - SignGyaan Admin')
@section('page-title', 'Bulk User Management')
@section('description', 'Import, export and update multiple SignGyaan user accounts safely.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">User management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Bulk User Management</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">Import users from CSV, export account data, or apply status and role changes to selected accounts.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">← Back to Users</a>
            </div>

            @if (session('status'))
                <div class="mt-6 rounded-2xl border border-sign-cyan bg-sign-light px-4 py-3 text-sm font-semibold text-sign-primary" role="status" aria-live="polite">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert">
                    <p class="text-sm font-semibold text-red-800">Please check the bulk user request.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="mt-7 grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="csv-import-heading">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">CSV import</p>
                    <h3 id="csv-import-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Import or update users</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Existing users are matched by email. New users require a password of at least 8 characters.</p>

                    <div class="mt-5 overflow-x-auto rounded-xl bg-sign-soft p-4 text-xs text-sign-muted">
                        <p class="font-semibold text-sign-primary">Required columns</p>
                        <code class="mt-2 block whitespace-nowrap">name,email,role,status</code>
                        <p class="mt-3 font-semibold text-sign-primary">Optional columns</p>
                        <code class="mt-2 block whitespace-nowrap">password,education_board,standard,academic_year</code>
                    </div>

                    <form method="POST" action="{{ route('admin.users.bulk.import') }}" enctype="multipart/form-data" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label for="csv-file" class="mb-2 block text-sm font-semibold text-sign-primary">CSV file</label>
                            <input id="csv-file" name="csv_file" type="file" accept=".csv,text/csv,text/plain" required class="block min-h-12 w-full rounded-xl border border-sign-border bg-white px-3 py-3 text-sm text-sign-text file:mr-3 file:rounded-lg file:border-0 file:bg-sign-light file:px-3 file:py-2 file:font-semibold file:text-sign-primary">
                            <p class="mt-2 text-xs text-sign-muted">Maximum file size: 2 MB.</p>
                        </div>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Import CSV</button>
                    </form>
                </section>

                <section class="rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="csv-export-heading">
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">CSV export</p>
                    <h3 id="csv-export-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Export user data</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Download account identity, role, status and academic-profile fields. Passwords and private preference data are never exported.</p>
                    <a href="{{ route('admin.users.bulk.export') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-primary bg-white px-5 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-light">Download Users CSV</a>

                    <div class="mt-7 rounded-2xl border border-sign-border bg-sign-soft p-4">
                        <p class="text-sm font-semibold text-sign-primary">Accepted values</p>
                        <p class="mt-2 text-xs leading-5 text-sign-muted"><strong>Roles:</strong> {{ implode(', ', array_keys($roleOptions)) }}</p>
                        <p class="mt-1 text-xs leading-5 text-sign-muted"><strong>Status:</strong> {{ implode(', ', array_keys($statusOptions)) }}</p>
                        <p class="mt-1 text-xs leading-5 text-sign-muted"><strong>Boards:</strong> {{ implode(', ', array_keys($boardOptions)) }}</p>
                    </div>
                </section>
            </div>

            <section class="mt-7 rounded-2xl border border-sign-border bg-white p-5 sm:rounded-3xl sm:p-7" aria-labelledby="bulk-actions-heading">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-sign-cyan-dark">Bulk actions</p>
                    <h3 id="bulk-actions-heading" class="mt-2 font-heading text-2xl font-semibold text-sign-primary">Update selected accounts</h3>
                    <p class="mt-2 text-sm leading-6 text-sign-muted">Select up to the first 250 users shown below. Your own account cannot be deactivated. Role changes in bulk require Super Administrator access.</p>
                </div>

                <form method="POST" action="{{ route('admin.users.bulk.action') }}" class="mt-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="bulk-action" class="mb-2 block text-sm font-semibold text-sign-primary">Action</label>
                            <select id="bulk-action" name="bulk_action" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text">
                                <option value="status">Change account status</option>
                                @if (auth()->user()->isSuperAdmin())<option value="role">Change role</option>@endif
                            </select>
                        </div>
                        <div>
                            <label for="bulk-status" class="mb-2 block text-sm font-semibold text-sign-primary">Status value</label>
                            <select id="bulk-status" name="status" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text">
                                <option value="">Choose status</option>
                                @foreach ($statusOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                            </select>
                        </div>
                        @if (auth()->user()->isSuperAdmin())
                            <div>
                                <label for="bulk-role" class="mb-2 block text-sm font-semibold text-sign-primary">Role value</label>
                                <select id="bulk-role" name="role" class="min-h-12 w-full rounded-xl border border-sign-border bg-white px-4 py-3 text-base text-sign-text">
                                    <option value="">Choose role</option>
                                    @foreach ($roleOptions as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach
                                </select>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 overflow-hidden rounded-2xl border border-sign-border">
                        <div class="max-h-[30rem] overflow-auto">
                            <table class="min-w-full divide-y divide-sign-border text-left text-sm">
                                <thead class="sticky top-0 bg-sign-soft text-xs font-semibold uppercase tracking-wider text-sign-muted">
                                    <tr><th class="px-4 py-3">Select</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Status</th></tr>
                                </thead>
                                <tbody class="divide-y divide-sign-border bg-white">
                                    @foreach ($users as $managedUser)
                                        <tr>
                                            <td class="px-4 py-3"><input type="checkbox" name="user_ids[]" value="{{ $managedUser->id }}" class="h-5 w-5 rounded border-sign-border text-sign-primary focus:ring-sign-cyan" aria-label="Select {{ $managedUser->name }}"></td>
                                            <td class="px-4 py-3"><p class="font-semibold text-sign-primary">{{ $managedUser->name }}</p><p class="mt-1 text-xs text-sign-muted">{{ $managedUser->email }}</p></td>
                                            <td class="px-4 py-3 text-sign-muted">{{ $roleOptions[$managedUser->role] ?? ucfirst($managedUser->role) }}</td>
                                            <td class="px-4 py-3 text-sign-muted">{{ $statusOptions[$managedUser->status] ?? ucfirst($managedUser->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Apply Bulk Action</button>
                    </div>
                </form>
            </section>
        </div>
    </section>
@endsection
