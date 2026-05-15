@props(['active' => false])

@php
$classes = $active
    ? 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-white text-sm font-semibold bg-senai shadow-sm'
    : 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-400 hover:text-white text-sm font-medium transition-all duration-150 hover:bg-white/8';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
