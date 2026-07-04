@props([
    // Daftar item: [['label' => 'Dashboard', 'href' => route('dashboard')], ['label' => 'Setting']]
    // Item tanpa 'href' dianggap halaman aktif (biasanya item terakhir).
    'items' => [],
])

<nav aria-label="breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-1.5 break-words font-dash text-sm text-[var(--color-text-muted)] sm:gap-2.5">
        @foreach ($items as $item)
            @php $isCurrent = empty($item['href']) || $loop->last; @endphp
            <li class="inline-flex items-center gap-1.5">
                @if ($isCurrent)
                    <span class="font-medium text-[var(--color-text)]" aria-current="page">{{ $item['label'] }}</span>
                @else
                    <a href="{{ $item['href'] }}" class="transition-colors hover:text-[var(--color-text)]">{{ $item['label'] }}</a>
                @endif
            </li>

            @unless ($loop->last)
                <li role="presentation" aria-hidden="true" class="inline-flex items-center">
                    <x-heroicon-o-chevron-right class="h-3.5 w-3.5" />
                </li>
            @endunless
        @endforeach
    </ol>
</nav>
