@props(['label' => null, 'name' => null, 'type' => 'text', 'error' => null])

<div class="space-y-1.5">
    @if ($label)
        <label @if($name) for="{{ $name }}" @endif class="text-sm font-dash font-medium leading-none">{{ $label }}</label>
    @endif
    <input
        type="{{ $type }}"
        @if($name) name="{{ $name }}" id="{{ $name }}" @endif
        {{ $attributes->merge(['class' => 'flex font-dash h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)] disabled:cursor-not-allowed disabled:opacity-50']) }}
    />
    @if ($error)
        <p class="text-xs font-dash text-[var(--color-bahaya)]">{{ $error }}</p>
    @endif
</div>
