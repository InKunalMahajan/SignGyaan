<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    private const ROLE_LABELS = [
        'learner' => 'Learner',
        'parents' => 'Parents',
        'teacher' => 'Teacher',
        'admin' => 'Admin',
    ];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = in_array($request->query('role'), User::ROLES, true)
            ? $request->query('role')
            : null;
        $status = in_array($request->query('status'), ['active', 'inactive'], true)
            ? $request->query('status')
            : null;

        $users = User::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($query, $role) => $query->where('role', $role))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'admins' => User::where('role', 'admin')->where('is_active', true)->count(),
        ];

        return view('admin.users.index', [
            'users' => $users,
            'stats' => $stats,
            'roles' => self::ROLE_LABELS,
            'filters' => compact('search', 'role', 'status'),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => self::ROLE_LABELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['required', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $user = User::create($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User account created successfully.');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'managedUser' => $user,
            'roles' => self::ROLE_LABELS,
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => self::ROLE_LABELS,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            throw ValidationException::withMessages([
                'role' => 'You cannot remove your own Admin role.',
            ]);
        }

        if ($user->is($request->user()) && ! $validated['is_active']) {
            throw ValidationException::withMessages([
                'is_active' => 'You cannot deactivate your own account.',
            ]);
        }

        if ($this->wouldRemoveLastActiveAdmin($user, $validated['role'], $validated['is_active'])) {
            throw ValidationException::withMessages([
                'role' => 'At least one active Admin account must remain.',
            ]);
        }

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User account updated successfully.');
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors([
                'status' => 'You cannot deactivate your own account.',
            ]);
        }

        $newStatus = ! $user->is_active;

        if (! $newStatus && $this->wouldRemoveLastActiveAdmin($user, $user->role, false)) {
            return back()->withErrors([
                'status' => 'At least one active Admin account must remain.',
            ]);
        }

        $user->update(['is_active' => $newStatus]);

        return back()->with(
            'success',
            $newStatus ? 'User account activated.' : 'User account deactivated.'
        );
    }

    private function wouldRemoveLastActiveAdmin(User $user, string $newRole, bool $newStatus): bool
    {
        if (! $user->hasRole('admin') || ! $user->is_active) {
            return false;
        }

        if ($newRole === 'admin' && $newStatus) {
            return false;
        }

        return User::where('role', 'admin')
            ->where('is_active', true)
            ->count() <= 1;
    }
}
