{{--
    Form field bersama untuk tambah & edit device.
    Variabel:
      $device   → Device|null (null = mode tambah)
      $statuses → array<DeviceStatus>
--}}
@php $device = $device ?? null; @endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
    <div>
        <x-input
            label="Kode Device"
            name="device_code"
            value="{{ old('device_code', $device?->device_code) }}"
            placeholder="mis. RTU-KTP-01"
            :error="$errors->first('device_code')"
            @if ($device) readonly @endif
        />
        @if ($device)
            <p class="mt-1.5 text-xs text-[var(--color-text-muted)]">Kode tidak dapat diubah setelah dibuat.</p>
        @endif
    </div>

    <x-input
        label="Nama"
        name="name"
        value="{{ old('name', $device?->name) }}"
        placeholder="mis. Ketapang Muara 1"
        :error="$errors->first('name')"
    />

    <div class="sm:col-span-2">
        <x-input
            label="Lokasi"
            name="location"
            value="{{ old('location', $device?->location) }}"
            placeholder="mis. Dermaga Ketapang, Sampit"
            :error="$errors->first('location')"
        />
    </div>

    <x-input
        label="Latitude"
        name="latitude"
        type="number"
        step="any"
        value="{{ old('latitude', $device?->latitude) }}"
        placeholder="-2.5410000"
        :error="$errors->first('latitude')"
    />

    <x-input
        label="Longitude"
        name="longitude"
        type="number"
        step="any"
        value="{{ old('longitude', $device?->longitude) }}"
        placeholder="112.9510000"
        :error="$errors->first('longitude')"
    />

    <div class="space-y-1.5">
        <label for="status" class="text-sm font-medium leading-none">Status</label>
        <select id="status" name="status"
            class="flex h-9 w-full rounded-lg border border-[var(--color-input)] bg-transparent px-3 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
            @foreach ($statuses as $s)
                <option value="{{ $s->value }}" @selected(old('status', $device?->status?->value ?? 'offline') === $s->value)>
                    {{ $s->label() }}
                </option>
            @endforeach
        </select>
        @error('status')<p class="text-xs text-[var(--color-bahaya)]">{{ $message }}</p>@enderror
    </div>
</div>
