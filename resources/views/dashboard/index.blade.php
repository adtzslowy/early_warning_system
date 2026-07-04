@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6 font-dash">

    @if ($selected === null)
        <x-page-header title="Dashboard" description="Monitoring realtime sensor banjir rob Ketapang" />
        <x-card>
            <x-empty-state icon="bell" title="Belum ada device" message="Jalankan `php artisan rob:sync` untuk menarik data sensor." />
        </x-card>
    @else

    <x-page-header
        title="Dashboard Early Warning"
        description="Monitoring realtime per titik sensor banjir rob Ketapang"
    >
        <x-slot:actions>
            {{-- Selektor alat gaya shadcn --}}
            <x-dropdown align="right">
                <x-slot:trigger>
                    <x-button variant="outline" class="min-w-[13rem] justify-between">
                        <span class="flex items-center gap-2">
                            <x-status-dot :online="$selected['status'] === 'online'" :pulse="false" />
                            <span class="truncate font-mono text-xs">{{ $selected['device_code'] }}</span>
                        </span>
                        <x-heroicon-o-chevron-down class="h-4 w-4 opacity-60" />
                    </x-button>
                </x-slot:trigger>

                <div class="max-h-80 w-64 overflow-y-auto">
                    @foreach ($deviceOptions as $option)
                        <a
                            href="{{ request()->fullUrlWithQuery(['device' => $option['device_code']]) }}"
                            @class([
                                'flex items-center justify-between gap-2 rounded-md px-2.5 py-2 text-sm transition-colors hover:bg-[var(--color-surface-2)]',
                                'bg-[var(--color-surface-2)]' => $option['device_code'] === $selectedCode,
                            ])
                        >
                            <span class="flex min-w-0 items-center gap-2">
                                <x-status-dot :online="$option['status'] === 'online'" :pulse="false" />
                                <span class="truncate font-mono text-xs">{{ $option['device_code'] }}</span>
                            </span>
                            <x-risk-badge :level="$option['risk']->value" size="sm" />
                        </a>
                    @endforeach
                </div>
            </x-dropdown>
        </x-slot:actions>
    </x-page-header>

    {{-- Identitas alat terpilih --}}
    <div class="flex flex-wrap items-center gap-3">
        <h2 class="font-display text-xl font-bold tracking-tight">{{ $selected['name'] ?? $selected['device_code'] }}</h2>
        <span data-rt-risk-hero><x-risk-badge :level="$selected['risk']->value" /></span>
    </div>

    {{-- KPI alat terpilih --}}
    @if ($prefs['cards']['kpi_water'] || $prefs['cards']['kpi_rise'] || $prefs['cards']['kpi_onshore'] || $prefs['cards']['kpi_score'])
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @if ($prefs['cards']['kpi_water'])
        <div class="card p-6">
            <div class="flex items-center justify-between gap-2">
                <p class="text-sm font-medium text-[var(--color-text-muted)]">Ketinggian Air</p>
                <span data-rt-risk-hero data-sm><x-risk-badge :level="$selected['risk']->value" size="sm" /></span>
            </div>
            <p class="mt-2 font-display text-3xl font-bold tracking-tight" data-rt-water-hero>
                {{ $selected['water_level'] !== null ? number_format($selected['water_level'], 1) : '—' }}<span class="text-lg font-medium text-[var(--color-text-muted)]"> cm</span>
            </p>
            @php
                $wl = $selected['water_level'];
                $wlMax = 200;
                $wlPercent = $wl !== null ? max(4, min(100, (($wlMax - $wl) / $wlMax) * 100)) : 0;
            @endphp
            <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-[var(--color-surface-2)]">
                <div class="h-full rounded-full transition-all duration-500" data-rt-waterbar style="width: {{ $wlPercent }}%; background-color: var(--color-{{ $selected['risk']->value }});"></div>
            </div>
            <p class="mt-1.5 text-xs text-[var(--color-text-muted)]">
                {{ $wl !== null ? 'jarak sensor ke permukaan air' : 'data belum tersedia' }}
            </p>
        </div>
        @endif

        @if ($prefs['cards']['kpi_rise'])
        <div class="card p-6">
            <p class="text-sm font-medium text-[var(--color-text-muted)]">Laju Kenaikan</p>
            <p class="mt-2 font-display text-3xl font-bold tracking-tight" data-rt-rise>{{ $selected['rise_rate'] !== null ? number_format($selected['rise_rate'], 2) : '—' }}<span class="text-lg font-medium text-[var(--color-text-muted)]"> cm/jam</span></p>
            <p class="mt-1 text-xs text-[var(--color-text-muted)]">positif = air naik</p>
        </div>
        @endif

        @if ($prefs['cards']['kpi_onshore'])
        <div class="card p-6">
            <p class="text-sm font-medium text-[var(--color-text-muted)]">Onshore Wind</p>
            <p class="mt-2 font-display text-3xl font-bold tracking-tight" data-rt-onshore>{{ $selected['onshore_wind'] !== null ? number_format($selected['onshore_wind'], 2) : '—' }}<span class="text-lg font-medium text-[var(--color-text-muted)]"> m/s</span></p>
            <p class="mt-1 text-xs text-[var(--color-text-muted)]">komponen angin ke darat</p>
        </div>
        @endif

        @if ($prefs['cards']['kpi_score'])
        <div class="card p-6">
            <p class="text-sm font-medium text-[var(--color-text-muted)]">Skor Risiko</p>
            <p class="mt-2 font-display text-3xl font-bold tracking-tight" data-rt-score>{{ $selected['risk_score'] !== null ? number_format($selected['risk_score'], 0) : '—' }}</p>
            <p class="mt-1 text-xs text-[var(--color-text-muted)]">hasil fuzzy Mamdani</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Telemetri + Prediksi --}}
    @if ($prefs['cards']['telemetry'] || $prefs['cards']['prediction'])
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        @if ($prefs['cards']['telemetry'])
        <x-card title="Telemetri" subtitle="pembacaan sensor terbaru" :class="$prefs['cards']['prediction'] ? 'lg:col-span-2' : 'lg:col-span-3'">
            @php
                $telemetry = [
                    ['temperature', 'Suhu', $selected['temperature'], '°C', 1],
                    ['humidity', 'Kelembapan', $selected['humidity'], '%', 0],
                    ['air_pressure', 'Tekanan Udara', $selected['air_pressure'], 'hPa', 1],
                    ['wind_speed', 'Kec. Angin', $selected['wind_speed'], 'm/s', 1],
                    ['wind_direction', 'Arah Angin', $selected['wind_direction'], '°', 0],
                ];
            @endphp

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap gap-2" role="tablist" aria-label="Pilih grafik telemetri">
                    @foreach ($telemetry as [$key, $label, $value, $unit, $decimals])
                        <button
                            type="button"
                            data-telemetry-tab="{{ $key }}"
                            @class([
                                'inline-flex h-8 items-center rounded-lg border px-3 text-xs font-medium transition-colors',
                                'border-[var(--color-accent)] bg-[var(--color-accent)] text-[var(--color-accent-foreground)]' => $key === $prefs['chart_metric'],
                                'border-[var(--color-border)] bg-[var(--color-surface)] hover:bg-[var(--color-surface-2)]' => $key !== $prefs['chart_metric'],
                            ])
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                <div class="ml-auto flex items-center gap-2">
                    <label for="rangeSelect" class="text-xs font-medium text-[var(--color-text-muted)]">Rentang</label>
                    <select
                        id="rangeSelect"
                        data-range-select
                        class="h-8 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-xs font-medium transition-colors hover:bg-[var(--color-surface-2)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]"
                    >
                        @foreach ([[60, '1 Jam'], [360, '6 Jam'], [1440, '24 Jam'], [0, 'Semua']] as [$mins, $rangeLabel])
                            <option value="{{ $mins }}" @selected($mins === $prefs['chart_range'])>{{ $rangeLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="min-h-[18rem]">
                <div id="telemetryChart" class="h-72 w-full"></div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-5">
                @foreach ($telemetry as [$key, $label, $value, $unit, $decimals])
                    <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface-2)] p-4" data-telemetry-card="{{ $key }}">
                        <p class="text-xs font-medium text-[var(--color-text-muted)]">{{ $label }}</p>
                        <p class="mt-1.5 font-mono text-lg font-semibold" data-rt-telemetry="{{ $key }}" data-decimals="{{ $decimals }}">
                            {{ $value !== null ? number_format($value, $decimals) : '—' }}<span class="text-xs text-[var(--color-text-muted)]"> {{ $unit }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </x-card>
        @endif

        @if ($prefs['cards']['prediction'])
        <x-card title="Prediksi" subtitle="Prediksi kenaikan/penurunan muka air laut" :class="$prefs['cards']['telemetry'] ? '' : 'lg:col-span-3'">
            @if (empty($selected['prediction_curve']))
                <x-empty-state icon="chart" title="Prediksi belum aktif" message="Menunggu histori data cukup untuk regresi." />
            @else
                <div id="predictionChart" class="h-56 w-full"></div>

                {{-- Strip forecast: tiap titik horizon, mirip prakiraan cuaca per jam --}}
                <div class="-mx-2 mt-3 flex gap-2 overflow-x-auto overflow-y-hidden px-2 pb-1">
                    @foreach ($selected['prediction_curve'] as $point)
                        <div class="flex min-w-[4.25rem] shrink-0 flex-col items-center rounded-lg border border-[var(--color-border)] bg-[var(--color-surface-2)] px-2 py-2 text-center">
                            <span class="text-[10px] font-medium text-[var(--color-text-muted)]">+{{ $point['horizon'] }}m</span>
                            <span class="mt-1 font-mono text-sm font-semibold">{{ number_format($point['value'], 1) }}</span>
                            <span class="text-[10px] text-[var(--color-text-muted)]">{{ $point['at'] }}</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-2 text-[10px] text-[var(--color-text-muted)]">Nilai dalam cm · waktu WIB · diperbarui saat data sensor baru masuk</p>
            @endif
        </x-card>
        @endif
    </div>
    @endif

    {{-- Metadata --}}
    @if ($prefs['cards']['info'])
    <x-card title="Info Alat">
        <dl class="grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3 lg:grid-cols-4">
            @php
                $coords = $selected['latitude'] !== null && $selected['longitude'] !== null
                    ? number_format($selected['latitude'], 5) . ', ' . number_format($selected['longitude'], 5)
                    : '—';
                $meta = [
                    ['Kode', $selected['device_code']],
                    ['Nama', $selected['name'] ?? '—'],
                    ['Lokasi', $selected['location'] ?? '—'],
                    ['Koordinat', $coords],
                    ['Terakhir Online', $selected['last_seen_at'] ?? '—'],
                    ['Terakhir Dievaluasi', $selected['evaluated_at'] ?? '—'],
                ];
            @endphp
            @foreach ($meta as [$label, $value])
                <div>
                    <dt class="text-xs font-medium text-[var(--color-text-muted)]">{{ $label }}</dt>
                    <dd class="mt-1 truncate text-sm">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </x-card>
    @endif

    {{-- Tabel semua device --}}
    @if ($prefs['cards']['device_table'])
    <x-card title="Semua Device" subtitle="{{ $deviceTable->total() }} titik sensor · klik “Lihat” untuk berpindah">
        <div class="-mx-6 -mt-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--color-border)] text-left text-xs font-medium text-[var(--color-text-muted)]">
                        <th class="px-6 py-3">Kode</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Ketinggian Air</th>
                        <th class="px-3 py-3">Risiko</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deviceTable as $option)
                        <tr data-code="{{ $option['device_code'] }}" @class([
                            'border-b border-[var(--color-border)] last:border-0 transition-colors hover:bg-[var(--color-surface-2)]',
                            'bg-[var(--color-surface-2)]' => $option['device_code'] === $selectedCode,
                        ])>
                            <td class="px-6 py-3 font-mono text-xs font-medium">{{ $option['device_code'] }}</td>
                            <td class="px-3 py-3" data-rt-status>
                                <x-status-dot :online="$option['status'] === 'online'">{{ $option['status'] === 'online' ? 'Online' : 'Offline' }}</x-status-dot>
                            </td>
                            <td class="px-3 py-3 font-mono text-xs" data-rt-water>
                                {{ $option['water_level'] !== null ? number_format($option['water_level'], 1) . ' cm' : '—' }}
                            </td>
                            <td class="px-3 py-3" data-rt-risk data-sm>
                                <x-risk-badge :level="$option['risk']->value" size="sm" />
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a
                                    href="{{ request()->fullUrlWithQuery(['device' => $option['device_code']]) }}"
                                    @class([
                                        'inline-flex h-8 items-center justify-center rounded-lg border px-3 text-xs font-medium transition-colors',
                                        'border-transparent bg-[var(--color-surface-2)] text-[var(--color-text)]' => $option['device_code'] === $selectedCode,
                                        'border-[var(--color-border)] bg-[var(--color-surface)] hover:bg-[var(--color-surface-2)]' => $option['device_code'] !== $selectedCode,
                                    ])
                                >
                                    {{ $option['device_code'] === $selectedCode ? 'Dipilih' : 'Lihat' }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginasi (komponen ala shadcn) --}}
        <div class="mt-6 flex flex-col items-center justify-between gap-3 sm:flex-row">
            <p class="text-xs text-[var(--color-text-muted)]">
                Menampilkan {{ $deviceTable->firstItem() }}–{{ $deviceTable->lastItem() }} dari {{ $deviceTable->total() }} device
            </p>
            <x-pagination :paginator="$deviceTable" />
        </div>
    </x-card>
    @endif

    {{-- Realtime via Reverb: update baris tabel + KPI device terpilih tanpa reload --}}
    <style>
        @keyframes rtFlash {
            0%   { background-color: color-mix(in srgb, var(--color-accent) 22%, transparent); }
            100% { background-color: transparent; }
        }
        .rt-flash { animation: rtFlash 1.2s ease-out; border-radius: 6px; }
    </style>

    <script>
    (function () {
        const SELECTED = @json($selectedCode);
        const SNAPSHOT_URL = @json(route('dashboard.devices.snapshot', ['device' => $selectedCode]));
        const TELEMETRY_HISTORY = @json($selected['telemetry_history'] ?? []);
        const PREDICTION_CURVE = @json($selected['prediction_curve'] ?? []);
        const CURRENT_WATER = @json($selected['water_level'] ?? null);
        const TELEMETRY_META = {
            temperature:    { label: 'Suhu', unit: '°C', decimals: 1, color: '#ef4444' },
            humidity:       { label: 'Kelembapan', unit: '%', decimals: 0, color: '#0ea5e9' },
            air_pressure:   { label: 'Tekanan Udara', unit: 'hPa', decimals: 1, color: '#8b5cf6' },
            wind_speed:     { label: 'Kecepatan Angin', unit: 'm/s', decimals: 1, color: '#22c55e' },
            wind_direction: { label: 'Arah Angin', unit: '°', decimals: 0, color: '#f97316' },
        };
        const RISK = {
            aman:    { label: 'Aman',    c: 'var(--color-aman)' },
            waspada: { label: 'Waspada', c: 'var(--color-waspada)' },
            siaga:   { label: 'Siaga',   c: 'var(--color-siaga)' },
            bahaya:  { label: 'Bahaya',  c: 'var(--color-bahaya)' },
        };
        let telemetryChart = null;
        let predictionChart = null;
        let activeTelemetry = @json($prefs['chart_metric']);
        let activeRangeMinutes = @json($prefs['chart_range']); // 0 = tampilkan semua data
        let lastSnapshotAt = null;

        function setConnectionState(label, active) {
            const el = document.querySelector('[data-rt-connection]');
            if (!el) return;

            el.textContent = label;
            el.style.borderColor = active
                ? 'color-mix(in srgb, var(--color-aman) 45%, transparent)'
                : 'var(--color-border)';
            el.style.color = active ? 'var(--color-aman)' : 'var(--color-text-muted)';
        }

        function badgeHTML(level, sm) {
            const r = RISK[level] || RISK.aman;
            const size = sm ? 'px-2 py-0.5 text-[10px]' : 'px-2.5 py-0.5 text-xs';
            return '<span class="inline-flex items-center gap-1.5 rounded-full border font-semibold ' + size + '"'
                + ' style="border-color: color-mix(in srgb, ' + r.c + ' 40%, transparent);'
                + ' background-color: color-mix(in srgb, ' + r.c + ' 12%, transparent); color: ' + r.c + ';">'
                + '<span class="h-1.5 w-1.5 rounded-full" style="background-color: ' + r.c + ';"></span>'
                + r.label + '</span>';
        }

        function statusHTML(online) {
            const c = online ? 'var(--color-aman)' : 'var(--color-text-muted)';
            const pulse = online ? ' status-pulse' : '';
            return '<span class="relative inline-flex items-center gap-2">'
                + '<span class="relative flex h-2 w-2 rounded-full' + pulse + '" style="color: ' + c + '; background-color: ' + c + ';"></span>'
                + '<span class="font-mono text-xs text-[var(--color-text-muted)]">' + (online ? 'Online' : 'Offline') + '</span></span>';
        }

        function flash(el) {
            if (!el) return;
            el.classList.remove('rt-flash');
            void el.offsetWidth; // paksa restart animasi
            el.classList.add('rt-flash');
        }

        const fmt = (v, d) => (v === null || v === undefined) ? '—' : Number(v).toFixed(d);

        function setMetric(selector, value, decimals) {
            const el = document.querySelector(selector);
            if (!el) return;

            el.firstChild.nodeValue = fmt(value, decimals);
            flash(el);
        }

        function setTelemetry(key, value) {
            const el = document.querySelector('[data-rt-telemetry="' + key + '"]');
            if (!el) return;

            const decimals = Number(el.dataset.decimals || 0);
            el.firstChild.nodeValue = fmt(value, decimals);
            flash(el);
        }

        function inActiveRange(timestamp) {
            if (activeRangeMinutes === 0) return true; // "Semua"
            return timestamp >= Date.now() - activeRangeMinutes * 60000;
        }

        function telemetrySeries(key) {
            const meta = TELEMETRY_META[key] || TELEMETRY_META.temperature;

            return [{
                name: meta.label,
                data: (TELEMETRY_HISTORY[key] || []).filter(function (point) {
                    return point[1] !== null && point[1] !== undefined && inActiveRange(point[0]);
                }),
            }];
        }

        function selectRange(minutes) {
            activeRangeMinutes = minutes;

            if (telemetryChart) {
                telemetryChart.updateSeries(telemetrySeries(activeTelemetry), true);
            }
        }

        function selectTelemetry(key) {
            activeTelemetry = key;

            document.querySelectorAll('[data-telemetry-tab]').forEach(function (tab) {
                const active = tab.dataset.telemetryTab === key;
                tab.className = active
                    ? 'inline-flex h-8 items-center rounded-lg border border-[var(--color-accent)] bg-[var(--color-accent)] px-3 text-xs font-medium text-[var(--color-accent-foreground)] transition-colors'
                    : 'inline-flex h-8 items-center rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-xs font-medium transition-colors hover:bg-[var(--color-surface-2)]';
            });

            document.querySelectorAll('[data-telemetry-card]').forEach(function (card) {
                card.classList.toggle('ring-1', card.dataset.telemetryCard === key);
                card.classList.toggle('ring-[var(--color-accent)]', card.dataset.telemetryCard === key);
            });

            if (telemetryChart) {
                const meta = TELEMETRY_META[key] || TELEMETRY_META.temperature;
                telemetryChart.updateOptions({
                    colors: [meta.color],
                    markers: {
                        colors: [meta.color],
                        strokeColors: 'var(--color-surface)',
                    },
                    yaxis: {
                        labels: {
                            formatter: function (value) {
                                return fmt(value, meta.decimals);
                            },
                        },
                    },
                    tooltip: {
                        y: {
                            formatter: function (value) {
                                return fmt(value, meta.decimals) + ' ' + meta.unit;
                            },
                        },
                    },
                }, false, false);
                telemetryChart.updateSeries(telemetrySeries(key), true);
            }
        }

        function initTelemetryChart() {
            const target = document.querySelector('#telemetryChart');
            if (!target || !window.ApexCharts) return;

            const meta = TELEMETRY_META[activeTelemetry];
            telemetryChart = new window.ApexCharts(target, {
                chart: {
                    type: 'area',
                    height: 288,
                    toolbar: { show: false },
                    animations: { enabled: true },
                    background: 'transparent',
                },
                series: telemetrySeries(activeTelemetry),
                colors: [meta.color],
                stroke: { width: 2.5, curve: 'smooth' },
                markers: {
                    size: 4,
                    strokeWidth: 2,
                    colors: [meta.color],
                    strokeColors: 'var(--color-surface)',
                    hover: { size: 7 },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.45,
                        opacityFrom: 0.42,
                        opacityTo: 0.08,
                        stops: [0, 70, 100],
                    },
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: 'var(--color-border)',
                    strokeDashArray: 4,
                },
                xaxis: {
                    type: 'datetime',
                    labels: { style: { colors: 'var(--color-text-muted)' }, datetimeUTC: false },
                    axisBorder: { color: 'var(--color-border)' },
                    axisTicks: { color: 'var(--color-border)' },
                },
                yaxis: {
                    labels: {
                        style: { colors: 'var(--color-text-muted)' },
                        formatter: function (value) {
                            return fmt(value, meta.decimals);
                        },
                    },
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    x: { format: 'HH:mm' },
                    y: {
                        formatter: function (value) {
                            return fmt(value, meta.decimals) + ' ' + meta.unit;
                        },
                    },
                },
                noData: {
                    text: 'Histori telemetri belum tersedia',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: { color: 'var(--color-text-muted)' },
                },
            });

            telemetryChart.render();
            document.querySelectorAll('[data-telemetry-tab]').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    selectTelemetry(tab.dataset.telemetryTab);
                });
            });
            const rangeSelect = document.querySelector('[data-range-select]');
            if (rangeSelect) {
                rangeSelect.addEventListener('change', function () {
                    selectRange(Number(this.value));
                });
            }
        }

        function predictionSeries() {
            const points = [];

            // Titik jangkar "sekarang" dari pembacaan air terbaru, supaya kurva
            // menyambung dari kondisi aktual ke proyeksi ke depan (gaya forecast).
            if (CURRENT_WATER !== null && CURRENT_WATER !== undefined) {
                points.push([Date.now(), Number(CURRENT_WATER)]);
            }

            PREDICTION_CURVE.forEach(function (p) {
                points.push([p.at_ts, Number(p.value)]);
            });

            return [{ name: 'Prediksi', data: points }];
        }

        function initPredictionChart() {
            const target = document.querySelector('#predictionChart');
            if (!target || !window.ApexCharts || !PREDICTION_CURVE.length) return;

            const accent = '#6366f1';
            predictionChart = new window.ApexCharts(target, {
                chart: {
                    type: 'area',
                    height: 224,
                    toolbar: { show: false },
                    animations: { enabled: true },
                    background: 'transparent',
                },
                series: predictionSeries(),
                colors: [accent],
                stroke: { width: 2.5, curve: 'smooth', dashArray: 5 },
                markers: {
                    size: 4,
                    strokeWidth: 2,
                    colors: [accent],
                    strokeColors: 'var(--color-surface)',
                    hover: { size: 7 },
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 0.45,
                        opacityFrom: 0.35,
                        opacityTo: 0.05,
                        stops: [0, 70, 100],
                    },
                },
                dataLabels: { enabled: false },
                grid: {
                    borderColor: 'var(--color-border)',
                    strokeDashArray: 4,
                },
                xaxis: {
                    type: 'datetime',
                    labels: { style: { colors: 'var(--color-text-muted)' }, datetimeUTC: false },
                    axisBorder: { color: 'var(--color-border)' },
                    axisTicks: { color: 'var(--color-border)' },
                },
                yaxis: {
                    labels: {
                        style: { colors: 'var(--color-text-muted)' },
                        formatter: function (value) {
                            return fmt(value, 1);
                        },
                    },
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    x: { format: 'HH:mm' },
                    y: {
                        formatter: function (value) {
                            return fmt(value, 1) + ' cm';
                        },
                    },
                },
            });

            predictionChart.render();
        }

        function pushTelemetryPoint(key, value, timestamp) {
            if (value === null || value === undefined) return;

            const series = TELEMETRY_HISTORY[key] || [];
            const last = series[series.length - 1];

            if (last && timestamp < last[0]) return;
            if (last && timestamp === last[0]) {
                last[1] = Number(value);
            } else {
                series.push([timestamp, Number(value)]);
            }

            TELEMETRY_HISTORY[key] = series.slice(-120);

            if (key === activeTelemetry && telemetryChart) {
                telemetryChart.updateSeries(telemetrySeries(key), true);
            }
        }

        function applyUpdate(e, fromPoll) {
            const online = e.status === 'online';

            // baris tabel utk device ini (kalau kebetulan ada di halaman aktif)
            const row = document.querySelector('tr[data-code="' + e.device_code + '"]');
            if (row) {
                const w = row.querySelector('[data-rt-water]');
                if (w) { w.textContent = e.water_level === null ? '—' : fmt(e.water_level, 1) + ' cm'; flash(w); }
                const rk = row.querySelector('[data-rt-risk]');
                if (rk) rk.innerHTML = badgeHTML(e.risk, rk.hasAttribute('data-sm'));
                const st = row.querySelector('[data-rt-status]');
                if (st) st.innerHTML = statusHTML(online);
            }

            // KPI atas kalau device ini yang sedang dipilih
            if (e.device_code === SELECTED) {
                const timestamp = e.evaluated_at ? new Date(e.evaluated_at).getTime() : Date.now();
                if (lastSnapshotAt !== null && timestamp <= lastSnapshotAt && fromPoll) return;
                lastSnapshotAt = Math.max(lastSnapshotAt || 0, timestamp);

                setMetric('[data-rt-water-hero]', e.water_level, 1);
                setMetric('[data-rt-rise]', e.rise_rate, 2);
                setMetric('[data-rt-onshore]', e.onshore_wind, 2);
                setMetric('[data-rt-score]', e.risk_score, 0);

                setTelemetry('temperature', e.temperature);
                setTelemetry('humidity', e.humidity);
                setTelemetry('air_pressure', e.air_pressure);
                setTelemetry('wind_speed', e.wind_speed);
                setTelemetry('wind_direction', e.wind_direction);

                pushTelemetryPoint('temperature', e.temperature, timestamp);
                pushTelemetryPoint('humidity', e.humidity, timestamp);
                pushTelemetryPoint('air_pressure', e.air_pressure, timestamp);
                pushTelemetryPoint('wind_speed', e.wind_speed, timestamp);
                pushTelemetryPoint('wind_direction', e.wind_direction, timestamp);

                const bar = document.querySelector('[data-rt-waterbar]');
                if (bar) {
                    const pct = e.water_level === null ? 0 : Math.max(4, Math.min(100, ((200 - e.water_level) / 200) * 100));
                    bar.style.width = pct + '%';
                    bar.style.backgroundColor = (RISK[e.risk] || RISK.aman).c;
                }

                document.querySelectorAll('[data-rt-risk-hero]').forEach(function (el) {
                    el.innerHTML = badgeHTML(e.risk, el.hasAttribute('data-sm'));
                });

                const heroStatus = document.querySelector('[data-rt-status-hero]');
                if (heroStatus) heroStatus.innerHTML = statusHTML(online);

                const ev = document.querySelector('[data-rt-evaluated]');
                if (ev) ev.textContent = fromPoll ? 'sinkron otomatis' : 'dievaluasi baru saja';
            }
        }

        async function fetchSnapshot() {
            try {
                const response = await fetch(SNAPSHOT_URL, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                });

                if (!response.ok) return;

                const payload = await response.json();
                applyUpdate(payload, true);
            } catch (error) {
                setConnectionState('poll retry', false);
            }
        }

        (function bootChart() {
            if (!window.ApexCharts) { setTimeout(bootChart, 250); return; }
            initTelemetryChart();
            initPredictionChart();
        })();

        (function connect() {
            if (!window.Echo) { setTimeout(connect, 250); return; }
            setConnectionState('reverb ready', true);
            if (window.Echo.connector?.pusher?.connection) {
                window.Echo.connector.pusher.connection.bind('connected', function () {
                    setConnectionState('reverb live', true);
                });
                window.Echo.connector.pusher.connection.bind('disconnected', function () {
                    setConnectionState('polling', false);
                });
                window.Echo.connector.pusher.connection.bind('error', function () {
                    setConnectionState('polling', false);
                });
            }
            window.Echo.channel('rob-monitoring').listen('.device.risk.updated', applyUpdate);
        })();

        // Muat data awal sekali; update selanjutnya datang realtime via Reverb.
        fetchSnapshot();
    })();
    </script>

    @endif

</div>
@endsection
