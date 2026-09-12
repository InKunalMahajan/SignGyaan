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

<details class="group relative">
    <summary class="flex cursor-pointer list-none items-center gap-3 rounded-xl px-2 py-1.5 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-900" aria-label="Open account menu for {{ $user->name }}">
        <span class="grid size-9 place-items-center rounded-full bg-slate-950 text-xs font-black text-white" aria-hidden="true">{{ $initials ?: 'SG' }}</span>
        <span class="hidden text-left sm:block">
            <span class="block max-w-44 truncate text-sm font-black">{{ $user->name }}</span>
            <span class="block text-xs font-semibold text-slate-500">{{ $roleLabel }} account</span>
        </span>
        <span class="text-slate-400 transition group-open:rotate-180" aria-hidden="true">⌄</span>
    </summary>

    <div class="absolute right-0 z-50 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
        <div class="border-b border-slate-200 px-3 pb-2 pt-1">
            <p class="truncate text-sm font-black text-slate-950">{{ $user->name }}</p>
            <p class="mt-0.5 text-xs font-semibold text-slate-500">{{ $roleLabel }} account</p>
        </div>
        <a href="{{ $profileRoute }}" class="mt-1 block rounded-lg px-3 py-2.5 text-sm font-bold text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-900">Profile</a>
        <a href="{{ $settingsRoute }}" class="block rounded-lg px-3 py-2.5 text-sm font-bold text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-900">Settings</a>
        <div class="my-1 border-t border-slate-200"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full rounded-lg px-3 py-2.5 text-left text-sm font-bold text-slate-800 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-900">Sign out</button>
        </form>
    </div>
</details>
