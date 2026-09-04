@extends('layouts.admin')

@section('title', 'Add Lesson - SignGyaan Admin')
@section('page-title', 'Add Lesson')
@section('description', 'Create a SignGyaan lesson with structured content and ISL support.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Lessons</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Create Lesson</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Add a lesson inside an existing unit, then prepare the learner-facing notes, key points, example and ISL video support.</p>
                </div>
                <a href="{{ route('admin.lessons.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to Lessons</a>
            </div>

            @if ($units->isEmpty())
                <div class="rounded-2xl border border-dashed border-sign-border bg-white p-7 text-center sm:rounded-3xl sm:p-10">
                    <h3 class="font-heading text-2xl font-semibold text-sign-primary">Create a unit first</h3>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-sign-muted">Every lesson belongs to a unit. Add at least one unit before creating lesson content.</p>
                    <a href="{{ route('admin.units.create') }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-xl bg-sign-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-sign-dark">Add Unit</a>
                </div>
            @else
                <form method="POST" action="{{ route('admin.lessons.store') }}">
                    @csrf
                    @include('admin.lessons._form')
                </form>
            @endif
        </div>
    </section>
@endsection
