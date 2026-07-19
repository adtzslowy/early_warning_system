<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EWS Banjir Rob - Early Warning System Ketapang</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-50">
    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-950/80 backdrop-blur">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="flex items-center gap-2">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600 text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-sm">EWS Banjir Rob</p>
                    <p class="text-xs text-slate-500">Ketapang Coastal</p>
                </div>
            </a>

            <!-- Nav Links -->
            <nav class="flex items-center gap-1">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-4 py-2 text-sm font-medium bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Register
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="relative py-20 lg:py-32 px-6 lg:px-8 overflow-hidden">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left: Content -->
                <div>
                    <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                        Early Warning System <span class="text-blue-600">Banjir Rob</span>
                    </h1>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                        Sistem peringatan dini untuk memprediksi risiko banjir rob di wilayah Ketapang dengan akurasi tinggi menggunakan logika fuzzy Mamdani dan data real-time dari IoT sensors.
                    </p>
                    <div class="flex gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                                Mulai Sekarang
                            </a>
                            <a href="#about"
                                class="px-8 py-3 border-2 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white font-medium rounded-lg hover:bg-slate-100 dark:hover:bg-slate-900 transition">
                                Pelajari Lebih Lanjut
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right: Illustration/Visual -->
                <div class="relative h-64 lg:h-96 flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-100 to-blue-50 dark:from-blue-900/20 dark:to-blue-950/20 rounded-3xl"></div>
                    <svg class="w-full h-full relative z-10 max-w-md mx-auto" fill="none" viewBox="0 0 400 300">
                        <!-- Water waves -->
                        <path d="M0 150 Q 100 100 200 150 T 400 150 L 400 300 L 0 300 Z" fill="#bfdbfe" opacity="0.5"/>
                        <path d="M0 180 Q 100 150 200 180 T 400 180 L 400 300 L 0 300 Z" fill="#93c5fd" opacity="0.7"/>
                        <!-- Monitor/Alert icon -->
                        <rect x="100" y="50" width="200" height="120" rx="8" fill="#3b82f6" opacity="0.1"/>
                        <circle cx="200" cy="80" r="30" fill="#ef4444"/>
                        <text x="200" y="130" text-anchor="middle" font-size="14" fill="#1e40af" font-weight="bold">
                            Alert System
                        </text>
                        <line x1="40" y1="240" x2="360" y2="240" stroke="#93c5fd" stroke-width="2"/>
                    </svg>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 lg:py-32 px-6 lg:px-8 bg-slate-50 dark:bg-slate-900/50">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl lg:text-5xl font-bold mb-4">Tentang Sistem Kami</h2>
                    <p class="text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        Platform terintegrasi untuk monitoring dan prediksi risiko banjir rob dengan teknologi terkini.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-sm hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Real-time Monitoring</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Monitor kondisi cuaca maritim dan ketinggian air secara real-time dari berbagai stasiun di Ketapang.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-sm hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Prediksi Akurat</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Teknologi Fuzzy Logic Mamdani memberikan prediksi risiko banjir rob dengan tingkat akurasi tinggi.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-sm hover:shadow-lg transition">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Alert Notifications</h3>
                        <p class="text-slate-600 dark:text-slate-400">
                            Terima notifikasi real-time melalui Telegram saat ada perubahan level risiko banjir rob.
                        </p>
                    </div>
                </div>
            </div>
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
