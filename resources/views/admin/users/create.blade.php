@extends('admin.layout')

@section('title', 'Create User')

@section('content')
    <div class="max-w-4xl">
        <a href="{{ route('admin.users.index') }}" class="text-sm font-black text-blue-700 hover:text-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-100">← Back to users</a>
        <div class="mt-5">
            <p class="text-xs font-black uppercase tracking-[0.16em] text-cyan-700">Users & Roles</p>
            <h1 class="mt-2 text-3xl font-black tracking-tight">Create user account</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Admins can create Learner, Parents, Teacher, or Admin accounts.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="mt-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-7">
            @csrf
            @include('admin.users._form')

            <div class="mt-7 flex flex-wrap gap-3 border-t border-slate-100 pt-6">
                <button type="submit" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Create user</button>
                <a href="{{ route('admin.users.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-cyan-200">Cancel</a>
            </div>
        </form>
    </div>
@endsection
