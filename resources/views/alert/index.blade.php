@extends('layouts.app')

@section('title', 'Alert')
@section('page-title', 'Alert')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Alert']
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <x-page-header
        title="Alert"
        description="Daftar alert dari semua perangkat yang dimonitor."
    />

    @if (session('status'))
        <div class="flex items-center gap-2 rounded-lg border border-[color-mix(in_srgb,var(--color-aman)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-aman)_12%,transparent)] px-4 py-3 text-sm text-[var(--color-aman)]">
            <x-heroicon-o-check-circle class="h-4 w-4 shrink-0" />
            {{ session('status') }}
        </div>
    @endif

    <x-card padding="p-0">
        {{-- Filter toolbar --}}
        <div class="border-b border-[var(--color-border)] p-4">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-2">
                <div class="flex-1 min-w-48">
                    <label for="risk_level" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Risk Level
                    </label>
                    <select name="risk_level" id="risk_level"
                        class="h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                        <option value="">Semua level</option>
                        @foreach ($riskLevels as $level)
                            <option value="{{ $level }}" @selected($selectedRiskLevel === $level)>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-48">
                    <label for="device" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Device
                    </label>
                    <select name="device" id="device"
                        class="h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                        <option value="">Semua device</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @selected($selectedDevice === (string)$device->id)>
                                {{ $device->device_code }} - {{ $device->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <x-button type="submit" variant="primary">Terapkan</x-button>
                    @if ($selectedRiskLevel !== '' || $selectedDevice !== '')
                        <x-button href="{{ route('alerts') }}" variant="ghost">Reset</x-button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Alerts table --}}
        @if ($alerts->isEmpty())
            <x-empty-state
                icon="bell"
                title="Belum ada alert"
                message="Semua perangkat dalam kondisi aman."
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-[var(--color-border)] text-left text-xs uppercase tracking-wider text-[var(--color-text-muted)]">
                        <tr>
                            <th class="px-4 py-3 font-medium">Device</th>
                            <th class="px-4 py-3 font-medium">Risk Level</th>
                            <th class="px-4 py-3 font-medium">Pesan</th>
                            <th class="px-4 py-3 font-medium">Dipicu</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($alerts as $alert)
                            <tr class="hover:bg-[var(--color-hover)]">
                                <td class="px-4 py-3">
                                    <a href="{{ route('dashboard', ['device' => $alert->device->device_code]) }}"
                                        class="font-medium text-[var(--color-accent)] hover:underline">
                                        {{ $alert->device->device_code }}
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <x-risk-badge :level="$alert->risk_level->value" />
                                </td>
                                <td class="px-4 py-3 text-[var(--color-text-muted)]">
                                    {{ $alert->message }}
                                </td>
                                <td class="px-4 py-3 text-[var(--color-text-muted)]">
                                    {{ $alert->triggered_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($alert->isAcknowledged())
                                        <x-risk-badge level="aman" size="sm">
                                            <x-slot:label>Acknowledged</x-slot:label>
                                        </x-risk-badge>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-xs font-semibold"
                                            style="border-color: color-mix(in srgb, var(--color-bahaya) 40%, transparent); background-color: color-mix(in srgb, var(--color-bahaya) 12%, transparent); color: var(--color-bahaya);">
                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: var(--color-bahaya);"></span>
                                            Aktif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex flex-col items-center justify-between gap-3 border-t border-[var(--color-border)] p-4 sm:flex-row">
            <p class="text-xs text-[var(--color-text-muted)]">
                Menampilkan {{ $alerts->firstItem() ?? 0 }}–{{ $alerts->lastItem() ?? 0 }} dari {{ $alerts->total() }} alert
            </p>
            <x-pagination :paginator="$alerts" />
        </div>
    </x-card>
</div>
@endsection
