@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => 'card']) }}>
    @if ($title || $subtitle || isset($header))
        <div class="flex font-dash items-center justify-between gap-4 border-b border-[var(--color-border)] px-6 py-4">
            <div class="space-y-0.5">
                @if ($title)
                    <h3 class="font-semibold leading-none tracking-tight">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-sm text-[var(--color-text-muted)]">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($header)
                <div class="shrink-0">{{ $header }}</div>
            @endisset
        </div>
        <div class="{{ $padding }}">{{ $slot }}</div>
    @else
        <div class="{{ $padding }}">{{ $slot }}</div>
    @endif
</div>
