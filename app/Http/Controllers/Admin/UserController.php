<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request, UserManagementService $userManagement): View
    {
        $query = User::query()
            ->withCount('learningProgress')
            ->withMax('learningProgress', 'last_accessed_at');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));

            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $role = (string) $request->input('role', '');
        if (array_key_exists($role, $userManagement->roles())) {
            $query->where('role', $role);
        }

        $status = (string) $request->input('status', '');
        if (array_key_exists($status, $userManagement->statuses())) {
            $query->where('status', $status);
        }

        $verification = (string) $request->input('verification', '');
        if ($verification === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($verification === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        if ($request->input('activity') === 'tracked') {
            $query->has('learningProgress');
        } elseif ($request->input('activity') === 'none') {
            $query->doesntHave('learningProgress');
        }

        $sort = (string) $request->input('sort', 'newest');
        match ($sort) {
            'oldest' => $query->oldest('created_at'),
            'name' => $query->orderBy('name'),
            'last_login' => $query->orderByRaw('last_login_at IS NULL, last_login_at DESC'),
            default => $query->latest('created_at'),
        };

        $now = now();

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
            'roleOptions' => $userManagement->roles(),
            'statusOptions' => $userManagement->statuses(),
            'totalUsers' => User::query()->count(),
            'learnerCount' => User::query()->where('role', User::ROLE_LEARNER)->count(),
            'teacherCount' => User::query()->where('role', User::ROLE_TEACHER)->count(),
            'adminCount' => User::query()->where('role', User::ROLE_ADMIN)->count(),
            'superAdminCount' => User::query()->where('role', User::ROLE_SUPER_ADMIN)->count(),
            'activeCount' => User::query()->where('status', User::STATUS_ACTIVE)->count(),
            'suspendedCount' => User::query()->where('status', User::STATUS_SUSPENDED)->count(),
            'disabledCount' => User::query()->where('status', User::STATUS_DISABLED)->count(),
            'verifiedCount' => User::query()->whereNotNull('email_verified_at')->count(),
            'usersWithProgress' => User::query()->has('learningProgress')->count(),
            'newUsersThisWeek' => User::query()->where('created_at', '>=', $now->copy()->subDays(7))->count(),
        ]);
    }

    public function edit(User $user, UserManagementService $userManagement): View
    {
        $user->load([
            'learningProgress' => fn ($query) => $query->orderByDesc('last_accessed_at'),
        ])->loadCount([
            'learningActivities',
            'assessmentAttempts',
        ]);

        $completedLessons = $user->learningProgress
            ->sum(fn ($progress) => $progress->completedLessonsCount());

        return view('admin.users.edit', [
            'managedUser' => $user,
            'completedLessons' => $completedLessons,
            'completedCourses' => $user->learningProgress->whereNotNull('completed_at')->count(),
            'adminCount' => $userManagement->administratorCount(),
            'roleOptions' => $userManagement->roles(),
            'statusOptions' => $userManagement->statuses(),
        ]);
    }

    public function update(Request $request, User $user, UserManagementService $userManagement): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(array_keys($userManagement->roles()))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $actor = $request->user();
        $validated['email'] = strtolower(trim($validated['email']));
        $validated['name'] = trim($validated['name']);
        $validated['admin_note'] = filled($validated['admin_note'] ?? null)
            ? trim((string) $validated['admin_note'])
            : null;

        if (($user->isSuperAdmin() || $validated['role'] === User::ROLE_SUPER_ADMIN) && ! $actor?->isSuperAdmin()) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'Only a Super Administrator can manage the Super Administrator role.']);
        }

        if ($user->is($actor) && ! in_array($validated['role'], [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN], true)) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'You cannot remove your own administrator access.']);
        }

        if ($user->isAdmin()
            && ! in_array($validated['role'], [User::ROLE_ADMIN, User::ROLE_SUPER_ADMIN], true)
            && $userManagement->administratorCount() <= 1) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'At least one administrator account must remain.']);
        }

        if ($user->isSuperAdmin()
            && $validated['role'] !== User::ROLE_SUPER_ADMIN
            && User::query()->where('role', User::ROLE_SUPER_ADMIN)->count() <= 1) {
            return back()
                ->withInput()
                ->withErrors(['role' => 'At least one Super Administrator account must remain.']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('status', 'User profile updated successfully.');
    }

    public function destroy(Request $request, User $user, UserManagementService $userManagement): RedirectResponse
    {
        $actor = $request->user();

        if ($user->is($actor)) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'You cannot delete your own signed-in administrator account.');
        }

        if ($user->isSuperAdmin() && ! $actor?->isSuperAdmin()) {
            abort(403, 'Only a Super Administrator can manage another Super Administrator.');
        }

        if ($user->isSuperAdmin() && User::query()->where('role', User::ROLE_SUPER_ADMIN)->count() <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'The final Super Administrator account cannot be deleted.');
        }

        if ($user->isAdmin() && $userManagement->administratorCount() <= 1) {
            return redirect()
                ->route('admin.users.index')
                ->with('status', 'The final administrator account cannot be deleted.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User account deleted successfully.');
    }
}
