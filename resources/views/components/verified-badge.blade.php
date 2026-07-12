@props([
    'user',            // model User
    'size' => 'sm',    // sm | md
])

@php
    $verified = $user?->hasVerifiedEmail();
    $px = $size === 'md' ? 'h-4 w-4' : 'h-3.5 w-3.5';
@endphp

@if ($verified)
    <span
        {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center text-[var(--color-aman)]']) }}
        title="Email terverifikasi"
        aria-label="Email terverifikasi"
    >
        {{-- ikon check bulat solid --}}
        <svg class="{{ $px }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z" clip-rule="evenodd" />
        </svg>
    </span>
@else
    <span
        {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center text-[var(--color-text-muted)]']) }}
        title="Email belum diverifikasi"
        aria-label="Email belum diverifikasi"
    >
        {{-- ikon tanda seru bulat (opsional; hapus blok @else kalau tak mau ikon saat belum verifikasi) --}}
        <svg class="{{ $px }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0 1 1 0 01-2 0zm.25-7.75a.75.75 0 011.5 0v4.5a.75.75 0 01-1.5 0v-4.5z" clip-rule="evenodd" />
        </svg>
    </span>
@endif
