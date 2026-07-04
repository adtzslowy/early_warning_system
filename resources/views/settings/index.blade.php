@extends('layouts.app')

@section('title', 'Setting Tampilan')
@section('page-title', 'Setting Tampilan')

@section('content')
<div class="mx-auto space-y-6">

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Setting Tampilan'],
    ]" />

    <x-page-header
        title="Setting Tampilan"
        description="Sesuaikan dashboard sesuai preferensimu — tersimpan di akunmu."
    />

    @if (session('status'))
        <div class="rounded-lg border border-[color-mix(in_srgb,var(--color-aman)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-aman)_12%,transparent)] px-4 py-3 text-sm text-[var(--color-aman)]">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Warna aksen --}}
        <x-card title="Warna Aksen" subtitle="warna utama tombol & elemen aktif">
            <div class="flex flex-wrap gap-3">
                @foreach ($accents as $key => $accent)
                    <label class="cursor-pointer">
                        <input type="radio" name="accent" value="{{ $key }}" class="peer sr-only"
                            @checked($prefs['accent'] === $key)>
                        <span class="flex items-center gap-2 rounded-lg border border-[var(--color-border)] px-3 py-2 text-sm transition-colors peer-checked:border-[var(--color-accent)] peer-checked:ring-1 peer-checked:ring-[var(--color-accent)] hover:bg-[var(--color-surface-2)]">
                            <span class="h-4 w-4 rounded-full border border-[var(--color-border)]"
                                style="background-color: {{ $accent['color'] ?? 'var(--color-accent)' }};"></span>
                            {{ $accent['label'] }}
                        </span>
                    </label>
                @endforeach
            </div>
        </x-card>

        {{-- Default grafik --}}
        <x-card title="Default Grafik Telemetri" subtitle="tampilan awal saat dashboard dibuka">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="chart_metric" class="text-sm font-medium">Metrik default</label>
                    <select id="chart_metric" name="chart_metric"
                        class="mt-1.5 h-9 w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm">
                        @foreach ($metrics as $key => $label)
                            <option value="{{ $key }}" @selected($prefs['chart_metric'] === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="chart_range" class="text-sm font-medium">Rentang waktu default</label>
                    <select id="chart_range" name="chart_range"
                        class="mt-1.5 h-9 w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm">
                        @foreach ($ranges as $mins => $label)
                            <option value="{{ $mins }}" @selected($prefs['chart_range'] === $mins)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </x-card>

        {{-- Visibilitas card --}}
        <x-card title="Tampilkan / Sembunyikan Card" subtitle="pilih card yang ingin tampil di dashboard">
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach ($cards as $key => $label)
                    <label class="flex items-center gap-3 rounded-lg border border-[var(--color-border)] px-3 py-2.5 text-sm transition-colors hover:bg-[var(--color-surface-2)]">
                        <input type="checkbox" name="cards[{{ $key }}]" value="1"
                            class="h-4 w-4 rounded border-[var(--color-border)] accent-[var(--color-accent)]"
                            @checked($prefs['cards'][$key])>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button href="{{ route('dashboard') }}" variant="outline">Batal</x-button>
            <x-button type="submit" variant="primary">Simpan Preferensi</x-button>
        </div>
    </form>
</div>
@endsection
