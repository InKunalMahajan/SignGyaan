<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AcademicProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserAcademicProfileController extends Controller
{
    public function edit(Request $request, User $user, AcademicProfileService $academicProfiles): View
    {
        if ($user->isSuperAdmin() && ! $request->user()?->isSuperAdmin()) {
            abort(403, 'Only a Super Administrator can manage another Super Administrator.');
        }

        return view('admin.users.academic-profile', [
            'managedUser' => $user,
            'boardOptions' => $academicProfiles->boards(),
            'standardOptions' => $academicProfiles->standards(),
            'academicYearOptions' => $academicProfiles->academicYears(),
        ]);
    }

    public function update(Request $request, User $user, AcademicProfileService $academicProfiles): RedirectResponse
    {
        if ($user->isSuperAdmin() && ! $request->user()?->isSuperAdmin()) {
            abort(403, 'Only a Super Administrator can manage another Super Administrator.');
        }

        $validated = $request->validate([
            'education_board' => ['nullable', Rule::in(array_keys($academicProfiles->boards()))],
            'standard' => ['nullable', Rule::in(array_keys($academicProfiles->standards()))],
            'academic_year' => ['nullable', Rule::in(array_keys($academicProfiles->academicYears()))],
        ]);

        $user->update([
            'education_board' => $validated['education_board'] ?: null,
            'standard' => $validated['standard'] ?: null,
            'academic_year' => $validated['academic_year'] ?: null,
        ]);

        return redirect()
            ->route('admin.users.academic-profile.edit', $user)
            ->with('status', 'Academic profile updated successfully.');
    }
}
