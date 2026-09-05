@extends('layouts.admin')

@section('title', 'Edit Assessment - SignGyaan Admin')
@section('page-title', 'Edit Assessment')
@section('description', 'Update SignGyaan assessment rules and publishing settings.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">{{ $assessment->practiceResource?->title ?? 'Assessment' }}</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">
                        {{ $assessment->practiceResource?->lesson?->unit?->course?->subject?->name ?? 'Subject' }} ·
                        {{ $assessment->practiceResource?->lesson?->unit?->course?->title ?? 'Course' }} ·
                        {{ $assessment->practiceResource?->lesson?->title ?? 'Lesson' }}
                    </p>
                </div>
                <a href="{{ route('admin.assessments.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to Assessments</a>
            </div>

            <form method="POST" action="{{ route('admin.assessments.update', $assessment) }}">
                @csrf
                @method('PUT')
                @include('admin.assessments._form')
            </form>
        </div>
    </section>
@endsection
