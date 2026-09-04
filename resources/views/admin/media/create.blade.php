@extends('layouts.admin')

@section('title', 'Add Media - SignGyaan Admin')
@section('page-title', 'Add Media')
@section('description', 'Add a reusable image, video, document, audio file or external link to the SignGyaan media library.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.media.index') }}" class="font-semibold transition hover:text-sign-primary">Media</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Add Media</span>
            </nav>

            <div class="mt-5">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Media library</p>
                <h2 class="mt-2 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Add Media</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Upload a reusable file or register an external media URL for lessons and learning resources.</p>
            </div>

            <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mt-7">
                @csrf
                @include('admin.media._form')
            </form>
        </div>
    </section>
@endsection
