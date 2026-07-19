<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP · EWS Banjir Rob</title>
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

        <aside class="relative hidden overflow-hidden lg:flex lg:flex-col lg:justify-between p-10 xl:p-14
                      bg-[color-mix(in_srgb,var(--color-accent)_92%,black)] text-[var(--color-accent-foreground)]">

            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-0">
                <div class="absolute -top-20 -left-10 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute top-1/3 right-0 h-56 w-56 rounded-full bg-black/20 blur-3xl"></div>
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
                    <h1 class="font-display text-2xl font-bold tracking-tight">Verifikasi Kode OTP</h1>
                    <p class="mt-1 text-sm text-[var(--color-text-muted)]">Kode telah dikirim ke {{ $email }}</p>
                </div>

                <div class="card p-6 sm:p-8">
                    {{-- Errors --}}
                    @if ($errors->any())
                        <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-[color-mix(in_srgb,var(--color-bahaya)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-bahaya)_10%,transparent)] px-3.5 py-2.5 text-sm text-[var(--color-bahaya)]">
                            <x-heroicon-o-exclamation-circle class="mt-0.5 h-5 w-5 shrink-0" />
                            <div>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Info box --}}
                    <div class="mb-6 flex items-start gap-2.5 rounded-lg border border-[color-mix(in_srgb,var(--color-accent)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-accent)_10%,transparent)] px-3.5 py-2.5 text-sm text-[var(--color-accent)]">
                        <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 shrink-0" />
                        <span>Kode berlaku selama 15 menit</span>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" x-data="{ showPassword: false }">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- OTP --}}
                        <div class="mb-4">
                            <label for="otp" class="block text-sm font-medium text-[var(--color-text-muted)] mb-1.5">Kode OTP (6 digit)</label>
                            <input
                                type="text"
                                id="otp"
                                name="otp"
                                placeholder="000000"
                                value="{{ old('otp') }}"
                                inputmode="numeric"
                                maxlength="6"
                                required
                                autofocus
                                class="w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2.5 text-sm tracking-widest text-center font-mono focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                            />
                            @error('otp')
                                <span class="mt-1 block text-xs text-[var(--color-bahaya)]">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Password Baru --}}
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-[var(--color-text-muted)] mb-1.5">Password Baru</label>
                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    name="password"
                                    placeholder="Minimal 8 karakter"
                                    required
                                    class="w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] hover:text-[var(--color-text)]"
                                >
                                    <x-heroicon-o-eye x-show="!showPassword" class="h-5 w-5" />
                                    <x-heroicon-o-eye-slash x-show="showPassword" class="h-5 w-5" />
                                </button>
                            </div>
                            @error('password')
                                <span class="mt-1 block text-xs text-[var(--color-bahaya)]">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-text-muted)] mb-1.5">Konfirmasi Password</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Ulangi password"
                                required
                                class="w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[var(--color-accent)]"
                            />
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-lg bg-[var(--color-accent)] px-4 py-2.5 text-sm font-medium text-[var(--color-accent-foreground)] transition-all hover:shadow-lg active:scale-95"
                        >
                            Reset Password
                        </button>
                    </form>

                    <div class="mt-4">
                        <a href="{{ route('password.request') }}" class="text-sm text-[var(--color-accent)] hover:underline">Minta kode baru</a>
                    </div>
                </div>
            </div>
        </main>

    </div>

</body>

</html>
