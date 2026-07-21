<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .gradient-btn {
            @apply bg-gradient-to-r from-cyan-500 to-emerald-500 text-white hover:shadow-lg transition-all duration-200;
        }
        .card-glass {
            @apply bg-[var(--color-surface-2)] border border-[var(--color-border)] rounded-lg;
        }
    </style>
</head>
<body class="bg-[var(--color-bg)] text-[var(--color-text)] font-sans">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-[var(--color-bg)]/95 backdrop-blur border-b border-[var(--color-border)]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="/" class="text-2xl font-bold gradient-text">EWS</a>

            <nav class="hidden lg:flex items-center gap-8">
                <a href="{{ route('beranda') }}" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Beranda</a>
                <a href="{{ route('tentang') }}" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Tentang</a>
                <a href="#fitur" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Fitur</a>
                <a href="#prediksi" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Prediksi</a>
                <a href="#alert" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Alert</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-6 py-2 gradient-btn rounded-lg font-semibold text-sm transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-text)] transition">Login</a>
                    <a href="{{ route('register') }}" class="px-6 py-2 gradient-btn rounded-lg font-semibold text-sm transition">Daftar Gratis</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-20 lg:pt-32 pb-16 px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center space-y-8">

            <h1 class="text-5xl lg:text-6xl font-black font-display leading-tight">
                Lindungi Wilayah Anda dari <span class="gradient-text">Banjir Rob</span>
            </h1>

            <p class="text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto">
                Sistem peringatan dini berbasis teknologi OLS regression dan Fuzzy Logic untuk memprediksi risiko banjir rob. Dapatkan alert real-time dan ambil keputusan lebih cepat.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 gradient-btn rounded-lg font-bold text-center transition">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 gradient-btn rounded-lg font-bold text-center transition">
                        Mulai Gratis
                    </a>
                    <a href="#fitur" class="px-8 py-4 border border-slate-600 rounded-lg font-bold text-center hover:border-slate-400 transition">
                        Pelajari Lebih Lanjut
                    </a>
                @endauth
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-800 max-w-lg mx-auto">
                <div class="text-center">
                    <p class="text-2xl font-bold gradient-text">24/7</p>
                    <p class="text-slate-400 text-xs mt-2">Monitoring</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold gradient-text">4 Jam</p>
                    <p class="text-slate-400 text-xs mt-2">Prediksi</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold gradient-text">< 5s</p>
                    <p class="text-slate-400 text-xs mt-2">Alert</p>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
