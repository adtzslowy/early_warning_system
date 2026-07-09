@extends('layouts.app')

@section('title', 'Devices')
@section('page-title', 'Devices')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Devices'],
    ]" />
@endsection

@section('content')
<div class="space-y-6"
    x-data="{
        confirm: false,
        url: '',
        code: '',
        ask(url, code) { this.url = url; this.code = code; this.confirm = true; },
    }"
    @keydown.escape.window="confirm = false"
>

    <x-page-header
        title="Devices"
        description="Kelola alat pemantau tinggi muka air di jaringan pesisir."
    >
        @can('create devices')
            <x-slot:actions>
                <x-button href="{{ route('devices.create') }}" variant="primary">
                    <x-heroicon-o-plus class="h-4 w-4" />
                    Tambah Device
                </x-button>
            </x-slot:actions>
        @endcan
    </x-page-header>

    @if (session('status'))
        <div class="flex items-center gap-2 rounded-lg border border-[color-mix(in_srgb,var(--color-aman)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-aman)_12%,transparent)] px-4 py-3 text-sm text-[var(--color-aman)]">
            <x-heroicon-o-check-circle class="h-4 w-4 shrink-0" />
            {{ session('status') }}
        </div>
    @endif

    <x-card padding="p-0">
        {{-- Toolbar: cari + filter status --}}
        <div class="border-b border-[var(--color-border)] p-4">
            <form method="GET" action="{{ route('devices') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <x-heroicon-o-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[var(--color-text-muted)]" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Cari kode, nama, atau lokasi…"
                        class="h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent pl-9 pr-3 text-sm shadow-sm transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                </div>
                <select name="status"
                    class="h-9 rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="secondary">Terapkan</x-button>
                @if ($search !== '' || $status !== '')
                    <x-button href="{{ route('devices') }}" variant="ghost">Reset</x-button>
                @endif
            </form>
        </div>

        @if ($devices->isEmpty())
            <x-empty-state
                icon="inbox"
                title="{{ $total === 0 ? 'Belum ada device' : 'Tidak ada hasil' }}"
                message="{{ $total === 0 ? 'Tambahkan alat pemantau pertama untuk mulai mengumpulkan data.' : 'Coba ubah kata kunci atau filter status.' }}"
            >
                @if ($total === 0)
                    @can('create devices')
                        <x-slot:action>
                            <x-button href="{{ route('devices.create') }}" variant="primary">
                                <x-heroicon-o-plus class="h-4 w-4" />
                                Tambah Device
                            </x-button>
                        </x-slot:action>
                    @endcan
                @endif
            </x-empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="rtable w-full text-sm">
                    <thead class="border-b border-[var(--color-border)] text-left text-xs uppercase tracking-wider text-[var(--color-text-muted)]">
                        <tr>
                            <th class="px-4 py-3 font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">Lokasi</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Sensor</th>
                            <th class="px-4 py-3 font-medium">Terakhir terlihat</th>
                            <th class="px-4 py-3 text-right font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($devices as $device)
                            <tr class="transition-colors hover:bg-[var(--color-surface-2)]">
                                <td data-label="Kode" class="px-4 py-3">
                                    <span class="font-mono text-xs">{{ $device->device_code }}</span>
                                </td>
                                <td data-label="Nama" class="px-4 py-3 font-medium">{{ $device->name }}</td>
                                <td data-label="Lokasi" class="px-4 py-3 text-[var(--color-text-muted)]">{{ $device->location ?: '—' }}</td>
                                <td data-label="Status" class="px-4 py-3">
                                    <x-status-dot :online="$device->status === \App\Enums\DeviceStatus::Online">
                                        {{ $device->status->label() }}
                                    </x-status-dot>
                                </td>
                                <td data-label="Sensor" class="px-4 py-3 text-[var(--color-text-muted)]">{{ $device->sensors_count }}</td>
                                <td data-label="Terakhir terlihat" class="px-4 py-3 text-[var(--color-text-muted)]">
                                    @if ($device->last_seen_at)
                                        {{ $device->last_seen_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        @can('edit devices')
                                            <a href="{{ route('devices.edit', $device) }}"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface)] hover:text-[var(--color-text)]"
                                                title="Edit">
                                                <x-heroicon-o-pencil-square class="h-4 w-4" />
                                            </a>
                                        @endcan
                                        @can('delete devices')
                                            <button type="button"
                                                @click="ask('{{ route('devices.destroy', $device) }}', '{{ $device->device_code }}')"
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

            <div class="flex flex-col items-center justify-between gap-3 border-t border-[var(--color-border)] p-4 sm:flex-row">
                <p class="text-xs text-[var(--color-text-muted)]">
                    Menampilkan {{ $devices->firstItem() }}–{{ $devices->lastItem() }} dari {{ $devices->total() }} device
                </p>
                <x-pagination :paginator="$devices" />
            </div>
        @endif
    </x-card>

    {{-- Modal konfirmasi hapus (tunggal, target di-set saat klik baris) --}}
    <template x-teleport="body">
        <div x-show="confirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div x-show="confirm" x-transition.opacity @click="confirm = false"
                class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
            <div x-show="confirm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="card relative z-10 w-full max-w-md p-6">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[color-mix(in_srgb,var(--color-bahaya)_12%,transparent)] text-[var(--color-bahaya)]">
                        <x-heroicon-o-exclamation-triangle class="h-5 w-5" />
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold leading-none tracking-tight">Hapus device?</h2>
                        <p class="mt-1.5 text-sm text-[var(--color-text-muted)]">
                            Device <span class="font-mono text-[var(--color-text)]" x-text="code"></span> beserta relasinya akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.
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
