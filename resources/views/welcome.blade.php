<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EWS Banjir Rob - Early Warning System Ketapang</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Instrument Sans', sans-serif; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        @keyframes gradient { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        @keyframes pulse-glow { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .animate-gradient { background-size: 200% 200%; animation: gradient 8s ease infinite; }
        .animate-pulse-glow { animation: pulse-glow 2s ease-in-out infinite; }
        .glass-effect { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .dark .glass-effect { background: rgba(15, 23, 42, 0.4); border-color: rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-50">
    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-600 text-white shadow-lg group-hover:shadow-xl transition transform group-hover:scale-105">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-base font-display">EWS Banjir Rob</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Ketapang Coastal</p>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="flex items-center gap-2">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:scale-105">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-semibold rounded-lg hover:shadow-lg transition transform hover:scale-105">
                        Daftar Gratis
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="relative py-24 lg:py-40 px-6 lg:px-8 overflow-hidden">
            <!-- Animated Background -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-20 left-10 w-72 h-72 bg-blue-300 dark:bg-blue-900 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
                <div class="absolute top-40 right-10 w-72 h-72 bg-cyan-300 dark:bg-cyan-900 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            </div>

            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left: Content -->
                <div class="space-y-8">
                    <div class="inline-block">
                        <div class="px-4 py-2 rounded-full glass-effect text-sm font-semibold text-blue-600 dark:text-blue-400 animate-pulse-glow">
                            ⚡ Teknologi Terdepan untuk Mitigasi Banjir
                        </div>
                    </div>

                    <h1 class="text-5xl lg:text-7xl font-bold font-display leading-tight">
                        Prediksi <span class="bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">Banjir Rob</span> dengan Akurasi Tinggi
                    </h1>

                    <p class="text-lg lg:text-xl text-slate-600 dark:text-slate-300 leading-relaxed max-w-2xl">
                        Sistem peringatan dini berbasis AI untuk memprediksi risiko banjir rob di wilayah Ketapang. Terima notifikasi real-time dan ambil tindakan proaktif.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold rounded-xl hover:shadow-2xl transition transform hover:scale-105 text-center">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="px-8 py-4 bg-gradient-to-r from-blue-600 to-cyan-600 text-white font-bold rounded-xl hover:shadow-2xl transition transform hover:scale-105 text-center">
                                Mulai Sekarang
                            </a>
                            <a href="#features"
                                class="px-8 py-4 border-2 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-bold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-900 transition text-center">
                                Pelajari Lebih Lanjut
                            </a>
                        @endauth
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-4 pt-8">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">OLS</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Regression Model</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-cyan-600">Real-time</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Monitoring 24/7</p>
                        </div>
                        <div class="text-center">
                            <p class="text-3xl font-bold text-blue-600">4 Hours</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Forecast Horizon</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Visual -->
                <div class="relative h-96 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/20 to-cyan-400/20 dark:from-blue-900/30 dark:to-cyan-900/30 rounded-3xl blur-2xl"></div>
                    <div class="relative z-10">
                        <svg class="w-full max-w-md mx-auto animate-float" fill="none" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                            <!-- Water illustration -->
                            <circle cx="200" cy="200" r="150" fill="url(#gradient)" opacity="0.1"/>
                            <path d="M100 220 Q150 200 200 220 T300 220" stroke="#0ea5e9" stroke-width="3" fill="none"/>
                            <path d="M80 250 Q140 230 200 250 T320 250" stroke="#06b6d4" stroke-width="2" fill="none"/>
                            <!-- Alert icon -->
                            <circle cx="200" cy="140" r="40" fill="#3b82f6" opacity="0.2"/>
                            <text x="200" y="150" text-anchor="middle" font-size="50" fill="#0ea5e9">⚠️</text>
                            <defs>
                                <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#0ea5e9;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#06b6d4;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-24 lg:py-32 px-6 lg:px-8 bg-gradient-to-b from-slate-50 to-white dark:from-slate-900/50 dark:to-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <h2 class="text-4xl lg:text-5xl font-bold font-display mb-6">Fitur Unggulan</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
                        Teknologi terdepan untuk monitoring dan prediksi banjir rob yang lebih akurat dan cepat
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105 duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center mb-6 group-hover:shadow-lg transition">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold font-display mb-3">Monitoring Real-time</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Pantau ketinggian air dan kondisi cuaca maritim dari berbagai stasiun IoT dengan pembaruan setiap menit.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105 duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center mb-6 group-hover:shadow-lg transition">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold font-display mb-3">Prediksi Akurat</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Analisis berbasis regresi OLS dan logika fuzzy pada data historis untuk prediksi ketinggian air dalam 4 horizon waktu berbeda (30, 60, 120, 240 menit).
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group glass-effect rounded-2xl p-8 hover:shadow-2xl transition transform hover:scale-105 duration-300">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center mb-6 group-hover:shadow-lg transition">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold font-display mb-3">Notifikasi Instant</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                            Terima alert real-time melalui Telegram saat ada perubahan level risiko dengan detail prediksi lengkap.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Risk Levels Section -->
        <section class="py-24 lg:py-32 px-6 lg:px-8 bg-white dark:bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <h2 class="text-4xl lg:text-5xl font-bold font-display mb-6">Level Risiko Banjir Rob</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400">Sistem klasifikasi berbasis AI untuk prediksi yang lebih akurat</p>
                </div>

                <div class="grid md:grid-cols-4 gap-6">
                    <!-- Aman -->
                    <div class="rounded-xl p-6 border-2 border-green-500/50 bg-green-500/10">
                        <div class="text-4xl mb-3">✅</div>
                        <h3 class="text-lg font-bold mb-2 text-green-700 dark:text-green-400">Aman</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Kondisi aman, tidak ada risiko signifikan</p>
                    </div>

                    <!-- Waspada -->
                    <div class="rounded-xl p-6 border-2 border-yellow-500/50 bg-yellow-500/10">
                        <div class="text-4xl mb-3">⚠️</div>
                        <h3 class="text-lg font-bold mb-2 text-yellow-700 dark:text-yellow-400">Waspada</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Tanda-tanda potensi banjir muncul</p>
                    </div>

                    <!-- Siaga -->
                    <div class="rounded-xl p-6 border-2 border-orange-500/50 bg-orange-500/10">
                        <div class="text-4xl mb-3">🔴</div>
                        <h3 class="text-lg font-bold mb-2 text-orange-700 dark:text-orange-400">Siaga</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Risiko tinggi, persiapkan mitigasi</p>
                    </div>

                    <!-- Bahaya -->
                    <div class="rounded-xl p-6 border-2 border-red-500/50 bg-red-500/10">
                        <div class="text-4xl mb-3">🚨</div>
                        <h3 class="text-lg font-bold mb-2 text-red-700 dark:text-red-400">Bahaya</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Risiko sangat tinggi, lakukan evakuasi</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-24 lg:py-32 px-6 lg:px-8 bg-gradient-to-r from-blue-600 to-cyan-600 dark:from-blue-900 dark:to-cyan-900">
            <div class="max-w-4xl mx-auto text-center text-white space-y-8">
                <h2 class="text-4xl lg:text-5xl font-bold font-display">Siap Melindungi Wilayah Anda?</h2>
                <p class="text-lg opacity-90 max-w-2xl mx-auto">
                    Bergabunglah dengan sistem early warning terdepan untuk monitoring dan prediksi banjir rob
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:shadow-2xl transition transform hover:scale-105">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="px-8 py-4 bg-white text-blue-600 font-bold rounded-xl hover:shadow-2xl transition transform hover:scale-105">
                            Daftar Sekarang
                        </a>
                        <a href="{{ route('login') }}"
                            class="px-8 py-4 border-2 border-white text-white font-bold rounded-xl hover:bg-white hover:text-blue-600 transition">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 px-6 lg:px-8 bg-slate-900 dark:bg-black text-white">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <h4 class="font-bold mb-4">EWS Banjir Rob</h4>
                        <p class="text-sm text-slate-400">Sistem peringatan dini untuk memprediksi risiko banjir rob</p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Platform</h4>
                        <ul class="text-sm text-slate-400 space-y-2">
                            <li><a href="#" class="hover:text-white transition">Monitoring</a></li>
                            <li><a href="#" class="hover:text-white transition">Prediksi</a></li>
                            <li><a href="#" class="hover:text-white transition">Alert</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Resources</h4>
                        <ul class="text-sm text-slate-400 space-y-2">
                            <li><a href="#" class="hover:text-white transition">Dokumentasi</a></li>
                            <li><a href="#" class="hover:text-white transition">FAQ</a></li>
                            <li><a href="#" class="hover:text-white transition">Support</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Kontak</h4>
                        <ul class="text-sm text-slate-400 space-y-2">
                            <li><a href="#" class="hover:text-white transition">Ketapang, Indonesia</a></li>
                            <li><a href="#" class="hover:text-white transition">info@ews-banjir.id</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-700 pt-8 text-sm text-slate-400 text-center">
                    <p>&copy; 2026 EWS Banjir Rob. Semua hak dilindungi.</p>
                </div>
            </div>
        </footer>
    </main>
</body>
</html>
        </section>

        <!-- Risk Levels Section -->
        <section class="py-20 lg:py-32 px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl lg:text-5xl font-bold mb-4">Level Risiko Banjir Rob</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400">
                        Sistem kami mengklasifikasikan risiko ke dalam 4 level berdasarkan analisis data real-time.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Aman -->
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-green-200 dark:bg-green-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">✓</span>
                        </div>
                        <h3 class="text-xl font-bold text-green-900 dark:text-green-100 mb-2">Aman</h3>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            Kondisi aman, tidak ada risiko banjir rob yang signifikan.
                        </p>
                    </div>

                    <!-- Waspada -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-yellow-200 dark:bg-yellow-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">⚠</span>
                        </div>
                        <h3 class="text-xl font-bold text-yellow-900 dark:text-yellow-100 mb-2">Waspada</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            Kondisi menunjukkan tanda-tanda potensi banjir rob.
                        </p>
                    </div>

                    <!-- Siaga -->
                    <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-orange-200 dark:bg-orange-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">!</span>
                        </div>
                        <h3 class="text-xl font-bold text-orange-900 dark:text-orange-100 mb-2">Siaga</h3>
                        <p class="text-sm text-orange-700 dark:text-orange-300">
                            Risiko tinggi, persiapkan tindakan mitigasi.
                        </p>
                    </div>

                    <!-- Bahaya -->
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
                        <div class="w-16 h-16 bg-red-200 dark:bg-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">✕</span>
                        </div>
                        <h3 class="text-xl font-bold text-red-900 dark:text-red-100 mb-2">Bahaya</h3>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            Risiko sangat tinggi, lakukan evakuasi segera.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 lg:py-32 px-6 lg:px-8 bg-blue-600 text-white">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-4xl lg:text-5xl font-bold mb-6">Siap untuk Memulai?</h2>
                <p class="text-lg mb-8 opacity-90">
                    Bergabunglah dengan sistem early warning kami dan dapatkan akses ke monitoring real-time dan prediksi akurat.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-slate-100 transition inline-block">
                            Kembali ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                            class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-slate-100 transition inline-block">
                            Daftar Akun Baru
                        </a>
                        <a href="{{ route('login') }}"
                            class="px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-blue-700 transition inline-block">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800 py-12 px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <p class="font-semibold mb-2">EWS Banjir Rob</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Early Warning System untuk Ketapang Coastal
                    </p>
                </div>
                <div>
                    <p class="font-semibold mb-4 text-sm">Platform</p>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        @auth
                            <li><a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a></li>
                        @endauth
                        <li><a href="#about" class="hover:text-blue-600 transition">Tentang</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-semibold mb-4 text-sm">Akun</p>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        @auth
                            <li><a href="{{ route('settings.profile') }}" class="hover:text-blue-600 transition">Profil</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-blue-600 transition">Login</a></li>
                            <li><a href="{{ route('register') }}" class="hover:text-blue-600 transition">Register</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <p class="font-semibold mb-4 text-sm">Kontak</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Ketapang, West Kalimantan<br>
                        Indonesia
                    </p>
                </div>
            </div>
            <div class="border-t border-slate-200 dark:border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    &copy; 2026 EWS Banjir Rob. All rights reserved.
                </p>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Built with <span class="text-red-500">❤</span> for Ketapang Coastal
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
