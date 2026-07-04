@props(['title' => 'Belum ada data', 'message' => null, 'icon' => 'inbox'])

@php
    // Peta nama pendek → komponen blade-heroicon.
    $iconMap = [
        'inbox' => 'heroicon-o-inbox',
        'bell' => 'heroicon-o-bell',
        'chart' => 'heroicon-o-chart-bar',
        'map' => 'heroicon-o-map-pin',
    ];
    $iconComponent = $iconMap[$icon] ?? $iconMap['inbox'];
@endphp

<div class="flex font-dash flex-col items-center justify-center py-12 text-center">
    <x-dynamic-component :component="$iconComponent" class="mb-3 h-10 w-10 text-[var(--color-text-muted)] opacity-40" />
    <p class="text-sm font-medium">{{ $title }}</p>
    @if ($message)
        <p class="mt-1 max-w-xs text-xs text-[var(--color-text-muted)]">{{ $message }}</p>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
