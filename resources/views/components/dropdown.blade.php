@props(['align' => 'right'])

@php $alignClass = $align === 'left' ? 'left-0' : 'right-0'; @endphp

<div x-data="{ open: false }" class="relative" @click.outside="open = false">
    <div @click="open = !open">{{ $trigger }}</div>

    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="card absolute {{ $alignClass }} z-50 mt-2 min-w-[10rem] max-w-[calc(100vw-2rem)] overflow-hidden p-1"
    >
        {{ $slot }}
    </div>
</div>
