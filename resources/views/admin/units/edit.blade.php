@extends('layouts.admin')

@section('title', 'Edit Unit - SignGyaan Admin')
@section('page-title', 'Edit Unit')
@section('description', 'Edit a SignGyaan course unit, order and publishing settings.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="transition hover:text-sign-primary">Dashboard</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('admin.units.index') }}" class="transition hover:text-sign-primary">Units</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Edit Unit</span>
            </nav>

            <div class="mt-6">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Course structure</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Edit {{ $unit->title }}</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Update the course assignment, unit details, display order and publishing state.</p>
            </div>

            <form method="POST" action="{{ route('admin.units.update', $unit) }}" class="mt-7">
                @csrf
                @method('PUT')
                @include('admin.units._form')
            </form>
        </div>
    </section>
@endsection
