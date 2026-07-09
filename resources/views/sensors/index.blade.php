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
                <form method="GET" action="{{ route('sensors') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <x-heroicon-o-magnifying-glass
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--color-text-muted)]" />
                        <input type="search" name="q" value="{{ $search }}"
                            placeholder="Cari kode atau nama device…"
                            class="h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent pl-9 pr-3 text-sm shadow-sm transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                    </div>
                    <select name="device_id"
                        class="h-9 rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                        <option value="">Semua device</option>
                        @foreach ($devices as $d)
                            <option value="{{ $d->id }}" @selected($deviceId === $d->id)>{{ $d->device_code }} —
                                {{ $d->name }}</option>
                        @endforeach
                    </select>
                    <select name="type"
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
                    <table class="w-full text-sm">
                        <thead
                            class="border-b border-[var(--color-border)] text-left text-xs uppercase tracking-wider text-[var(--color-text-muted)]">
                            <tr>
                                <th class="px-4 py-3 font-medium">Device</th>
                                <th class="px-4 py-3 font-medium">Tipe</th>
                                <th class="px-4 py-3 font-medium">Nilai</th>
                                <th class="px-4 py-3 font-medium">Direkam pada</th>
                                <th class="px-4 py-3 text-right font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-border)]">
                            @foreach ($sensors as $sensor)
                                <tr class="transition-colors hover:bg-[var(--color-surface-2)]">
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs">{{ $sensor->device->device_code }}</span>
                                        <span
                                            class="ml-1 text-[var(--color-text-muted)]">{{ $sensor->device->name }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium">{{ Str::headline($sensor->type) }}</td>
                                    <td class="px-4 py-3 text-[var(--color-text-muted)]">
                                        {{ number_format($sensor->value, 2) }} {{ $sensor->unit }}
                                    </td>
                                    <td class="px-4 py-3 text-[var(--color-text-muted)]">
                                        {{ $sensor->recorded_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-1">
                                            @can('edit sensors')
                                                <a href="{{ route('sensors.edit', $sensor) }}"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface)] hover:text-[var(--color-text)]"
                                                    title="Edit">
                                                    <x-heroicon-o-pencil-square class="h-4 w-4" />
                                                </a>
                                            @endcan
                                            @can('delete sensors')
                                                <button type="button"
                                                    @click="ask('{{ route('sensors.destroy', $sensor) }}', '{{ $sensor->device->device_code }} — {{ Str::headline($sensor->type) }}')"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[var(--color-text-muted)] transition-colors hover:bg-[color-mix(in_srgb,var(--color-bahaya)_12%,transparent)] hover:text-[var(--color-bahaya)]"
                                                    title="Hapus">
                                                    <x-heroicon-o-trash class="h-4 w-4" />
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div
                    class="flex flex-col items-center justify-between gap-3 border-t border-[var(--color-border)] p-4 sm:flex-row">
                    <p class="text-xs text-[var(--color-text-muted)]">
                        Menampilkan {{ $sensors->firstItem() }}–{{ $sensors->lastItem() }} dari {{ $sensors->total() }}
                        data
                    </p>
                    <x-pagination :paginator="$sensors" />
                </div>
            @endif
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
