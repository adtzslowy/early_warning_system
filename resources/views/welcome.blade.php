<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EWS Banjir Rob - Prediksi Banjir Rob Real-time</title>
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
                <a href="#tentang" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Tentang</a>
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
    <section class="pt-20 lg:pt-32 pb-16 px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-cyan-500 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-green-500 rounded-full mix-blend-screen filter blur-3xl opacity-20"></div>
        </div>

        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left: Content -->
            <div class="space-y-8">
                <div class="inline-block px-4 py-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 text-cyan-400 text-sm font-semibold">
                    ⚡ Prediksi Banjir Rob Real-time
                </div>

                <h1 class="text-5xl lg:text-6xl font-black font-display leading-tight">
                    Lindungi Wilayah Ketapang dari <span class="gradient-text">Banjir Rob</span>
                </h1>

                <p class="text-lg text-slate-300 leading-relaxed">
                    Sistem peringatan dini berbasis teknologi OLS regression dan Fuzzy Logic untuk memprediksi risiko banjir rob dengan akurasi tinggi. Dapatkan alert real-time dan ambil keputusan lebih cepat.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
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
                <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-800">
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

            <!-- Right: Dashboard Preview -->
            <div class="relative">
                <div class="card-glass overflow-hidden">
                    <div class="bg-[var(--color-surface)] p-6 border-b border-[var(--color-border)]">
                        <p class="text-xs text-[var(--color-text-muted)]">Dashboard Preview</p>
                        <h3 class="text-xl font-bold mt-2 text-[var(--color-accent)]">Rob Ketapang - Muara</h3>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="p-6 space-y-4">
                        <!-- Metrics Row 1 -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="card p-4">
                                <p class="text-xs text-[var(--color-text-muted)] mb-2">Ketinggian Air</p>
                                <p class="text-2xl font-bold">45.7 <span class="text-sm text-[var(--color-text-muted)]">cm</span></p>
                                <div class="mt-3 h-1 bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full"></div>
                                <p class="text-xs text-[var(--color-waspada)] mt-2">⚠️ Waspada</p>
                            </div>

                            <div class="card p-4">
                                <p class="text-xs text-[var(--color-text-muted)] mb-2">Skor Risiko</p>
                                <p class="text-2xl font-bold text-[var(--color-aman)]">45</p>
                                <p class="text-xs text-[var(--color-text-muted)] mt-4">fuzzy Mamdani</p>
                            </div>
                        </div>

                        <!-- Metrics Row 2 -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="card p-4">
                                <p class="text-xs text-[var(--color-text-muted)] mb-2">Laju Kenaikan</p>
                                <p class="text-xl font-bold">-5.02 <span class="text-sm text-[var(--color-text-muted)]">cm/jam</span></p>
                                <p class="text-xs text-[var(--color-text-muted)] mt-2">positif = naik</p>
                            </div>

                            <div class="card p-4">
                                <p class="text-xs text-[var(--color-text-muted)] mb-2">Onshore Wind</p>
                                <p class="text-xl font-bold">2.43 <span class="text-sm text-[var(--color-text-muted)]">m/s</span></p>
                            </div>
                        </div>

                        <!-- Mini Chart -->
                        <div class="card p-4">
                            <p class="text-xs text-[var(--color-text-muted)] mb-3">Prediksi 2 Jam</p>
                            <div class="h-12 bg-gradient-to-r from-blue-500/20 via-cyan-500/20 to-green-500/20 rounded flex items-end justify-between px-2 gap-1">
                                <div class="w-1 bg-blue-500 h-6 rounded-sm"></div>
                                <div class="w-1 bg-blue-500 h-7 rounded-sm"></div>
                                <div class="w-1 bg-cyan-500 h-8 rounded-sm"></div>
                                <div class="w-1 bg-cyan-500 h-9 rounded-sm"></div>
                                <div class="w-1 bg-green-500 h-10 rounded-sm"></div>
                            </div>
                            <p class="text-xs text-[var(--color-text-muted)] mt-2">Linear Regression - 8 titik proyeksi</p>
                        </div>
                    </div>
                </div>

                <!-- Floating badge -->
                <div class="absolute -top-3 -right-3 px-4 py-2 gradient-btn rounded-lg text-white text-sm font-bold">
                    🚀 Live Data
                </div>
            </div>
        </div>
    </section>

</body>
</html>
