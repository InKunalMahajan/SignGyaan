@props([
    'href' => null,
])

@if ($href)

    <a
        href="{{ $href }}"
        {{ $attributes->merge([
            'class' => 'inline-flex items-center justify-center rounded-lg bg-sign-primary px-5 py-3 text-sm font-semibold text-white'
        ]) }}
    >
        {{ $slot }}
    </a>

@else

    <button
        {{ $attributes->merge([
            'class' => 'inline-flex items-center justify-center rounded-lg bg-sign-primary px-5 py-3 text-sm font-semibold text-white'
        ]) }}
    >
        {{ $slot }}
    </button>

@endif