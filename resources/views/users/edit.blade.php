@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'User Managemen', 'href' => route('users')],
        ['label' => $user->name],
    ]" />
@endsection

@section('content')
<div class="mx-auto space-y-6">

    <x-page-header
        title="Edit User"
        :description="$user->email"
    />

    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PATCH')

        <x-card title="Data User" subtitle="perbarui informasi akun">
            @include('users.partials.form')
        </x-card>

        <div class="flex items-center justify-end gap-3">
            <x-button href="{{ route('users') }}" variant="outline">Batal</x-button>
            <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
        </div>
    </form>
</div>
@endsection
