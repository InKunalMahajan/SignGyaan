@extends('layouts.admin')

@section('title', 'Add Unit - SignGyaan Admin')
@section('page-title', 'Add Unit')
@section('description', 'Create a new learning unit inside a SignGyaan course.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-sign-primary">Dashboard</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('admin.units.index') }}" class="transition hover:text-sign-primary">Units</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Add Unit</span>
            </nav>

            <div class="mt-6">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Course structure</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Create Unit</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Add an ordered unit to a course. Lessons will be attached to these units in Step 7F.</p>
            </div>

            <form method="POST" action="{{ route('admin.units.store') }}" class="mt-7">
                @csrf
                @include('admin.units._form')
            </form>
        </div>
    </section>
@endsection
