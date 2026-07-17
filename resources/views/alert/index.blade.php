@extends('layouts.app')

@section('title', 'Alert')
@section('page-title', 'Alert')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Alert']
    ]"/>
@endsection

@section('content')
    <div class="space-y-6">
        <x-page-header title="Alert" description="Daftar alert dari semua perangkat"></x-page-header>

        @if (session('status'))
            <div class="flex items-center gap-2 rounded-lg border border-[color-mix(in_srgb,var(--color-aman)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-aman)_12%,transparent)] px-4 py-3 text-sm text-[var(--color-aman)]">
                <x-heroicon-o-check-circle class="h-4 w-4 shrink-0"/>
                {{ session('status') }}
            </div>
        @endif

        <!-- Filter Section -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <form method="GET" class="flex gap-4 flex-wrap">
                <div class="flex-1 min-w-48">
                    <label for="risk_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Filter by Risk Level
                    </label>
                    <select name="risk_level" id="risk_level" class="w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                        <option value="">All Levels</option>
                        @foreach ($riskLevels as $level)
                            <option value="{{ $level }}" @selected($selectedRiskLevel === $level)>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-48">
                    <label for="device" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Filter by Device
                    </label>
                    <select name="device" id="device" class="w-full rounded-md border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                        <option value="">All Devices</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}" @selected($selectedDevice === (string)$device->id)>
                                {{ $device->device_code }} - {{ $device->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 text-white font-medium hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="{{ route('alerts') }}" class="px-4 py-2 rounded-md bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white font-medium hover:bg-gray-300">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Alerts Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Device</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Risk Level</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Message</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Triggered At</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($alerts as $alert)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3">
                                <a href="{{ route('dashboard', ['device' => $alert->device->device_code]) }}" class="text-blue-600 hover:underline">
                                    {{ $alert->device->device_code }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 rounded text-xs font-semibold
                                    @if ($alert->risk_level === 'Waspada')
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif ($alert->risk_level === 'Siaga')
                                        bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @else
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif
                                ">
                                    {{ $alert->risk_level }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $alert->message }}
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                {{ $alert->triggered_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($alert->isAcknowledged())
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Acknowledged
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Active
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No alerts found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $alerts->links() }}
        </div>
    </div>
@endsection
