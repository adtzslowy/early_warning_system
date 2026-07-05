@extends('layouts.app')

@section('title', 'Edit Device')
@section('page-title', 'Edit Device')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Devices', 'href' => route('devices')],
        ['label' => $device->device_code],
    ]" />

    <x-page-header
        title="Edit Device"
        :description="$device->name"
    />

    <form method="POST" action="{{ route('devices.update', $device) }}" class="space-y-6">
        @csrf
        @method('PATCH')

        <x-card title="Data Device" subtitle="perbarui informasi alat">
            @include('device.partials.form')
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button href="{{ route('devices') }}" variant="outline">Batal</x-button>
            <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
        </div>
    </form>
</div>
@endsection
