@extends('layouts.admin')

@section('title', 'Add Course - SignGyaan Admin')
@section('page-title', 'Add Course')
@section('description', 'Create a new SignGyaan course inside a subject.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-sign-primary">Admin</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('admin.courses.index') }}" class="transition hover:text-sign-primary">Courses</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Add Course</span>
            </nav>

            <div class="mt-5">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Create Course</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Add a new course, connect it to a subject and prepare it for units and lessons.</p>
            </div>

            <form method="POST" action="{{ route('admin.courses.store') }}" class="mt-7">
                @csrf
                @include('admin.courses._form')
            </form>
        </div>
    </section>
@endsection
