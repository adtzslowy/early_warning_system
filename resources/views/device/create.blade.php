@extends('layouts.app')

@section('title', 'Tambah Device')
@section('page-title', 'Tambah Device')

@section('content')
<div class="mx-auto space-y-6">

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Devices', 'href' => route('devices')],
        ['label' => 'Tambah'],
    ]" />

    <x-page-header
        title="Tambah Device"
        description="Daftarkan alat pemantau baru ke jaringan."
    />

    <form method="POST" action="{{ route('devices.store') }}" class="space-y-6">
        @csrf

        <x-card title="Data Device" subtitle="kode & nama wajib diisi">
            @include('device.partials.form')
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button href="{{ route('devices') }}" variant="outline">Batal</x-button>
            <x-button type="submit" variant="primary">Simpan Device</x-button>
        </div>
    </form>
</div>
@endsection
