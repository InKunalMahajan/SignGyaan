@extends('layouts.admin')

@section('title', 'Edit Course - SignGyaan Admin')
@section('page-title', 'Edit Course')
@section('description', 'Edit a SignGyaan course, subject, publishing and organisation settings.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-sign-primary">Admin</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('admin.courses.index') }}" class="transition hover:text-sign-primary">Courses</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $course->title }}</span>
            </nav>

            <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Edit Course</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Update course information, learning level, publishing and display settings.</p>
                </div>
                <a href="{{ route('admin.courses.builder', $course) }}" class="inline-flex min-h-11 w-fit items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Open Course Builder</a>
            </div>

            <form method="POST" action="{{ route('admin.courses.update', $course) }}" class="mt-7">
                @csrf
                @method('PUT')
                @include('admin.courses._form')
            </form>
        </div>
    </section>
@endsection
