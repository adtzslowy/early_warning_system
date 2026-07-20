@extends('layouts.app')

@section('title', 'Notification Log')
@section('page-title', 'Notification Log')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Notification Log']
    ]" />
@endsection

@section('content')
<div class="space-y-6">
    <x-page-header
        title="Notification Log"
        description="Riwayat notifikasi yang dikirim ke Telegram."
    />

    <x-card padding="p-0">
        {{-- Filter toolbar --}}
        <div class="border-b border-[var(--color-border)] p-4">
            <form method="GET" class="flex flex-col gap-3 sm:flex-row sm:items-end sm:gap-2">
                <div class="flex-1 min-w-48">
                    <label for="status" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Status
                    </label>
                    <select name="status" id="status"
                        class="h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected($selectedStatus === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 min-w-48">
                    <label for="device_id" class="block text-sm font-medium text-[var(--color-text)] mb-2">
                        Device
                    </label>
                    <select name="device_id" id="device_id"
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
                    @if ($selectedStatus !== '' || $selectedDevice !== '')
                        <x-button href="{{ route('notifications.log') }}" variant="ghost">Reset</x-button>
                    @endif
                </div>
            </form>
        </div>

        {{-- Notifications table --}}
        @if ($notifications->isEmpty())
            <x-empty-state
                icon="bell"
                title="Belum ada notifikasi"
                message="Tidak ada notifikasi yang dikirim."
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-[var(--color-border)] text-left text-xs uppercase tracking-wider text-[var(--color-text-muted)]">
                        <tr>
                            <th class="px-4 py-3 font-medium">Device</th>
                            <th class="px-4 py-3 font-medium">Chat ID</th>
                            <th class="px-4 py-3 font-medium">Pesan</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        @foreach ($notifications as $notification)
                            <tr class="hover:bg-[var(--color-hover)]">
                                <td class="px-4 py-3">
                                    @if ($notification->device)
                                        <a href="{{ route('dashboard', ['device' => $notification->device->device_code]) }}"
                                            class="font-medium text-[var(--color-accent)] hover:underline">
                                            {{ $notification->device->device_code }}
                                        </a>
                                    @else
                                        <span class="text-[var(--color-text-muted)]">Device deleted</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-mono text-sm">
                                    {{ $notification->telegram_chat_id ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-[var(--color-text-muted)] truncate max-w-xs" title="{{ $notification->message }}">
                                    {{ substr($notification->message, 0, 50) }}...
                                </td>
                                <td class="px-4 py-3">
                                    @if ($notification->isSent())
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            style="border-color: var(--color-aman); background-color: color-mix(in srgb, var(--color-aman) 12%, transparent); color: var(--color-aman);">
                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: var(--color-aman);"></span>
                                            Sent
                                        </span>
                                    @elseif ($notification->isFailed())
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            style="border-color: var(--color-bahaya); background-color: color-mix(in srgb, var(--color-bahaya) 12%, transparent); color: var(--color-bahaya);">
                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: var(--color-bahaya);"></span>
                                            Failed
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                            style="border-color: var(--color-waspada); background-color: color-mix(in srgb, var(--color-waspada) 12%, transparent); color: var(--color-waspada);">
                                            <span class="h-1.5 w-1.5 rounded-full" style="background-color: var(--color-waspada);"></span>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-[var(--color-text-muted)]">
                                    {{ $notification->created_at->timezone('Asia/Jakarta')->format('d M Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex flex-col items-center justify-between gap-3 border-t border-[var(--color-border)] p-4 sm:flex-row">
            <p class="text-xs text-[var(--color-text-muted)]">
                Menampilkan {{ $notifications->firstItem() ?? 0 }}–{{ $notifications->lastItem() ?? 0 }} dari {{ $notifications->total() }} notifikasi
            </p>
            <x-pagination :paginator="$notifications" />
        </div>
    </x-card>
</div>
@endsection
