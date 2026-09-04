@extends('layouts.admin')

@section('title', 'Edit Subject - SignGyaan Admin')
@section('page-title', 'Edit Subject')
@section('description', 'Edit a SignGyaan learning subject.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-6xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard') }}" class="font-semibold transition hover:text-sign-primary">Admin</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('admin.subjects.index') }}" class="font-semibold transition hover:text-sign-primary">Subjects</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">{{ $subject->name }}</span>
            </nav>

            <div class="mt-6">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Subject settings</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Edit {{ $subject->name }}</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Update the subject information, order and publishing status.</p>
            </div>

            <form method="POST" action="{{ route('admin.subjects.update', $subject) }}" class="mt-7">
                @csrf
                @method('PUT')
                @include('admin.subjects._form', ['subject' => $subject])
            </form>
        </div>
    </section>
@endsection
