@props(['label', 'value', 'color' => 'senai'])

<div class="bg-white rounded-xl shadow-sm p-6 flex items-center gap-4">
    <div class="w-12 h-12 rounded-full bg-{{ $color }}/10 flex items-center justify-center flex-shrink-0">
        {{ $icon ?? '' }}
    </div>
    <div>
        <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
        <p class="text-sm text-gray-500">{{ $label }}</p>
    </div>
</div>
