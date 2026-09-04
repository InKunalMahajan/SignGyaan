<div
    {{ $attributes->merge([
        'class' => 'rounded-2xl border border-sign-border bg-white p-6'
    ]) }}
>
    {{ $slot }}
</div>