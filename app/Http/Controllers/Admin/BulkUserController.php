<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AcademicProfileService;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkUserController extends Controller
{
    public function index(
        Request $request,
        UserManagementService $userManagement,
        AcademicProfileService $academicProfile
    ): View {
        return view('admin.users.bulk', [
            'roleOptions' => $userManagement->roles(),
            'statusOptions' => $userManagement->statuses(),
            'boardOptions' => $academicProfile->boards(),
            'standardOptions' => $academicProfile->standards(),
            'academicYearOptions' => $academicProfile->academicYears(),
            'users' => User::query()->orderBy('name')->limit(250)->get(['id', 'name', 'email', 'role', 'status']),
        ]);
    }

    public function import(
        Request $request,
        UserManagementService $userManagement,
        AcademicProfileService $academicProfile
    ): RedirectResponse {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $actor = $request->user();
        $handle = fopen($request->file('csv_file')->getRealPath(), 'rb');

        if (! $handle) {
            throw ValidationException::withMessages(['csv_file' => 'The CSV file could not be opened.']);
        }

        $headers = fgetcsv($handle);
        $headers = array_map(fn ($value) => strtolower(trim((string) $value)), $headers ?: []);
        $requiredHeaders = ['name', 'email', 'role', 'status'];

        foreach ($requiredHeaders as $requiredHeader) {
            if (! in_array($requiredHeader, $headers, true)) {
                fclose($handle);
                throw ValidationException::withMessages([
                    'csv_file' => "Missing required CSV column: {$requiredHeader}.",
                ]);
            }
        }

        $created = 0;
        $updated = 0;
        $rowNumber = 1;

        DB::transaction(function () use (
            $handle,
            $headers,
            $actor,
            $userManagement,
            $academicProfile,
            &$created,
            &$updated,
            &$rowNumber
        ): void {
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;

                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $row = array_pad($row, count($headers), null);
                $data = array_combine($headers, array_slice($row, 0, count($headers)));
                $email = strtolower(trim((string) ($data['email'] ?? '')));
                $existing = User::query()->where('email', $email)->first();

                $rules = [
                    'name' => ['required', 'string', 'max:100'],
                    'email' => ['required', 'email', 'max:255'],
                    'role' => ['required', Rule::in(array_keys($userManagement->roles()))],
                    'status' => ['required', Rule::in(array_keys($userManagement->statuses()))],
                    'password' => [$existing ? 'nullable' : 'required', 'nullable', 'string', 'min:8'],
                    'education_board' => ['nullable', Rule::in(array_keys($academicProfile->boards()))],
                    'standard' => ['nullable', Rule::in(array_keys($academicProfile->standards()))],
                    'academic_year' => ['nullable', Rule::in(array_keys($academicProfile->academicYears()))],
                ];

                $validator = Validator::make($data, $rules);

                if ($validator->fails()) {
                    throw ValidationException::withMessages([
                        'csv_file' => "Row {$rowNumber}: ".implode(' ', $validator->errors()->all()),
                    ]);
                }

                $validated = $validator->validated();

                if (($existing?->isSuperAdmin() || $validated['role'] === User::ROLE_SUPER_ADMIN) && ! $actor?->isSuperAdmin()) {
                    throw ValidationException::withMessages([
                        'csv_file' => "Row {$rowNumber}: only a Super Administrator can import or modify Super Administrator accounts.",
                    ]);
                }

                $attributes = [
                    'name' => trim($validated['name']),
                    'role' => $validated['role'],
                    'status' => $validated['status'],
                    'education_board' => $validated['education_board'] ?: null,
                    'standard' => $validated['standard'] ?: null,
                    'academic_year' => $validated['academic_year'] ?: null,
                    'suspended_at' => $validated['status'] === User::STATUS_SUSPENDED ? ($existing?->suspended_at ?? now()) : null,
                ];

                if (filled($validated['password'] ?? null)) {
                    $attributes['password'] = Hash::make($validated['password']);
                }

                if ($existing) {
                    if ($existing->is($actor) && $validated['status'] !== User::STATUS_ACTIVE) {
                        throw ValidationException::withMessages([
                            'csv_file' => "Row {$rowNumber}: you cannot deactivate your own signed-in account.",
                        ]);
                    }

                    $existing->update($attributes);
                    $updated++;
                } else {
                    User::create(array_merge($attributes, ['email' => $email]));
                    $created++;
                }
            }
        });

        fclose($handle);

        return redirect()
            ->route('admin.users.bulk.index')
            ->with('status', "Bulk import complete: {$created} created, {$updated} updated.");
    }

    public function export(): StreamedResponse
    {
        $filename = 'signgyaan-users-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['name', 'email', 'role', 'status', 'education_board', 'standard', 'academic_year']);

            User::query()->orderBy('id')->chunk(200, function ($users) use ($output): void {
                foreach ($users as $user) {
                    fputcsv($output, [
                        $user->name,
                        $user->email,
                        $user->role,
                        $user->status,
                        $user->education_board,
                        $user->standard,
                        $user->academic_year,
                    ]);
                }
            });

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function action(Request $request, UserManagementService $userManagement): RedirectResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'bulk_action' => ['required', Rule::in(['status', 'role'])],
            'status' => ['nullable', Rule::in(array_keys($userManagement->statuses()))],
            'role' => ['nullable', Rule::in(array_keys($userManagement->roles()))],
        ]);

        $actor = $request->user();
        $users = User::query()->whereIn('id', $validated['user_ids'])->get();

        if ($validated['bulk_action'] === 'role' && ! $actor?->isSuperAdmin()) {
            abort(403, 'Only a Super Administrator can change roles in bulk.');
        }

        $changed = 0;

        DB::transaction(function () use ($users, $validated, $actor, &$changed): void {
            foreach ($users as $user) {
                if ($user->isSuperAdmin() && ! $actor?->isSuperAdmin()) {
                    continue;
                }

                if ($validated['bulk_action'] === 'status') {
                    $status = $validated['status'] ?? null;
                    if (! $status || ($user->is($actor) && $status !== User::STATUS_ACTIVE)) {
                        continue;
                    }

                    $user->update([
                        'status' => $status,
                        'suspended_at' => $status === User::STATUS_SUSPENDED ? ($user->suspended_at ?? now()) : null,
                    ]);
                    $changed++;
                } else {
                    $role = $validated['role'] ?? null;
                    if (! $role || ($user->is($actor) && ! in_array($role, [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN], true))) {
                        continue;
                    }

                    $user->update(['role' => $role]);
                    $changed++;
                }
            }
        });

        return redirect()
            ->route('admin.users.bulk.index')
            ->with('status', "Bulk action applied to {$changed} user account(s).");
    }
}
