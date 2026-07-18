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
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @if ($prefs['cards']['telemetry'])
        <x-card title="Telemetri" subtitle="pembacaan sensor terbaru">
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
                <div id="telemetryChart" class="h-72 w-full">
                    <div class="flex h-full items-center justify-center">
                        <p class="text-sm text-[var(--color-text-muted)]">Loading chart...</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5">
                @foreach ($telemetry as [$key, $label, $value, $unit, $decimals])
                    <div class="group rounded-lg border border-[var(--color-border)] bg-gradient-to-br from-[var(--color-surface-2)] to-[var(--color-surface)] p-3 transition-all duration-200 hover:border-[var(--color-accent)] hover:shadow-lg" data-telemetry-card="{{ $key }}">
                        <p class="text-xs font-medium text-[var(--color-text-muted)] group-hover:text-[var(--color-accent)] transition-colors">{{ $label }}</p>
                        <p class="mt-2 font-mono text-lg font-bold" data-rt-telemetry="{{ $key }}" data-decimals="{{ $decimals }}">
                            {{ $value !== null ? number_format($value, $decimals) : '—' }}<span class="text-xs font-normal text-[var(--color-text-muted)]"> {{ $unit }}</span>
                        </p>
                    </div>
                @endforeach
            </div>
        </x-card>
        @endif

        @if ($prefs['cards']['prediction'])
        <x-card title="Prediksi Ketinggian Air" subtitle="Proyeksi muka air laut 2 jam ke depan">
            @if (empty($selected['prediction_curve']))
                <x-empty-state icon="chart" title="Prediksi belum aktif" message="Menunggu minimal 10 data sensor untuk membangun model prediksi." />
            @else
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-[var(--color-text-muted)]">Prediksi Menggunakan</p>
                        <p class="text-sm text-[var(--color-text)]">Linear Regression · 8 titik proyeksi · 3 hari data terakhir</p>
                    </div>
                    <div class="flex items-center gap-2 rounded-lg bg-[var(--color-surface-2)] px-3 py-2">
                        <svg class="h-4 w-4 text-[var(--color-accent)]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 3.062v6.418a3 3 0 01-.879 2.121l-6.798 6.799a1 1 0 01-1.414 0l-6.798-6.799A3 3 0 01.734 12.935V6.517a3.066 3.066 0 012.812-3.062zM9 12a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs font-medium">Terpercaya</span>
                    </div>
                </div>
                <div id="predictionChart" class="h-64 w-full">
                    <div class="flex h-full items-center justify-center">
                        <p class="text-sm text-[var(--color-text-muted)]">Loading chart...</p>
                    </div>
                </div>

                {{-- Strip forecast tiles --}}
                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[var(--color-text)]">Proyeksi Kenaikan Air</p>
                            <p class="text-xs text-[var(--color-text-muted)]">Prediksi per horizon waktu</p>
                        </div>
                    </div>
                    <div data-rt-forecast class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-8">
                        @foreach ($selected['prediction_curve'] as $point)
                            @php
                                $value = (float) $point['value'];
                                $currentWater = $selected['water_level'] ?? 0;
                                $trend = $value > $currentWater ? 'up' : ($value < $currentWater ? 'down' : 'stable');
                                $riskColor = match(true) {
                                    $value < 100 => 'var(--color-aman)',
                                    $value < 150 => 'var(--color-waspada)',
                                    $value < 200 => 'var(--color-siaga)',
                                    default => 'var(--color-bahaya)',
                                };
                                $bgColor = match(true) {
                                    $value < 100 => 'color-mix(in srgb, var(--color-aman) 12%, transparent)',
                                    $value < 150 => 'color-mix(in srgb, var(--color-waspada) 12%, transparent)',
                                    $value < 200 => 'color-mix(in srgb, var(--color-siaga) 12%, transparent)',
                                    default => 'color-mix(in srgb, var(--color-bahaya) 12%, transparent)',
                                };
                            @endphp
                            <div class="flex flex-col items-center rounded-lg border-2 p-3 text-center transition-all hover:shadow-md"
                                style="border-color: {{ $riskColor }}; background-color: {{ $bgColor }};">
                                <span class="text-[10px] font-semibold text-[var(--color-text-muted)] uppercase">+{{ $point['horizon'] }}m</span>
                                <span class="mt-2 font-mono text-sm font-bold" style="color: {{ $riskColor }};">
                                    {{ number_format($point['value'], 1) }}<span class="text-[10px] font-normal text-[var(--color-text-muted)]">cm</span>
                                </span>
                                <div class="mt-2 flex items-center gap-1">
                                    @if ($trend === 'up')
                                        <svg class="h-3.5 w-3.5" style="color: var(--color-bahaya);" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3.293 5.293a1 1 0 011.414 0L10 10.586l5.293-5.293a1 1 0 111.414 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414z" transform="rotate(180 10 10)" />
                                        </svg>
                                    @elseif ($trend === 'down')
                                        <svg class="h-3.5 w-3.5" style="color: var(--color-aman);" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3.293 5.293a1 1 0 011.414 0L10 10.586l5.293-5.293a1 1 0 111.414 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414z" />
                                        </svg>
                                    @else
                                        <svg class="h-3.5 w-3.5" style="color: var(--color-waspada);" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 3a1 1 0 011 1v8a1 1 0 11-2 0V4a1 1 0 011-1z" />
                                        </svg>
                                    @endif
                                </div>
                                <span class="mt-2 text-[9px] text-[var(--color-text-muted)]">{{ $point['at'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 rounded-lg bg-[var(--color-surface-2)] p-3">
                    <svg class="h-4 w-4 text-[var(--color-text-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-[11px] text-[var(--color-text-muted)]">Prediksi otomatis diperbarui saat ada data sensor baru · Waktu dalam WIB</p>
                </div>
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
