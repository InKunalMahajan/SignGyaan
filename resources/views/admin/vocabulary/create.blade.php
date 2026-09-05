@extends('layouts.admin')

@section('title', 'Add ISL Vocabulary - SignGyaan Admin')
@section('page-title', 'Add ISL Vocabulary')
@section('description', 'Create a reusable vocabulary term with ISL video support.')

@section('content')
<section class="px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <a href="{{ route('admin.vocabulary.index') }}" class="text-sm font-semibold text-sign-primary hover:text-sign-cyan-dark">← Back to ISL Vocabulary</a>
            <h2 class="mt-3 font-heading text-3xl font-semibold text-sign-primary sm:text-4xl">Add vocabulary term</h2>
        </div>
        <form method="POST" action="{{ route('admin.vocabulary.store') }}">
            @csrf
            @include('admin.vocabulary._form')
        </form>
    </div>
</section>
@endsection
