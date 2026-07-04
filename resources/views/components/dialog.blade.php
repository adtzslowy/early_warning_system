@props(['title' => null, 'description' => null])

<div
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    x-on:open-dialog.window="open = true"
>
    {{-- Trigger --}}
    @isset($trigger)
        <div @click="open = true">{{ $trigger }}</div>
    @endisset

    {{-- Overlay + panel --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed font-dash inset-0 z-50 flex items-center justify-center p-4">
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="open = false"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm"
            ></div>

            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="card relative z-10 w-full max-w-lg p-6"
            >
                @if ($title)
                    <h2 class="text-lg font-semibold leading-none tracking-tight">{{ $title }}</h2>
                @endif
                @if ($description)
                    <p class="mt-1.5 text-sm text-[var(--color-text-muted)]">{{ $description }}</p>
                @endif
                <div class="mt-4">{{ $slot }}</div>
            </div>
        </div>
    </template>
</div>
