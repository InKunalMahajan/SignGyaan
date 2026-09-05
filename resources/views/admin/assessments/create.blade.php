@extends('layouts.admin')

@section('title', 'Create Assessment - SignGyaan Admin')
@section('page-title', 'Create Assessment')
@section('description', 'Create assessment rules for an eligible SignGyaan quiz or exercise.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-6xl">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Assessment management</p>
                    <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Create Assessment</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">Choose a Quiz or Exercise practice item and define the learner assessment rules.</p>
                </div>
                <a href="{{ route('admin.assessments.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-sign-border bg-white px-4 py-3 text-sm font-semibold text-sign-primary transition hover:bg-sign-soft">Back to Assessments</a>
            </div>

            <form method="POST" action="{{ route('admin.assessments.store') }}">
                @csrf
                @include('admin.assessments._form')
            </form>
        </div>
    </section>
@endsection
