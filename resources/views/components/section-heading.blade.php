@props([
    'title',
    'description' => null,
])

<div class="max-w-3xl">

    <h2 class="font-heading text-3xl font-semibold text-sign-primary">
        {{ $title }}
    </h2>

    @if ($description)

        <p class="mt-4 text-sign-muted">
            {{ $description }}
        </p>

    @endif

</div>