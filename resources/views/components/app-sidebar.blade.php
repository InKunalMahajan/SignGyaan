@props(['role'])

@php
    $roleKey = $role === 'parent' ? 'parents' : $role;
    $dashboardRoute = route('dashboard.role', $roleKey);

    $items = match ($roleKey) {
        'learner' => [
            ['label' => 'Dashboard', 'href' => $dashboardRoute, 'active' => request()->routeIs('dashboard.role')],
            ['label' => 'My Classes', 'href' => route('learner.classes.index'), 'active' => request()->routeIs('learner.classes.*')],
            ['label' => 'My Profile', 'href' => route('learner.profile.show'), 'active' => request()->routeIs('learner.profile.*')],
            ['label' => 'Parent Access', 'href' => route('learner.parent-links.index'), 'active' => request()->routeIs('learner.parent-links.*')],
            ['label' => 'Explore Public', 'href' => route('dashboard.guest'), 'active' => false],
        ],
        'teacher' => [
            ['label' => 'Dashboard', 'href' => $dashboardRoute, 'active' => request()->routeIs('dashboard.role')],
            ['label' => 'My Classes', 'href' => route('teacher.classes.index'), 'active' => request()->routeIs('teacher.classes.*')],
            ['label' => 'Course Content', 'href' => route('teacher.courses.index'), 'active' => request()->routeIs('teacher.courses.*')],
            ['label' => 'My Profile', 'href' => route('teacher.profile.show'), 'active' => request()->routeIs('teacher.profile.*')],
            ['label' => 'Explore Public', 'href' => route('dashboard.guest'), 'active' => false],
        ],
        'parents' => [
            ['label' => 'Dashboard', 'href' => $dashboardRoute, 'active' => request()->routeIs('dashboard.role')],
            ['label' => 'Parent Profile', 'href' => route('parents.profile.show'), 'active' => request()->routeIs('parents.profile.*')],
            ['label' => 'Learner Progress', 'href' => route('parents.profile.show'), 'active' => request()->routeIs('parents.learners.*')],
            ['label' => 'Explore Public', 'href' => route('dashboard.guest'), 'active' => false],
        ],
        'admin' => [
            ['label' => 'Dashboard', 'href' => $dashboardRoute, 'active' => request()->routeIs('dashboard.role')],
            ['label' => 'Users & Roles', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
            ['label' => 'Academic Structure', 'href' => route('admin.curriculum.index'), 'active' => request()->routeIs('admin.curriculum.index') || request()->routeIs('admin.curriculum.boards.*') || request()->routeIs('admin.curriculum.classes.*') || request()->routeIs('admin.curriculum.subjects.*')],
            ['label' => 'Course Content', 'href' => route('admin.curriculum.content.index'), 'active' => request()->routeIs('admin.curriculum.content.*')],
            ['label' => 'Teaching Management', 'href' => route('admin.teaching.index'), 'active' => request()->routeIs('admin.teaching.*')],
            ['label' => 'Progress & Assessment', 'href' => route('admin.progress.index'), 'active' => request()->routeIs('admin.progress.*')],
            ['label' => 'Explore Public', 'href' => route('dashboard.guest'), 'active' => false],
        ],
        default => [],
    };
@endphp

<aside class="border-b border-slate-200 bg-white lg:min-h-screen lg:border-b-0 lg:border-r" aria-label="{{ ucfirst($roleKey) }} sidebar">
    <div class="px-5 py-5 lg:px-6">
        <a href="{{ $dashboardRoute }}" class="flex items-center gap-3 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900" aria-label="SignGyaan {{ $roleKey }} dashboard">
            <span class="grid size-11 place-items-center rounded-xl bg-slate-950 text-sm font-black text-white">SG</span>
            <span>
                <span class="block text-lg font-black">SignGyaan</span>
                <span class="block text-xs font-medium text-slate-500">Accessible learning</span>
            </span>
        </a>
    </div>

    <nav class="px-4 pb-5 lg:px-5" aria-label="{{ ucfirst($roleKey) }} navigation">
        <p class="mb-2 hidden px-3 text-xs font-bold uppercase tracking-[0.18em] text-slate-400 lg:block">Navigation</p>
        <div class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
            @foreach ($items as $item)
                <a href="{{ $item['href'] }}"
                   @if ($item['active']) aria-current="page" @endif
                   class="flex min-w-fit items-center rounded-xl px-3 py-3 text-sm font-bold {{ $item['active'] ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-slate-100' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </nav>
</aside>
