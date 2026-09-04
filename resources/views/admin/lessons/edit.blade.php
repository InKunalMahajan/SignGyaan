@extends('layouts.admin')

@section('title', 'Edit Lesson - SignGyaan Admin')
@section('page-title', 'Edit Lesson')
@section('description', 'Edit SignGyaan lesson content, ISL video support, order and publishing status.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Lessons</p>
                    <h2 class="mt-2 break-words font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Edit {{ $lesson->title }}</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">
                        {{ $lesson->unit?->course?->subject?->name ?? 'Subject' }} → {{ $lesson->unit?->course?->title ?? 'Course' }} → {{ $lesson->unit?->title ?? 'Unit' }}
                    </p>
                </div>
                <a href="{{ route('admin.lessons.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to Lessons</a>
            </div>

            <form method="POST" action="{{ route('admin.lessons.update', $lesson) }}">
                @csrf
                @method('PUT')
                @include('admin.lessons._form')
            </form>
        </div>
    </section>
@endsection
