@props([
    // Laravel LengthAwarePaginator / Paginator
    'paginator' => null,
    // Jumlah halaman yang ditampilkan di kiri-kanan halaman aktif.
    'siblings' => 1,
])

@if ($paginator && $paginator->hasPages())
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $s = max(0, (int) $siblings);

        // Halaman yang selalu tampil: pertama, terakhir, dan sekitar halaman aktif.
        $pages = collect([1, $last]);
        for ($i = $current - $s; $i <= $current + $s; $i++) {
            if ($i >= 1 && $i <= $last) {
                $pages->push($i);
            }
        }
        $pages = $pages->unique()->sort()->values();

        // Sisipkan ellipsis di celah yang bolong (mis. 1 … 4 5 6 … 12).
        $items = [];
        $previous = 0;
        foreach ($pages as $page) {
            if ($previous && $page - $previous > 1) {
                $items[] = ['type' => 'dots'];
            }
            $items[] = ['type' => 'page', 'page' => $page];
            $previous = $page;
        }

        $base = 'inline-flex h-9 items-center justify-center rounded-lg border text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]';
    @endphp

    {{-- Selebar konten (netral). Untuk menaruh di tengah, bungkus dgn `flex justify-center` atau tambahkan `class="mx-auto"`. --}}
    <nav role="navigation" aria-label="pagination" {{ $attributes->merge(['class' => 'flex font-dash w-max max-w-full']) }}>
        <ul class="flex flex-row items-center gap-1">
            {{-- Sebelumnya --}}
            @php
                $prevOn = ! $paginator->onFirstPage();
                $prevCls = $base . ' gap-1 px-3 ' . ($prevOn
                    ? 'border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-[var(--color-surface-2)]'
                    : 'pointer-events-none border-transparent text-[var(--color-text-muted)] opacity-50');
            @endphp
            <li>
                <{{ $prevOn ? 'a' : 'span' }}
                    @if ($prevOn) href="{{ $paginator->previousPageUrl() }}" rel="prev" @else aria-disabled="true" @endif
                    class="{{ $prevCls }}" aria-label="Sebelumnya"
                >
                    <x-heroicon-o-chevron-left class="h-4 w-4" />
                    <span class="hidden sm:inline">Sebelumnya</span>
                </{{ $prevOn ? 'a' : 'span' }}>
            </li>

            {{-- Angka halaman + ellipsis --}}
            @foreach ($items as $item)
                @if ($item['type'] === 'dots')
                    <li>
                        <span class="inline-flex h-9 w-9 items-center justify-center text-[var(--color-text-muted)]" aria-hidden="true">
                            <x-heroicon-o-ellipsis-horizontal class="h-4 w-4" />
                            <span class="sr-only">Lebih banyak halaman</span>
                        </span>
                    </li>
                @else
                    @php
                        $active = $item['page'] === $current;
                        $pageCls = $base . ' w-9 ' . ($active
                            ? 'border-[var(--color-border)] bg-[var(--color-surface-2)] text-[var(--color-text)]'
                            : 'border-transparent text-[var(--color-text-muted)] hover:bg-[var(--color-surface-2)] hover:text-[var(--color-text)]');
                    @endphp
                    <li>
                        <a href="{{ $paginator->url($item['page']) }}" class="{{ $pageCls }}" @if ($active) aria-current="page" @endif>
                            {{ $item['page'] }}
                        </a>
                    </li>
                @endif
            @endforeach

            {{-- Berikutnya --}}
            @php
                $nextOn = $paginator->hasMorePages();
                $nextCls = $base . ' gap-1 px-3 ' . ($nextOn
                    ? 'border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] hover:bg-[var(--color-surface-2)]'
                    : 'pointer-events-none border-transparent text-[var(--color-text-muted)] opacity-50');
            @endphp
            <li>
                <{{ $nextOn ? 'a' : 'span' }}
                    @if ($nextOn) href="{{ $paginator->nextPageUrl() }}" rel="next" @else aria-disabled="true" @endif
                    class="{{ $nextCls }}" aria-label="Berikutnya"
                >
                    <span class="hidden sm:inline">Berikutnya</span>
                    <x-heroicon-o-chevron-right class="h-4 w-4" />
                </{{ $nextOn ? 'a' : 'span' }}>
            </li>
        </ul>
    </nav>
@endif
