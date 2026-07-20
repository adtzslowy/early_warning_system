<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EWS Banjir Rob - Early Warning System</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f1f5f9; }
        .font-display { font-family: 'Instrument Sans', sans-serif; }
        .accent-cyan { color: #06b6d4; }
        .accent-green { color: #10b981; }
        .btn-accent { background: #10b981; color: white; }
        .btn-accent:hover { background: #059669; }
        .highlight { background: linear-gradient(to right, #06b6d4, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
</head>
<body class="bg-slate-950 text-slate-50">
    <!-- Header Navigation -->
    <header class="sticky top-0 z-50 bg-slate-950/95 backdrop-blur border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo -->
            <a href="/" class="text-xl font-bold font-display accent-cyan">EWS</a>

            <!-- Nav Links -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="#tentang" class="text-sm text-slate-300 hover:accent-cyan transition">Tentang</a>
                <a href="#prediksi" class="text-sm text-slate-300 hover:accent-cyan transition">Prediksi</a>
                <a href="#dokumentasi" class="text-sm text-slate-300 hover:accent-cyan transition">Dokumentasi</a>
                <a href="#harga" class="text-sm text-slate-300 hover:accent-cyan transition">Harga</a>
                <a href="#faq" class="text-sm text-slate-300 hover:accent-cyan transition">FAQ</a>
            </nav>

            <!-- Auth Links -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2 btn-accent rounded font-semibold text-sm transition">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="text-sm text-slate-300 hover:accent-cyan transition">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="px-6 py-2 btn-accent rounded font-semibold text-sm transition">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="min-h-screen flex items-center px-6 lg:px-8 py-20">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center w-full">
                <!-- Left: Content -->
                <div class="space-y-8">
                    <div class="inline-block px-3 py-1 rounded text-xs accent-green font-mono">
                        EWS BANJIR ROB
                    </div>

                    <h1 class="text-6xl lg:text-7xl font-black font-display leading-tight">
                        Pantau <span class="highlight">Banjir Rob</span> tanpa henti
                    </h1>

                    <p class="text-lg text-slate-300 leading-relaxed max-w-xl">
                        Sistem peringatan dini untuk memprediksi risiko banjir rob di wilayah Ketapang. Prediksi real-time dengan analisis data, notifikasi instant, dan dashboard monitoring terpadu.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-8 py-3 btn-accent rounded font-bold transition text-center">
                                Buka Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}"
                                class="px-8 py-3 btn-accent rounded font-bold transition text-center">
                                Daftar Sekarang
                            </a>
                            <a href="#tentang"
                                class="px-8 py-3 border border-slate-600 rounded font-bold hover:border-slate-400 transition text-center">
                                Lihat Selengkapnya
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right: Visual -->
                <div class="relative flex items-center justify-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/10 to-green-500/10 rounded-3xl blur-3xl"></div>
                    <div class="relative z-10 w-full h-96 flex items-center justify-center">
                        <svg class="w-full max-w-md" fill="none" viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                            <!-- Water waves -->
                            <g opacity="0.3">
                                <path d="M0 250 Q100 200 200 250 T400 250" stroke="#06b6d4" stroke-width="2" fill="none"/>
                                <path d="M0 280 Q100 230 200 280 T400 280" stroke="#10b981" stroke-width="2" fill="none"/>
                                <path d="M0 310 Q100 260 200 310 T400 310" stroke="#06b6d4" stroke-width="2" fill="none"/>
                            </g>
                            <!-- Alert icon -->
                            <circle cx="200" cy="120" r="50" fill="#10b981" opacity="0.2"/>
                            <text x="200" y="135" text-anchor="middle" font-size="60" fill="#10b981">⚠️</text>
                            <!-- Text label -->
                            <text x="200" y="380" text-anchor="middle" font-size="16" fill="#64748b">Prediksi Ketinggian Air</text>
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tentang Section -->
        <section id="tentang" class="py-24 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl lg:text-5xl font-black font-display mb-12">Tentang EWS Banjir Rob</h2>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="space-y-3">
                        <div class="text-3xl accent-green">📊</div>
                        <h3 class="text-xl font-bold">Monitoring Real-time</h3>
                        <p class="text-slate-400">Pantau ketinggian air dan kondisi cuaca dari berbagai stasiun IoT dengan update setiap menit.</p>
                    </div>

                    <div class="space-y-3">
                        <div class="text-3xl accent-green">🔮</div>
                        <h3 class="text-xl font-bold">Prediksi 4 Jam</h3>
                        <p class="text-slate-400">Prediksi ketinggian air dengan OLS regression untuk 4 horizon waktu berbeda (30, 60, 120, 240 menit).</p>
                    </div>

                    <div class="space-y-3">
                        <div class="text-3xl accent-green">🔔</div>
                        <h3 class="text-xl font-bold">Alert via Telegram</h3>
                        <p class="text-slate-400">Terima notifikasi real-time saat perubahan level risiko langsung ke Telegram Anda.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Prediksi Section -->
        <section id="prediksi" class="py-24 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl lg:text-5xl font-black font-display mb-12">Level Risiko & Prediksi</h2>

                <div class="grid md:grid-cols-4 gap-4 mb-16">
                    <div class="p-6 rounded border border-slate-700 bg-slate-800/50">
                        <div class="text-2xl mb-3">✅</div>
                        <h3 class="font-bold mb-2">Aman</h3>
                        <p class="text-sm text-slate-400">Kondisi aman, tidak ada risiko</p>
                    </div>

                    <div class="p-6 rounded border border-slate-700 bg-slate-800/50">
                        <div class="text-2xl mb-3">⚠️</div>
                        <h3 class="font-bold mb-2">Waspada</h3>
                        <p class="text-sm text-slate-400">Tanda-tanda potensi banjir</p>
                    </div>

                    <div class="p-6 rounded border border-slate-700 bg-slate-800/50">
                        <div class="text-2xl mb-3">🔴</div>
                        <h3 class="font-bold mb-2">Siaga</h3>
                        <p class="text-sm text-slate-400">Risiko tinggi, persiapkan mitigasi</p>
                    </div>

                    <div class="p-6 rounded border border-slate-700 bg-slate-800/50">
                        <div class="text-2xl mb-3">🚨</div>
                        <h3 class="font-bold mb-2">Bahaya</h3>
                        <p class="text-sm text-slate-400">Risiko sangat tinggi, evakuasi</p>
                    </div>
                </div>

                <div class="p-8 rounded border border-slate-700 bg-slate-800/50">
                    <h3 class="text-2xl font-bold mb-4">Metodologi Prediksi</h3>
                    <ul class="space-y-3 text-slate-300">
                        <li>• <strong>OLS Regression:</strong> Analisis data historis untuk prediksi ketinggian air</li>
                        <li>• <strong>Fuzzy Logic Mamdani:</strong> Klasifikasi tingkat risiko berdasarkan berbagai parameter</li>
                        <li>• <strong>4 Horizon Prediksi:</strong> 30, 60, 120, dan 240 menit ke depan</li>
                        <li>• <strong>Update Real-time:</strong> Setiap menit dengan data sensor IoT terbaru</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Dokumentasi Section -->
        <section id="dokumentasi" class="py-24 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
            <div class="max-w-7xl mx-auto">
                <h2 class="text-4xl lg:text-5xl font-black font-display mb-12">Dokumentasi & API</h2>

                <div class="grid md:grid-cols-2 gap-8">
                    <div class="p-8 rounded border border-slate-700 bg-slate-800/50">
                        <h3 class="text-2xl font-bold mb-4">Dokumentasi Lengkap</h3>
                        <p class="text-slate-300 mb-6">Pelajari cara menggunakan platform EWS Banjir Rob, integrasi API, dan best practices.</p>
                        <a href="#" class="text-cyan-400 font-semibold hover:accent-cyan transition">Baca Dokumentasi →</a>
                    </div>

                    <div class="p-8 rounded border border-slate-700 bg-slate-800/50">
                        <h3 class="text-2xl font-bold mb-4">REST API</h3>
                        <p class="text-slate-300 mb-6">Integrasikan sistem prediksi kami ke aplikasi Anda melalui REST API yang powerful.</p>
                        <a href="#" class="text-cyan-400 font-semibold hover:accent-cyan transition">Lihat API Reference →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-12 px-6 lg:px-8 border-t border-slate-800 text-slate-400 text-sm">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div>EWS Banjir Rob © 2026 | Sistem Peringatan Dini Banjir Rob Ketapang</div>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition">Status</a>
                    <a href="#" class="hover:text-white transition">Hubungi Kami</a>
                    <a href="#" class="hover:text-white transition">Privasi</a>
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
