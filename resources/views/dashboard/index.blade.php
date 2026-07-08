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
                            <span class="truncate font-mono text-xs" data-current-code>{{ $selected['device_code'] }}</span>
                        </span>
                        <x-heroicon-o-chevron-down class="h-4 w-4 opacity-60" />
                    </x-button>
                </x-slot:trigger>

                <div class="max-h-80 w-64 overflow-y-auto">
                    @foreach ($deviceOptions as $option)
                        <a
                            href="{{ request()->fullUrlWithQuery(['device' => $option['device_code']]) }}"
                            data-device-link="{{ $option['device_code'] }}"
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

    <div id="device-detail" class="space-y-6">
        @include('dashboard.partials.device-detail')
    </div>

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
                                    data-device-link="{{ $option['device_code'] }}"
                                    data-action-link
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

    {{-- Update via polling snapshot: perbarui baris tabel + KPI device terpilih tanpa reload --}}
    <style>
        @keyframes rtFlash {
            0%   { background-color: color-mix(in srgb, var(--color-accent) 22%, transparent); }
            100% { background-color: transparent; }
        }
        .rt-flash { animation: rtFlash 1.2s ease-out; border-radius: 6px; }
    </style>

    <script>
    (function () {
        // Dibuat `let` karena berubah saat ganti device tanpa reload.
        let SELECTED = @json($selectedCode);
        // URL snapshot & detail per-device; __CODE__ diganti kode device aktif.
        // Dibuat template (bukan URL beku) agar polling selalu mengikuti device terpilih.
        const SNAPSHOT_URL_TEMPLATE = @json(route('dashboard.devices.snapshot', ['device' => '__CODE__']));
        const DETAIL_URL = @json(route('dashboard.devices.detail', ['device' => '__CODE__']));
        function snapshotUrl() {
            return SNAPSHOT_URL_TEMPLATE.replace('__CODE__', encodeURIComponent(SELECTED));
        }
        let TELEMETRY_HISTORY = @json($selected['telemetry_history'] ?? []);
        let PREDICTION_CURVE = @json($selected['prediction_curve'] ?? []);
        let CURRENT_WATER = @json($selected['water_level'] ?? null);
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
                // ── Data sensor LIVE: selalu perbarui tiap poll, JANGAN di-gate
                // ke timestamp evaluasi fuzzy. Ketinggian air, telemetri & status
                // adalah pembacaan sensor langsung (sumber sama dgn baris tabel).
                // Kalau diikat ke evaluasi, saat worker antrean telat KPI ini beku
                // padahal tabel device tetap jalan — itulah bug "sering berubah di
                // tabel tapi tidak di card atas".
                setMetric('[data-rt-water-hero]', e.water_level, 1);

                setTelemetry('temperature', e.temperature);
                setTelemetry('humidity', e.humidity);
                setTelemetry('air_pressure', e.air_pressure);
                setTelemetry('wind_speed', e.wind_speed);
                setTelemetry('wind_direction', e.wind_direction);

                const bar = document.querySelector('[data-rt-waterbar]');
                if (bar) {
                    const pct = e.water_level === null ? 0 : Math.max(4, Math.min(100, ((200 - e.water_level) / 200) * 100));
                    bar.style.width = pct + '%';
                    bar.style.backgroundColor = (RISK[e.risk] || RISK.aman).c;
                }

                const heroStatus = document.querySelector('[data-rt-status-hero]');
                if (heroStatus) heroStatus.innerHTML = statusHTML(online);

                // ── Turunan evaluasi fuzzy: hanya berubah saat ADA evaluasi baru.
                // Diikat ke evaluated_at agar tidak dobel-proses & titik grafik
                // telemetri tidak menumpuk di timestamp yang sama.
                const timestamp = e.evaluated_at ? new Date(e.evaluated_at).getTime() : Date.now();
                if (lastSnapshotAt !== null && timestamp <= lastSnapshotAt && fromPoll) return;
                lastSnapshotAt = Math.max(lastSnapshotAt || 0, timestamp);

                setMetric('[data-rt-rise]', e.rise_rate, 2);
                setMetric('[data-rt-onshore]', e.onshore_wind, 2);
                setMetric('[data-rt-score]', e.risk_score, 0);

                pushTelemetryPoint('temperature', e.temperature, timestamp);
                pushTelemetryPoint('humidity', e.humidity, timestamp);
                pushTelemetryPoint('air_pressure', e.air_pressure, timestamp);
                pushTelemetryPoint('wind_speed', e.wind_speed, timestamp);
                pushTelemetryPoint('wind_direction', e.wind_direction, timestamp);

                document.querySelectorAll('[data-rt-risk-hero]').forEach(function (el) {
                    el.innerHTML = badgeHTML(e.risk, el.hasAttribute('data-sm'));
                });

                const ev = document.querySelector('[data-rt-evaluated]');
                if (ev) ev.textContent = fromPoll ? 'sinkron otomatis' : 'dievaluasi baru saja';
            }
        }

        async function fetchSnapshot() {
            try {
                const response = await fetch(snapshotUrl(), {
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

        // Polling snapshot: dashboard disegarkan berkala dari server (tanpa WebSocket).
        const POLL_INTERVAL_MS = 20000;
        setConnectionState('polling', true);
        setInterval(fetchSnapshot, POLL_INTERVAL_MS);

        // ── Ganti device tanpa reload (in-place) ─────────────────────────────
        // Ambil potongan HTML "detail device" dari server, tukar di tempat,
        // bangun ulang chart lokal. Aset TIDAK dimuat ulang.

        function updateSelectionUI(code) {
            // Label pada trigger dropdown.
            const trigger = document.querySelector('[data-current-code]');
            if (trigger) trigger.textContent = code;

            // Highlight item di dropdown (link tanpa data-action-link).
            document.querySelectorAll('[data-device-link]:not([data-action-link])').forEach(function (a) {
                a.classList.toggle('bg-[var(--color-surface-2)]', a.dataset.deviceLink === code);
            });

            // Highlight baris tabel.
            document.querySelectorAll('tr[data-code]').forEach(function (tr) {
                tr.classList.toggle('bg-[var(--color-surface-2)]', tr.dataset.code === code);
            });

            // Tombol aksi tabel: teks + gaya "Dipilih" vs "Lihat".
            document.querySelectorAll('[data-action-link]').forEach(function (a) {
                const sel = a.dataset.deviceLink === code;
                a.textContent = sel ? 'Dipilih' : 'Lihat';
                a.classList.toggle('border-transparent', sel);
                a.classList.toggle('bg-[var(--color-surface-2)]', sel);
                a.classList.toggle('text-[var(--color-text)]', sel);
                a.classList.toggle('border-[var(--color-border)]', !sel);
                a.classList.toggle('bg-[var(--color-surface)]', !sel);
                a.classList.toggle('hover:bg-[var(--color-surface-2)]', !sel);
            });
        }

        let switching = false;
        async function switchDevice(code, push) {
            if (!code || code === SELECTED || switching) return;
            switching = true;

            const region = document.getElementById('device-detail');
            if (region) region.style.opacity = '0.5';

            try {
                const res = await fetch(DETAIL_URL.replace('__CODE__', encodeURIComponent(code)), {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                });
                if (!res.ok) { window.location = '?device=' + encodeURIComponent(code); return; }

                const data = await res.json();

                // Buang chart lama (DOM-nya akan diganti innerHTML).
                if (telemetryChart) { telemetryChart.destroy(); telemetryChart = null; }
                if (predictionChart) { predictionChart.destroy(); predictionChart = null; }

                region.innerHTML = data.html;

                // Perbarui state modul.
                SELECTED = data.code;
                TELEMETRY_HISTORY = data.telemetry_history || {};
                PREDICTION_CURVE = data.prediction_curve || [];
                CURRENT_WATER = (data.water_level === undefined ? null : data.water_level);
                lastSnapshotAt = null;

                // Bangun ulang chart, lalu kembalikan pilihan tab & rentang yang aktif.
                initTelemetryChart();
                initPredictionChart();
                selectTelemetry(activeTelemetry);
                const rangeSel = document.querySelector('[data-range-select]');
                if (rangeSel) rangeSel.value = String(activeRangeMinutes);

                updateSelectionUI(data.code);

                if (push !== false) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('device', data.code);
                    history.pushState({ device: data.code }, '', url);
                }
            } catch (e) {
                window.location = '?device=' + encodeURIComponent(code); // fallback: reload penuh
            } finally {
                if (region) region.style.opacity = '';
                switching = false;
            }
        }

        // Intersep klik pada semua link device (dropdown + tabel).
        document.addEventListener('click', function (ev) {
            const link = ev.target.closest('[data-device-link]');
            if (!link) return;
            ev.preventDefault();

            // Tutup dropdown Alpine bila link berada di dalamnya.
            const root = link.closest('[x-data]');
            if (root && window.Alpine) {
                try { const d = window.Alpine.$data(root); if (d && 'open' in d) d.open = false; } catch (_) {}
            }

            switchDevice(link.dataset.deviceLink, true);
        });

        // Tombol back/forward browser → ikut berpindah tanpa menambah histori.
        window.addEventListener('popstate', function () {
            const code = new URL(window.location.href).searchParams.get('device');
            if (code && code !== SELECTED) switchDevice(code, false);
        });

        // Muat data awal sekali; update selanjutnya datang lewat polling berkala.
        fetchSnapshot();
    })();
    </script>

    @endif

</div>
@endsection
