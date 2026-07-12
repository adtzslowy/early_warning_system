@extends('layouts.app')
@section('content')
<div class="mx-auto max-w-md px-4 py-12 text-center">
    @if (session('blocked'))
        <div class="mb-4 rounded-md bg-[var(--color-bahaya)]/10 p-3 text-sm text-[var(--color-bahaya)]">
            Masa tenggang 3×24 jam telah habis. Akses dinonaktifkan sampai email Anda diverifikasi.
        </div>
    @endif

    <h1 class="text-lg font-semibold">Verifikasi Email Anda</h1>
    <p class="mt-2 text-sm text-[var(--color-text-muted)]">
        Kami telah mengirim tautan verifikasi ke <strong>{{ auth()->user()->email }}</strong>.
        Silakan verifikasi dalam <strong>3×24 jam</strong> sejak mendaftar agar akses tidak dinonaktifkan.
    </p>

    @if (session('status'))
        <p class="mt-3 text-sm text-[var(--color-aman)]">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-5">
        @csrf
        <button type="submit" class="rounded-md bg-[var(--color-primary)] px-4 py-2 text-sm text-white">
            Kirim Ulang Tautan Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
        @csrf
        <button type="submit" class="text-xs text-[var(--color-text-muted)] hover:underline">Keluar</button>
    </form>
</div>
@endsection
