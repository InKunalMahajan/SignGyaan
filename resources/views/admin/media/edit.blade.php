@extends('layouts.admin')

@section('title', 'Edit Media - SignGyaan Admin')
@section('page-title', 'Edit Media')
@section('description', 'Edit a reusable SignGyaan media item, source, accessibility text and publishing status.')

@section('content')
    <section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <nav class="flex flex-wrap items-center gap-2 text-xs text-sign-muted sm:text-sm" aria-label="Breadcrumb">
                <a href="{{ route('admin.media.index') }}" class="font-semibold transition hover:text-sign-primary">Media</a>
                <span aria-hidden="true">/</span>
                <span class="font-semibold text-sign-primary">Edit Media</span>
            </nav>

            <div class="mt-5">
                <p class="text-sm font-semibold uppercase tracking-wider text-sign-cyan-dark">Media library</p>
                <h2 class="mt-2 break-words font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Edit {{ $mediaAsset->title }}</h2>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-sign-muted">Update the media file or URL, accessibility text, notes and publishing status.</p>
            </div>

            <form method="POST" action="{{ route('admin.media.update', $mediaAsset) }}" enctype="multipart/form-data" class="mt-7">
                @csrf
                @method('PUT')
                @include('admin.media._form')
            </form>
        </div>
    </section>
@endsection
