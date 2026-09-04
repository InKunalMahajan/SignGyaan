@extends('layouts.admin')

@section('title', 'Add Practice or Resource - SignGyaan Admin')
@section('page-title', 'Add Practice or Resource')
@section('description', 'Create a practice activity or supporting resource for a SignGyaan lesson.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.practice.index') }}" class="font-semibold text-sign-primary transition hover:text-sign-cyan-dark">Practice & Resources</a>
                <span aria-hidden="true">/</span>
                <span>Add item</span>
            </nav>

            <div class="mt-5">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Content management</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Add Practice or Resource</h2>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-sign-muted">Attach an activity, worksheet, reference or useful link to a lesson.</p>
            </div>

            <form method="POST" action="{{ route('admin.practice.store') }}" class="mt-7">
                @csrf
                @include('admin.practice-resources._form')
            </form>
        </div>
    </section>
@endsection
