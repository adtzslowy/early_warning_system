@props(['online' => false, 'pulse' => true])

@php $color = $online ? 'var(--color-aman)' : 'var(--color-text-muted)'; @endphp

<span class="relative inline-flex items-center gap-2">
    <span
        @class(['relative flex h-2 w-2 rounded-full', 'status-pulse' => $pulse && $online])
        style="color: {{ $color }}; background-color: {{ $color }};"
    ></span>
    @if (trim($slot) !== '')
        <span class="font-mono text-xs text-[var(--color-text-muted)]">{{ $slot }}</span>
    @endif
</span>
