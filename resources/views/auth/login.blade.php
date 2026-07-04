<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Masuk' }} · EWS Banjir Rob</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">

    {{-- Anti-flash: set tema SEBELUM body dirender --}}
    <script>
        if (localStorage.theme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen font-dash text-[var(--color-text)] antialiased">

    <div class="relative flex min-h-screen items-center justify-center overflow-hidden p-4 sm:p-6">

        {{-- Latar dekoratif (nuansa air), lembut di light & dark --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute -top-24 left-1/2 h-72 w-[36rem] max-w-[90vw] -translate-x-1/2 rounded-full bg-[var(--color-accent)]/10 blur-3xl"></div>
            <div class="absolute bottom-[-6rem] right-[-4rem] h-64 w-64 rounded-full bg-[color-mix(in_srgb,var(--color-aman)_18%,transparent)] blur-3xl"></div>
        </div>

        {{-- Toggle tema (pojok kanan atas) --}}
        <button
            type="button"
            x-data="{ dark: document.documentElement.classList.contains('dark') }"
            @click="dark = !dark; document.documentElement.classList.toggle('dark', dark); localStorage.theme = dark ? 'dark' : 'light'"
            class="absolute right-4 top-4 inline-flex h-9 w-9 items-center justify-center rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text-muted)] transition-colors hover:bg-[var(--color-surface-2)] hover:text-[var(--color-text)] sm:right-6 sm:top-6"
            aria-label="Ganti tema terang/gelap"
        >
            <span x-show="!dark" x-cloak><x-heroicon-o-moon class="h-5 w-5" /></span>
            <span x-show="dark" x-cloak><x-heroicon-o-sun class="h-5 w-5" /></span>
        </button>

        <div class="w-full max-w-md">
            {{-- Brand --}}
            <div class="mb-6 flex flex-col items-center text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-[var(--color-accent)] text-[var(--color-accent-foreground)]">
                    <x-heroicon-o-signal class="h-6 w-6" />
                </div>
                <h1 class="font-display text-xl font-bold tracking-tight">EWS Banjir Rob</h1>
                <p class="mt-1 text-sm text-[var(--color-text-muted)]">Masuk untuk mengakses dashboard monitoring</p>
            </div>

            {{-- Kartu form --}}
            <div class="card p-6 sm:p-8">
                {{-- Error umum (mis. kredensial salah) --}}
                @if ($errors->has('email'))
                    <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-[color-mix(in_srgb,var(--color-bahaya)_45%,transparent)] bg-[color-mix(in_srgb,var(--color-bahaya)_10%,transparent)] px-3.5 py-2.5 text-sm text-[var(--color-bahaya)]">
                        <x-heroicon-o-exclamation-circle class="mt-0.5 h-4 w-4 shrink-0" />
                        <span>{{ $errors->first('email') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('proses') }}" class="space-y-4" x-data="{ show: false }">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium">Email</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[var(--color-text-muted)]">
                                <x-heroicon-o-envelope class="h-4 w-4" />
                            </span>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                inputmode="email"
                                placeholder="nama@email.com"
                                class="h-10 w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] pl-9 pr-3 text-sm outline-none transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:border-[var(--color-accent)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-[var(--color-text-muted)]">
                                <x-heroicon-o-lock-closed class="h-4 w-4" />
                            </span>
                            <input
                                id="password"
                                :type="show ? 'text' : 'password'"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="h-10 w-full rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] pl-9 pr-10 text-sm outline-none transition-colors placeholder:text-[var(--color-text-muted)] focus-visible:border-[var(--color-accent)] focus-visible:ring-2 focus-visible:ring-[var(--color-ring)]"
                            >
                            <button
                                type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-[var(--color-text-muted)] transition-colors hover:text-[var(--color-text)]"
                                :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                                tabindex="-1"
                            >
                                <span x-show="!show"><x-heroicon-o-eye class="h-4 w-4" /></span>
                                <span x-show="show" x-cloak><x-heroicon-o-eye-slash class="h-4 w-4" /></span>
                            </button>
                        </div>
                    </div>

                    {{-- Ingat saya --}}
                    <div class="flex items-center">
                        <label class="flex cursor-pointer items-center gap-2 text-sm text-[var(--color-text-muted)]">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                @checked(old('remember'))
                                class="h-4 w-4 rounded border-[var(--color-border)] accent-[var(--color-accent)]"
                            >
                            Ingat saya
                        </label>
                    </div>

                    <x-button type="submit" variant="primary" class="w-full">
                        Masuk
                        <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4" />
                    </x-button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-[var(--color-text-muted)]">
                &copy; {{ date('Y') }} EWS Banjir Rob Ketapang · Akun dibuat oleh admin
            </p>
        </div>
    </div>

</body>

</html>
