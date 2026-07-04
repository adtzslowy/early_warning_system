@props([
    'level' => 'aman',
    'size' => 'md',
])

@php
    $colors = [
        'aman' => 'var(--color-aman)',
        'waspada' => 'var(--color-waspada)',
        'siaga' => 'var(--color-siaga)',
        'bahaya' => 'var(--color-bahaya)',
    ];
    $labels = ['aman' => 'Aman', 'waspada' => 'Waspada', 'siaga' => 'Siaga', 'bahaya' => 'Bahaya'];

    $color = $colors[$level] ?? $colors['aman'];
    $label = $labels[$level] ?? ucfirst($level);
    $sizeClass = $size === 'sm' ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-0.5 text-xs';
@endphp

<span
    {{ $attributes->merge(['class' => "inline-flex font-dash items-center gap-1.5 rounded-full border font-semibold {$sizeClass}"]) }}
    style="border-color: color-mix(in srgb, {{ $color }} 40%, transparent); background-color: color-mix(in srgb, {{ $color }} 12%, transparent); color: {{ $color }};"
>
    <span class="h-1.5 w-1.5 font-dash rounded-full" style="background-color: {{ $color }};"></span>
    {{ $label }}
</span>
