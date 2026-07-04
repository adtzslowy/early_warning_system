@props(['title' => '', 'description' => null])

<div class="mb-6 font-dash flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="space-y-1">
        <h1 class="font-display text-2xl font-bold tracking-tight">{{ $title }}</h1>
        @if ($description)
            <p class="text-sm text-[var(--color-text-muted)]">{{ $description }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>
