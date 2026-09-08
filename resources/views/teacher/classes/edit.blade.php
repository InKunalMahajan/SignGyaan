@extends('teacher.layout')

@section('title', 'Edit Class')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <a href="{{ route('teacher.classes.show', $class) }}" class="text-sm font-black text-blue-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">← Back to class</a>
            <h1 class="mt-4 text-3xl font-black tracking-tight">Edit {{ $class->name }}</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Update class details without changing the class code.</p>
        </div>

        <form method="POST" action="{{ route('teacher.classes.update', $class) }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')
            @include('teacher.classes._form', ['class' => $class])
            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-black text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-cyan-200">Save changes</button>
                <a href="{{ route('teacher.classes.show', $class) }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-cyan-200">Cancel</a>
            </div>
        </form>
    </div>
@endsection
