@props([
    'variant' => 'primary', // primary | secondary | outline | ghost | destructive
    'size' => 'md',         // sm | md | lg | icon
    'type' => 'button',
    'href' => null,
])

@php
    $base = 'inline-flex font-dash items-center justify-center gap-2 whitespace-nowrap rounded-lg text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)] focus-visible:ring-offset-2 focus-visible:ring-offset-[var(--color-bg)] disabled:pointer-events-none disabled:opacity-50';

    $variants = [
        'primary' => 'bg-[var(--color-accent)] text-[var(--color-accent-foreground)] hover:opacity-90',
        'secondary' => 'bg-[var(--color-surface-2)] text-[var(--color-text)] hover:opacity-80',
        'outline' => 'border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-[var(--color-surface-2)]',
        'ghost' => 'text-[var(--color-text)] hover:bg-[var(--color-surface-2)]',
        'destructive' => 'bg-[var(--color-bahaya)] text-white hover:opacity-90',
    ];

    $sizes = [
        'sm' => 'h-8 px-3 text-xs',
        'md' => 'h-9 px-4',
        'lg' => 'h-10 px-6',
        'icon' => 'h-9 w-9',
    ];

    $classes = implode(' ', [$base, $variants[$variant] ?? $variants['primary'], $sizes[$size] ?? $sizes['md']]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
