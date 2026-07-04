@props(['label' => '', 'value' => '—', 'unit' => null, 'hint' => null])

<div class="card p-6 font-dash">
    <p class="text-sm font-medium text-[var(--color-text-muted)]">{{ $label }}</p>
    <p class="mt-2 font-display text-3xl font-bold tracking-tight">
        {{ $value }}@if ($unit)<span class="text-lg font-medium text-[var(--color-text-muted)]"> {{ $unit }}</span>@endif
    </p>
    @if ($hint)
        <p class="mt-1 text-xs text-[var(--color-text-muted)]">{{ $hint }}</p>
    @endif
</div>
