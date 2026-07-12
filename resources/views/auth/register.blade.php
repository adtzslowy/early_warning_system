<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Daftar' }} · EWS Banjir Rob</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">

    <script>
        if (localStorage.theme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes waveMove { from { transform: translateX(0); } to { transform: translateX(-50%); } }
        .wave { animation: waveMove linear infinite; will-change: transform; }
        .wave-1 { animation-duration: 12s; opacity: .55; }
        .wave-2 { animation-duration: 18s; opacity: .35; }
        .wave-3 { animation-duration: 26s; opacity: .22; }
        @keyframes floatY { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .float-slow { animation: floatY 6s ease-in-out infinite; }
        @media (prefers-reduced-motion: reduce) { .wave, .float-slow { animation: none; } }
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="min-h-screen font-dash text-[var(--color-text)] antialiased">

    <button
        type="button"
        x-data="{ dark: document.documentElement.classList.contains('dark') }"
        @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.theme = dark ? 'dark' : 'light'"
        class="fixed right-4 top-4 z-20 inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface-2)] hover:text-[var(--color-text)]"
        aria-label="Ganti tema terang/gelap"
    >
        <span x-show="!dark" x-cloak><x-heroicon-o-moon class="h-5 w-5" /></span>
        <span x-show="dark" x-cloak><x-heroicon-o-sun class="h-5 w-5" /></span>
    </button>

    <div class="grid min-h-screen lg:grid-cols-2">

        {{-- ══════════ PANEL HERO (kiri, desktop saja) ══════════ --}}
        <aside class="relative hidden overflow-hidden lg:flex lg:flex-col lg:justify-between p-10 xl:p-14
                      bg-[color-mix(in_srgb,var(--color-accent)_92%,black)] text-[var(--color-accent-foreground)]">

            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-0">
                <div class="absolute -top-20 -left-10 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute top-1/3 right-0 h-56 w-56 rounded-full bg-black/20 blur-3xl"></div>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15 backdrop-blur">
                    <x-heroicon-o-signal class="h-6 w-6" />
                </div>
                <span class="font-display text-lg font-bold tracking-tight">EWS Banjir Rob</span>
            </div>

            <div class="relative z-10 max-w-sm">
                <h2 class="font-display text-3xl font-bold leading-tight xl:text-4xl">
                    Gabung &amp; pantau risiko banjir rob wilayahmu.
                </h2>
                <ul class="mt-8 space-y-4 text-sm text-white/90">
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                            <x-heroicon-o-bolt class="h-4 w-4" />
                        </span>
                        Peringatan dini otomatis berbasis multi-faktor
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                            <x-heroicon-o-device-phone-mobile class="h-4 w-4" />
                        </span>
                        Akses dari mana saja lewat browser
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/15">
                            <x-heroicon-o-shield-check class="h-4 w-4" />
                        </span>
                        Verifikasi email untuk keamanan akun
                    </li>
                </ul>
            </div>

            <div class="relative z-10 flex items-center gap-2 text-xs text-white/80">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-white/70"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-white"></span>
                </span>
                Memantau Perairan Ketapang — Selat Karimata
            </div>

            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 bottom-0 h-40 overflow-hidden">
                <svg class="wave wave-1 absolute bottom-0 h-24 w-[200%]" viewBox="0 0 1440 120" preserveAspectRatio="none" fill="currentColor">
                    <path d="M0,64 C240,120 480,0 720,48 C960,96 1200,24 1440,64 L1440,120 L0,120 Z"/>
                </svg>
                <svg class="wave wave-2 absolute bottom-0 h-28 w-[200%]" viewBox="0 0 1440 120" preserveAspectRatio="none" fill="currentColor">
                    <path d="M0,80 C300,20 600,120 900,64 C1140,20 1320,96 1440,72 L1440,120 L0,120 Z"/>
                </svg>
                <svg class="wave wave-3 absolute bottom-0 h-32 w-[200%]" viewBox="0 0 1440 120" preserveAspectRatio="none" fill="currentColor">
                    <path d="M0,96 C360,48 720,120 1080,80 C1260,60 1380,100 1440,88 L1440,120 L0,120 Z"/>
                </svg>
            </div>
        </aside>

        {{-- ══════════ PANEL FORM (kanan) ══════════ --}}
        <main class="relative flex items-center justify-center overflow-hidden p-4 sm:p-6">

            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 lg:hidden">
                <div class="absolute -top-24 left-1/2 h-72 w-[36rem] max-w-[90vw] -translate-x-1/2 rounded-full bg-[var(--color-accent)]/10 blur-3xl"></div>
            </div>

            <div class="w-full max-w-md">
                <div class="mb-6 flex flex-col items-center text-center lg:hidden">
                    <div class="float-slow mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[var(--color-accent)] text-[var(--color-accent-foreground)]">
                        <x-heroicon-o-signal class="h-6 w-6" />
                    </div>
                    <h1 class="font-display text-xl font-bold tracking-tight">EWS Banjir Rob</h1>
                </div>

                <div class="mb-6 hidden lg:block">
                    <h1 class="font-display text-2xl font-bold tracking-tight">Buat akun baru</h1>
                    <p class="mt-1 text-sm text-[var(--color-text-muted)]">Daftar untuk mulai memantau risiko banjir rob.</p>
                </div>

                <div class="card p-6 sm:p-8">

                    {{-- Tombol Google --}}
                    <a href="{{ route('google.redirect') }}"
                       class="flex h-10 w-full items-center justify-center gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] text-sm font-medium transition-colors hover:bg-[var(--color-surface-2)]">
                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 0 1 0-4.2V7.06H2.18a11 11 0 0 0 0 9.88l3.66-2.84z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84C6.71 7.31 9.14 5.38 12 5.38z"/>
                        </svg>
                        Daftar dengan Google
                    </a>

                    <div class="my-5 flex items-center gap-3 text-xs text-[var(--color-text-muted)]">
                        <span class="h-px flex-1 bg-[var(--color-border)]"></span>
                        atau dengan email
                        <span class="h-px flex-1 bg-[var(--color-border)]"></span>
                    </div>

                    <form method="POST" action="{{ route('register.store') }}" class="space-y-4" x-data="{ show: false, show2: false }">
                        @csrf

                        {{-- Nama --}}
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-medium">Nama Lengkap</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[var(--color-text-muted)]">
                                    <x-heroicon-o-user class="h-4 w-4" />
                                </span>
                                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                    autocomplete="name" placeholder="Nama Anda"
                                    class="h-10 w-full rounded-lg border bg-[var(--color-surface)] pl-9 pr-3 text-sm outline-none transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)] @error('name') border-[var(--color-bahaya)] @else border-[var(--color-border)] focus-visible:border-[var(--color-accent)] @enderror">
                            </div>
                            @error('name') <p class="mt-1 text-xs text-[var(--color-bahaya)]">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[var(--color-text-muted)]">
                                    <x-heroicon-o-envelope class="h-4 w-4" />
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autocomplete="email" inputmode="email" placeholder="nama@email.com"
                                    class="h-10 w-full rounded-lg border bg-[var(--color-surface)] pl-9 pr-3 text-sm outline-none transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)] @error('email') border-[var(--color-bahaya)] @else border-[var(--color-border)] focus-visible:border-[var(--color-accent)] @enderror">
                            </div>
                            @error('email') <p class="mt-1 text-xs text-[var(--color-bahaya)]">{{ $message }}</p> @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[var(--color-text-muted)]">
                                    <x-heroicon-o-lock-closed class="h-4 w-4" />
                                </span>
                                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                                    autocomplete="new-password" placeholder="Minimal 8 karakter"
                                    class="h-10 w-full rounded-lg border bg-[var(--color-surface)] pl-9 pr-10 text-sm outline-none transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)] @error('password') border-[var(--color-bahaya)] @else border-[var(--color-border)] focus-visible:border-[var(--color-accent)] @enderror">
                                <button type="button" @click="show = !show" tabindex="-1"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-text-muted)] transition-colors hover:text-[var(--color-text)]"
                                    :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                    <span x-show="!show"><x-heroicon-o-eye class="h-4 w-4" /></span>
                                    <span x-show="show" x-cloak><x-heroicon-o-eye-slash class="h-4 w-4" /></span>
                                </button>
                            </div>
                            @error('password') <p class="mt-1 text-xs text-[var(--color-bahaya)]">{{ $message }}</p> @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div>
                            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium">Konfirmasi Password</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[var(--color-text-muted)]">
                                    <x-heroicon-o-lock-closed class="h-4 w-4" />
                                </span>
                                <input id="password_confirmation" :type="show2 ? 'text' : 'password'" name="password_confirmation" required
                                    autocomplete="new-password" placeholder="Ulangi password"
                                    class="h-10 w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] pl-9 pr-10 text-sm outline-none transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:border-[var(--color-accent)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]">
                                <button type="button" @click="show2 = !show2" tabindex="-1"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-text-muted)] transition-colors hover:text-[var(--color-text)]"
                                    :aria-label="show2 ? 'Sembunyikan password' : 'Tampilkan password'">
                                    <span x-show="!show2"><x-heroicon-o-eye class="h-4 w-4" /></span>
                                    <span x-show="show2" x-cloak><x-heroicon-o-eye-slash class="h-4 w-4" /></span>
                                </button>
                            </div>
                        </div>

                        <x-button type="submit" variant="primary" class="w-full">
                            Daftar
                            <x-heroicon-o-arrow-right class="h-4 w-4" />
                        </x-button>
                    </form>
                </div>

                <p class="mt-6 text-center text-sm text-[var(--color-text-muted)]">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-medium text-[var(--color-accent)] hover:underline">Masuk di sini</a>
                </p>

                <p class="mt-2 text-center text-xs text-[var(--color-text-muted)]">
                    &copy; {{ date('Y') }} EWS Banjir Rob Ketapang
                </p>
            </div>
        </main>
    </div>

</body>

</html>
