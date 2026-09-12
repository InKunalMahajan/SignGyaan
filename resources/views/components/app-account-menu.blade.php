@props(['role'])

@php
    $user = auth()->user();
    $nameParts = preg_split('/\s+/', trim($user->name));
    $initials = collect($nameParts)->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('');
    $roleLabel = $role === 'parents' ? 'Parent' : ucfirst($role);

    $profileRoute = match ($role) {
        'learner' => route('learner.profile.show'),
        'teacher' => route('teacher.profile.show'),
        'parents' => route('parents.profile.show'),
        'admin' => route('dashboard.role', 'admin'),
        default => route('dashboard'),
    };

    $settingsRoute = match ($role) {
        'learner' => route('learner.profile.show').'#settings',
        'teacher' => route('teacher.profile.show').'#settings',
        'parents' => route('parents.profile.show').'#settings',
        'admin' => route('admin.users.index'),
        default => route('dashboard'),
    };
@endphp

<details class="sg-account-menu">
    <summary aria-label="Open account menu for {{ $user->name }}">
        <span class="sg-account-avatar" aria-hidden="true">{{ $initials ?: 'SG' }}</span>
        <span class="sg-account-text">
            <span class="sg-account-name">{{ $user->name }}</span>
            <span class="sg-account-role">{{ $roleLabel }} account</span>
        </span>
        <svg class="sg-account-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
    </summary>

    <div class="sg-account-popover">
        <div class="sg-account-popover-header">
            <strong>{{ $user->name }}</strong>
            <span>{{ $roleLabel }} account</span>
        </div>

        <a class="sg-account-link" href="{{ $profileRoute }}">
            <svg class="sg-account-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0"/></svg>
            <span>Profile</span>
        </a>
        <a class="sg-account-link" href="{{ $settingsRoute }}">
            <svg class="sg-account-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06-2.83 2.83-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.04 1.56V21h-4v-.08A1.7 1.7 0 0 0 8.96 19.36a1.7 1.7 0 0 0-1.87.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 4.6 15 1.7 1.7 0 0 0 3.08 14H3v-4h.08A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 8.96 4.64 1.7 1.7 0 0 0 10 3.08V3h4v.08a1.7 1.7 0 0 0 1.04 1.56 1.7 1.7 0 0 0 1.87-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 9c.17.62.73 1 1.36 1H21v4h-.24c-.63 0-1.19.38-1.36 1Z"/></svg>
            <span>Settings</span>
        </a>
        <div class="sg-account-menu-divider"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sg-account-logout">
                <svg class="sg-account-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 19V5a2 2 0 0 0-2-2h-6"/></svg>
                <span>Sign out</span>
            </button>
        </form>
    </div>
</details>
