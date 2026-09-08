@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
    <div class="max-w-4xl">
        <a href="{{ route('admin.users.show', $managedUser) }}" class="text-sm font-black text-blue-700 hover:text-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-100">← Back to user</a>
        <div class="mt-5">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Users & Roles</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Edit {{ $managedUser->name }}</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Update account details, role, password, or access status.</p>
        </div>

        @if (auth()->user()->is($managedUser))
            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <p class="font-black">This is your own Admin account.</p>
                <p class="mt-1">You cannot remove your Admin role or deactivate your own account.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="mt-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            @csrf
            @method('PUT')
            @include('admin.users._form')

            <div class="mt-7 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                <button type="submit" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Save changes</button>
                <a href="{{ route('admin.users.show', $managedUser) }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-cyan-200">Cancel</a>
            </div>
        </form>
    </div>
@endsection
