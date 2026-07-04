@extends('layouts.app')

@section('title', 'Setting Profil')
@section('page-title', 'Setting Profil')

@section('content')
<div class="mx-auto space-y-6">

    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'href' => route('dashboard')],
        ['label' => 'Setting'],
        ['label' => 'Profil'],
    ]" />

    <x-page-header
        title="Setting"
        description="Kelola tampilan dashboard dan profil akunmu."
    />

    <div class="grid gap-6 lg:grid-cols-[200px_1fr]">
        <aside>@include('partials.settings-nav')</aside>

        <div class="space-y-6">
            @if (session('status'))
                <div class="rounded-lg border border-[color-mix(in_srgb,var(--color-aman)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-aman)_12%,transparent)] px-4 py-3 text-sm text-[var(--color-aman)]">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('settings.profile.update') }}" enctype="multipart/form-data"
                class="space-y-6" x-data="{ preview: null, fileName: '', showPassword: {{ $errors->has('password') ? 'true' : 'false' }} }">
                @csrf
                @method('PATCH')

                {{-- Foto profil --}}
                <x-card title="Foto Profil" subtitle="JPG, PNG, atau WEBP · maks 2 MB">
                    <div class="flex flex-col items-start gap-5 sm:flex-row sm:items-center">
                        {{-- Avatar / preview --}}
                        <div class="relative h-24 w-24 shrink-0 overflow-hidden rounded-full border border-[var(--color-border)] bg-[var(--color-surface-2)]">
                            {{-- Preview file baru (Alpine) --}}
                            <template x-if="preview">
                                <img :src="preview" alt="Preview" class="h-full w-full object-cover">
                            </template>
                            {{-- Foto tersimpan / inisial --}}
                            <div x-show="!preview" class="h-full w-full">
                                @if ($user->fotoUrl())
                                    <img src="{{ $user->fotoUrl() }}" alt="Foto profil" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-2xl font-semibold text-[var(--color-text-muted)]">
                                        {{ $user->initials() }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-2 text-sm font-medium transition-colors hover:bg-[var(--color-surface-2)]">
                                <x-heroicon-o-arrow-up-tray class="h-4 w-4" />
                                <span x-text="fileName || 'Pilih foto'">Pilih foto</span>
                                <input type="file" name="foto_profil" accept="image/jpeg,image/png,image/webp" class="sr-only"
                                    @change="
                                        const f = $event.target.files[0];
                                        fileName = f ? f.name : '';
                                        preview = f ? URL.createObjectURL(f) : null;
                                    ">
                            </label>

                            @if ($user->foto_profil)
                                <label class="flex items-center gap-2 text-sm text-[var(--color-text-muted)]">
                                    <input type="checkbox" name="hapus_foto" value="1"
                                        class="h-4 w-4 rounded border-[var(--color-border)] accent-[var(--color-bahaya)]">
                                    Hapus foto saat ini
                                </label>
                            @endif

                            @error('foto_profil')
                                <p class="text-sm text-[var(--color-bahaya)]">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-card>

                {{-- Data akun (nama & email read-only untuk saat ini) + ganti password --}}
                <x-card title="Data Akun" subtitle="nama & email tampil read-only">
                    <x-slot:header>
                        <button type="button" @click="showPassword = !showPassword"
                            class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-xs font-medium transition-colors hover:bg-[var(--color-surface-2)]">
                            <x-heroicon-o-key class="h-3.5 w-3.5" />
                            <span x-text="showPassword ? 'Batal' : 'Ganti Password'">Ganti Password</span>
                        </button>
                    </x-slot:header>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <p class="text-xs font-medium text-[var(--color-text-muted)]">Nama</p>
                            <p class="mt-1 text-sm">{{ $user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-[var(--color-text-muted)]">Email</p>
                            <p class="mt-1 text-sm">{{ $user->email }}</p>
                        </div>
                    </div>

                    {{-- Field ganti password muncul saat mode edit --}}
                    <div x-show="showPassword" x-cloak class="mt-5 border-t border-[var(--color-border)] pt-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="password" class="mb-1.5 block text-sm font-medium">Password Baru</label>
                                <input id="password" type="password" name="password" autocomplete="new-password" placeholder="min. 8 karakter"
                                    class="h-10 w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm outline-none transition-colors focus-visible:border-[var(--color-accent)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                                @error('password')<p class="mt-1 text-sm text-[var(--color-bahaya)]">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Konfirmasi Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="ulangi password baru"
                                    class="h-10 w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm outline-none transition-colors focus-visible:border-[var(--color-accent)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-[var(--color-text-muted)]">Kosongkan bila tidak ingin mengganti password.</p>
                    </div>
                </x-card>

                <div class="flex items-center justify-end gap-3">
                    <x-button href="{{ route('dashboard') }}" variant="outline">Batal</x-button>
                    <x-button type="submit" variant="primary">Simpan Profil</x-button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
