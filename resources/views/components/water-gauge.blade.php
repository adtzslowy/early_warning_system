@props([
    'value' => null,
    'level' => 'aman',
    'height' => 'h-16',
    'width' => 'w-8',
    'max' => 200,
    'min' => 0,
])

@php
    $colors = [
        'aman' => 'var(--color-aman)',
        'waspada' => 'var(--color-waspada)',
        'siaga' => 'var(--color-siaga)',
        'bahaya' => 'var(--color-bahaya)',
    ];
    $color = $colors[$level] ?? $colors['aman'];
    $percent = $value === null ? 0 : max(0, min(100, (($max - $value) / ($max - $min)) * 100));
@endphp

<div {{ $attributes->merge(['class' => "water-gauge {$height} {$width} shrink-0 font-dash"]) }} style="color: {{ $color }};">
    <div class="water-gauge__fill" style="height: {{ $percent }}%; color: {{ $color }};"></div>
</div>
