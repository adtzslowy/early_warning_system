{{--
    Region detail device terpilih (identitas + KPI + telemetri + prediksi + info).
    Dipakai saat load awal via @include DAN saat ganti device tanpa reload
    (dirender ulang oleh DashboardController@detail lalu ditukar via JS).
    Butuh variabel: $selected, $prefs.
--}}
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
                    ['wind_speed', 'Kecepatan Angin', $selected['wind_speed'], 'm/s', 1],
                    ['water_level', 'Ketinggian Air', $selected['water_level'], 'cm', 1],
                ];
            @endphp

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <label for="telemetrySelect" class="text-xs font-medium text-[var(--color-text-muted)]">Metrik</label>
                    <select
                        id="telemetrySelect"
                        data-telemetry-select
                        class="h-8 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-xs font-medium transition-colors hover:bg-[var(--color-surface-2)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]"
                    >
                        @foreach ($telemetry as [$key, $label, $value, $unit, $decimals])
                            <option value="{{ $key }}" @selected($key === $prefs['chart_metric'])>{{ $label }}</option>
                        @endforeach
                    </select>
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

            {{-- Widget Kecepatan & Arah Angin --}}
            <div class="mt-4 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface-2)] p-4">
                <p class="text-xs font-medium text-[var(--color-text-muted)]">Kecepatan & Arah Angin</p>
                <div class="mt-3 flex items-center justify-between gap-6">
                    <div class="flex flex-col">
                        <p class="text-sm text-[var(--color-text-muted)]">Kecepatan</p>
                        <p class="mt-1 font-mono text-2xl font-semibold" data-rt-telemetry="wind_speed" data-decimals="1">
                            {{ $selected['wind_speed'] !== null ? number_format($selected['wind_speed'], 1) : '—' }}<span class="text-sm text-[var(--color-text-muted)]"> m/s</span>
                        </p>
                    </div>

                    <div class="flex flex-col items-center">
                        <p class="text-sm text-[var(--color-text-muted)]">Arah</p>
                        <div class="relative mt-2 h-20 w-20">
                            <svg viewBox="0 0 100 100" class="h-full w-full" style="filter: drop-shadow(0 0 1px var(--color-text-muted));">
                                {{-- Lingkaran kompas --}}
                                <circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="1" opacity="0.3"/>

                                {{-- Panah arah angin --}}
                                <g data-wind-arrow transform="rotate({{ $selected['wind_direction'] ?? 0 }} 50 50)">
                                    {{-- Batang panah --}}
                                    <line x1="50" y1="15" x2="50" y2="45" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    {{-- Kepala panah --}}
                                    <polygon points="50,10 45,20 55,20" fill="currentColor"/>
                                </g>

                                {{-- Titik pusat --}}
                                <circle cx="50" cy="50" r="3" fill="currentColor"/>
                            </svg>
                        </div>
                        <p class="mt-2 text-xs font-mono text-[var(--color-text-muted)]">
                            <span data-rt-telemetry="wind_direction" data-decimals="0">{{ $selected['wind_direction'] !== null ? number_format($selected['wind_direction'], 0) : '—' }}</span>°
                        </p>
                    </div>

                    <div class="flex flex-col text-right">
                        <p class="text-sm text-[var(--color-text-muted)]">Arah</p>
                        <p class="mt-1 text-lg font-semibold" data-wind-label>
                            @php
                                $windDir = $selected['wind_direction'];
                                $directions = ['U', 'TL', 'T', 'TG', 'G', 'BG', 'B', 'BD'];
                                $dirLabel = '—';
                                if ($windDir !== null) {
                                    $idx = round((($windDir % 360) + 22.5) / 45) % 8;
                                    $dirLabel = $directions[$idx];
                                }
                                echo $dirLabel;
                            @endphp
                        </p>
                        <p class="text-xs text-[var(--color-text-muted)]">Utara, Timur, dll</p>
                    </div>
                </div>
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
                <div data-rt-forecast class="-mx-2 mt-3 flex gap-2 overflow-x-auto overflow-y-hidden px-2 pb-1">
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
