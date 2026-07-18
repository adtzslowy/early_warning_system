@extends('layouts.app')

@section('title', 'Prediction')
@section('page-title', 'Prediction')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Prediction']
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <x-page-header
        title="Prediksi Ketinggian Air"
        description="Proyeksi kenaikan/penurunan muka air laut untuk semua device."
    />

    <div class="grid gap-6">
        @forelse ($predictions as $item)
            @php
                $device = $item['device'];
                $curve = $item['curve'];
                $riskLevel = $device->latestRiskEvaluation?->risk_level ?? 'aman';
            @endphp
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden hover:border-[var(--color-accent)]/30 transition-colors">
                {{-- Header --}}
                <div class="flex items-center justify-between gap-4 border-b border-[var(--color-border)] p-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold text-[var(--color-text)]">{{ $device->name }}</h3>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                style="border-color: color-mix(in srgb, var(--color-{{ $riskLevel }}) 40%, transparent);
                                        background-color: color-mix(in srgb, var(--color-{{ $riskLevel }}) 12%, transparent);
                                        color: var(--color-{{ $riskLevel }});">
                                <span class="h-1.5 w-1.5 rounded-full" style="background-color: var(--color-{{ $riskLevel }});"></span>
                                {{ ucfirst($riskLevel) }}
                            </span>
                        </div>
                        <p class="text-sm text-[var(--color-text-muted)]">
                            <span class="font-mono">{{ $device->device_code }}</span> · {{ $device->location }}
                        </p>
                    </div>
                    <a href="{{ route('dashboard', ['device' => $device->device_code]) }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-[var(--color-accent)]/10 px-3 py-2 text-sm font-medium text-[var(--color-accent)] transition-colors hover:bg-[var(--color-accent)]/20">
                        <x-heroicon-o-arrow-right class="h-4 w-4" />
                        Lihat Dashboard
                    </a>
                </div>

                {{-- Content --}}
                <div class="p-4">
                    @if (!$item['has_prediction'])
                        <div class="text-center py-8">
                            <p class="text-sm text-[var(--color-text-muted)]">
                                Prediksi belum aktif — menunggu minimal 10 data sensor untuk membangun model
                            </p>
                        </div>
                    @else
                        <div>
                            <p class="text-xs font-medium text-[var(--color-text-muted)] mb-3">Proyeksi 2 Jam Ke Depan</p>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                                @foreach ($curve as $point)
                                    @php
                                        $value = (float) $point->predictedValue;
                                        $currentWater = $device->latestWaterLevel?->value ?? 0;
                                        $riskColor = match(true) {
                                            $value < 100 => 'var(--color-aman)',
                                            $value < 150 => 'var(--color-waspada)',
                                            $value < 200 => 'var(--color-siaga)',
                                            default => 'var(--color-bahaya)',
                                        };
                                    @endphp
                                    <div class="flex flex-col items-center rounded-lg border-2 p-2 text-center text-xs"
                                        style="border-color: {{ $riskColor }}; background-color: color-mix(in srgb, {{ $riskColor }} 12%, transparent);">
                                        <span class="font-semibold text-[10px] text-[var(--color-text-muted)] uppercase">+{{ $point->horizonMinutes }}m</span>
                                        <span class="mt-1 font-mono font-bold" style="color: {{ $riskColor }};">
                                            {{ number_format($point->predictedValue, 1) }}
                                        </span>
                                        <span class="mt-1 text-[9px] text-[var(--color-text-muted)]">
                                            {{ $point->predictedAt->format('H:i') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-3 text-[11px] text-[var(--color-text-muted)]">
                                💡 Prediksi menggunakan Linear Regression dari 3 hari data terakhir
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <x-empty-state
                icon="chart"
                title="Belum ada device"
                message="Jalankan `php artisan rob:sync` untuk menarik data dari API Ketapang."
            />
        @endforelse
    </div>
</div>
@endsection
