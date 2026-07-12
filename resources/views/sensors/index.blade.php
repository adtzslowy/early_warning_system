@extends('layouts.app')

@section('title', 'Sensors')
@section('page-title', 'Sensors')

@section('breadcrumb')
    <x-breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard')], ['label' => 'Sensors']]" />
@endsection

@section('content')
    <div class="space-y-6" x-data="{
        confirm: false,
        url: '',
        code: '',
        ask(url, code) { this.url = url;
            this.code = code;
            this.confirm = true; },

        controller: null,
        async liveSearch() {
            if (this.controller) this.controller.abort();
            this.controller = new AbortController();

            const params = new URLSearchParams(new FormData(this.$refs.filterForm));

            try {
                const res = await fetch('{{ route('sensors') }}?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: this.controller.signal,
                });

                if (!res.ok) throw new Error('Request failed: ' + res.status);

                this.$refs.results.innerHTML = await res.text();
                history.pushState({}, '', '?' + params.toString());
            } catch (e) {
                if (e.name !== 'AbortError') console.error(e);
            }
        },
    }" @keydown.escape.window="confirm = false">

        <x-page-header title="History Sensor" description="Riwayat data pembacaan sensor dari seluruh device pemantau.">
        </x-page-header>

        @if (session('status'))
            <div
                class="flex items-center gap-2 rounded-lg border border-[color-mix(in_srgb,var(--color-aman)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-aman)_12%,transparent)] px-4 py-3 text-sm text-[var(--color-aman)]">
                <x-heroicon-o-check-circle class="h-4 w-4 shrink-0" />
                {{ session('status') }}
            </div>
        @endif

        <x-card padding="p-0">
            <div class="border-b border-[var(--color-border)] p-4">
                <form method="GET" action="{{ route('sensors') }}" x-ref="filterForm"
                    class="flex flex-col gap-3 sm:flex-row sm:items-center" @submit.prevent="liveSearch">
                    <div class="relative flex-1">
                        <x-heroicon-o-magnifying-glass
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--color-text-muted)]" />
                        <input type="search" name="q" value="{{ $search }}"
                            placeholder="Cari kode atau nama device…"
                            @input.debounce.400ms="liveSearch"
                            class="h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent pl-9 pr-3 text-sm shadow-sm transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                    </div>
                    <select name="device_id" @change="liveSearch"
                        class="h-9 rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                        <option value="">Semua device</option>
                        @foreach ($devices as $d)
                            <option value="{{ $d->id }}" @selected($deviceId === $d->id)>{{ $d->device_code }} —
                                {{ $d->name }}</option>
                        @endforeach
                    </select>
                    <select name="type" @change="liveSearch"
                        class="h-9 rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                        <option value="">Semua tipe</option>
                        @foreach ($types as $t)
                            <option value="{{ $t }}" @selected($type === $t)>{{ Str::headline($t) }}
                            </option>
                        @endforeach
                    </select>
                    <x-button type="submit" variant="secondary">Terapkan</x-button>
                    @if ($search !== '' || $deviceId !== '' || $type !== '')
                        <x-button href="{{ route('sensors') }}" variant="ghost">Reset</x-button>
                    @endif
                </form>
            </div>

            <div x-ref="results">
                @fragment('results')
                    @if ($sensors->isEmpty())
                        <x-empty-state icon="inbox" title="{{ $total === 0 ? 'Belum ada data sensor' : 'Tidak ada hasil' }}"
                            message="{{ $total === 0 ? 'Data pembacaan sensor akan muncul di sini setelah sinkronisasi berjalan.' : 'Coba ubah kata kunci atau filter.' }}">
                            @if ($total === 0)
                                @can('create sensors')
                                    <x-slot:action>
                                        <x-button href="{{ route('sensors.create') }}" variant="primary">
                                            <x-heroicon-o-plus class="h-4 w-4" />
                                            Tambah Reading
                                        </x-button>
                                    </x-slot:action>
                                @endcan
                            @endif
                        </x-empty-state>
                    @else
                        <div class="overflow-x-auto">
                            <table class="rtable w-full text-sm">
                                <thead
                                    class="border-b border-[var(--color-border)] text-left text-xs uppercase tracking-wider text-[var(--color-text-muted)]">
                                    <tr>
                                        <th class="px-4 py-3 font-medium">Device</th>
                                        <th class="px-4 py-3 font-medium">Tipe</th>
                                        <th class="px-4 py-3 font-medium">Nilai</th>
                                        <th class="px-4 py-3 font-medium">Direkam pada</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[var(--color-border)]">
                                    @foreach ($sensors as $sensor)
                                        <tr class="transition-colors hover:bg-[var(--color-surface-2)]">
                                            <td data-label="Device" class="px-4 py-3">
                                                <span class="font-mono text-xs">{{ $sensor->device->device_code }}</span>
                                                <span
                                                    class="ml-1 text-[var(--color-text-muted)]">{{ $sensor->device->name }}</span>
                                            </td>
                                            <td data-label="Tipe" class="px-4 py-3 font-medium">
                                                {{ Str::headline($sensor->type->value) }}</td>
                                            <td data-label="Nilai" class="px-4 py-3 text-[var(--color-text-muted)]">
                                                {{ number_format($sensor->value, 2) }} {{ $sensor->unit }}
                                            </td>
                                            <td data-label="Direkam pada" class="px-4 py-3 text-[var(--color-text-muted)]">
                                                {{ $sensor->recorded_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                WIB
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="flex flex-col items-center justify-between gap-3 border-t border-[var(--color-border)] p-4 sm:flex-row">
                            <p class="text-xs text-[var(--color-text-muted)]">
                                Menampilkan {{ $sensors->firstItem() }}–{{ $sensors->lastItem() }} dari
                                {{ $sensors->total() }}
                                data
                            </p>
                            <x-pagination :paginator="$sensors" />
                        </div>
                    @endif
                @endfragment
            </div>
        </x-card>

        <template x-teleport="body">
            <div x-show="confirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div x-show="confirm" x-transition.opacity @click="confirm = false"
                    class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
                <div x-show="confirm" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="card relative z-10 w-full max-w-md p-6">
                    <div class="flex items-start gap-3">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-bahaya)_12%,transparent)] text-[var(--color-bahaya)]">
                            <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                        </span>
                        <div>
                            <h2 class="text-lg font-semibold leading-none tracking-tight">Hapus data sensor?</h2>
                            <p class="mt-1.5 text-sm text-[var(--color-text-muted)]">
                                Reading <span class="font-mono text-[var(--color-text)]" x-text="code"></span> akan dihapus
                                permanen. Tindakan ini tidak bisa dibatalkan.
                            </p>
                        </div>
                    </div>
                    <form method="POST" :action="url" class="mt-6 flex items-center justify-end gap-3">
                        @csrf
                        @method('DELETE')
                        <x-button type="button" variant="outline" @click="confirm = false">Batal</x-button>
                        <x-button type="submit" variant="destructive">Hapus</x-button>
                    </form>
                </div>
            </div>
        </template>
    </div>
@endsection
