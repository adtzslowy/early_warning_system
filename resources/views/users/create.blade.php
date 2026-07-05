@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'User Managemen', 'href' => route('users')],
        ['label' => 'Tambah'],
    ]" />
@endsection

@section('content')
<div class="mx-auto space-y-6">

    <x-page-header
        title="Tambah User"
        description="Buat akun pengguna baru beserta role-nya."
    />

    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
        @csrf

        <x-card title="Data User" subtitle="semua field wajib diisi">
            @include('users.partials.form')
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button href="{{ route('users') }}" variant="outline">Batal</x-button>
            <x-button type="submit" variant="primary">Simpan User</x-button>
        </div>
    </form>
</div>
@endsection
