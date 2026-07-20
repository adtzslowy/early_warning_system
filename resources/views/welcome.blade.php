<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EWS Banjir Rob - Prediksi Banjir Rob Real-time</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800&family=inter:400,500,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #f1f5f9; }
        .font-display { font-family: 'Instrument Sans', sans-serif; }
        @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-20px); } }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .gradient-text { background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .gradient-btn { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; }
        .gradient-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.2); }
        .card-glass { background: rgba(30, 41, 59, 0.5); backdrop-filter: blur(10px); border: 1px solid rgba(148, 163, 184, 0.1); }
        .feature-card { transition: all 0.3s ease; }
        .feature-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="/" class="text-2xl font-bold font-display gradient-text">EWS</a>

            <nav class="hidden lg:flex items-center gap-8">
                <a href="#fitur" class="text-sm text-slate-300 hover:text-cyan-400 transition">Fitur</a>
                <a href="#bagaimana" class="text-sm text-slate-300 hover:text-cyan-400 transition">Bagaimana</a>
                <a href="#harga" class="text-sm text-slate-300 hover:text-cyan-400 transition">Harga</a>
                <a href="#faq" class="text-sm text-slate-300 hover:text-cyan-400 transition">FAQ</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-6 py-2 gradient-btn rounded-lg font-semibold text-sm transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm text-slate-300 hover:text-white transition">Login</a>
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

        <div class="max-w-5xl mx-auto text-center space-y-8">
            <div class="inline-block px-4 py-2 rounded-full border border-cyan-500/30 bg-cyan-500/10 text-cyan-400 text-sm font-semibold">
                ⚡ Prediksi Banjir Rob Real-time
            </div>

            <h1 class="text-5xl lg:text-7xl font-black font-display leading-tight">
                Lindungi Wilayah Ketapang dari <span class="gradient-text">Banjir Rob</span>
            </h1>

            <p class="text-lg text-slate-300 max-w-3xl mx-auto leading-relaxed">
                Sistem peringatan dini berbasis teknologi OLS regression dan Fuzzy Logic untuk memprediksi risiko banjir rob dengan akurasi tinggi. Dapatkan alert real-time dan ambil keputusan lebih cepat.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
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
            <div class="grid md:grid-cols-3 gap-8 pt-12 border-t border-slate-800">
                <div class="text-center">
                    <p class="text-3xl font-bold gradient-text">24/7</p>
                    <p class="text-slate-400 text-sm mt-2">Monitoring Real-time</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold gradient-text">4 Jam</p>
                    <p class="text-slate-400 text-sm mt-2">Prediksi Akurat</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold gradient-text">< 5 Detik</p>
                    <p class="text-slate-400 text-sm mt-2">Notifikasi Alert</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-20 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold font-display mb-4">Fitur Unggulan</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">Semua yang Anda butuhkan untuk monitoring dan prediksi banjir rob</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card card-glass p-8 rounded-xl space-y-4">
                    <div class="text-4xl">📊</div>
                    <h3 class="text-xl font-bold">Monitoring Real-time</h3>
                    <p class="text-slate-400">Pantau ketinggian air dari multiple stasiun IoT dengan update setiap menit.</p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card card-glass p-8 rounded-xl space-y-4">
                    <div class="text-4xl">🔮</div>
                    <h3 class="text-xl font-bold">Prediksi Akurat</h3>
                    <p class="text-slate-400">OLS Regression untuk 4 horizon waktu berbeda (30, 60, 120, 240 menit).</p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card card-glass p-8 rounded-xl space-y-4">
                    <div class="text-4xl">🔔</div>
                    <h3 class="text-xl font-bold">Alert Instant</h3>
                    <p class="text-slate-400">Notifikasi real-time via Telegram saat ada perubahan level risiko.</p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card card-glass p-8 rounded-xl space-y-4">
                    <div class="text-4xl">🎯</div>
                    <h3 class="text-xl font-bold">Fuzzy Logic</h3>
                    <p class="text-slate-400">Klasifikasi risiko (Aman/Waspada/Siaga/Bahaya) berbasis Mamdani inference.</p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card card-glass p-8 rounded-xl space-y-4">
                    <div class="text-4xl">📱</div>
                    <h3 class="text-xl font-bold">Dashboard Intuitif</h3>
                    <p class="text-slate-400">Interface modern dengan visualisasi data yang jelas dan mudah dipahami.</p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card card-glass p-8 rounded-xl space-y-4">
                    <div class="text-4xl">🔗</div>
                    <h3 class="text-xl font-bold">REST API</h3>
                    <p class="text-slate-400">Integrasi mudah dengan sistem existing Anda melalui API yang powerful.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="bagaimana" class="py-20 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold font-display mb-4">Bagaimana Cara Kerjanya</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">3 langkah sederhana untuk mulai melindungi wilayah Anda</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-green-500 rounded-full flex items-center justify-center mx-auto">
                        <span class="text-2xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold">Daftar & Setup</h3>
                    <p class="text-slate-400">Buat akun dan tambahkan device IoT Anda ke dashboard dalam hitungan menit.</p>
                </div>

                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-green-500 rounded-full flex items-center justify-center mx-auto">
                        <span class="text-2xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold">Monitor & Prediksi</h3>
                    <p class="text-slate-400">Sistem mulai menganalisis data dan memberikan prediksi real-time 24/7.</p>
                </div>

                <div class="text-center space-y-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-green-500 rounded-full flex items-center justify-center mx-auto">
                        <span class="text-2xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold">Terima Alert</h3>
                    <p class="text-slate-400">Dapatkan notifikasi instant saat ada perubahan level risiko via Telegram.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="harga" class="py-20 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold font-display mb-4">Harga Transparan</h2>
                <p class="text-lg text-slate-400 max-w-2xl mx-auto">Mulai gratis, upgrade saat Anda siap</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Basic -->
                <div class="card-glass p-8 rounded-xl space-y-6 border border-slate-700">
                    <div>
                        <h3 class="text-2xl font-bold">Starter</h3>
                        <p class="text-4xl font-black gradient-text mt-2">Gratis</p>
                        <p class="text-slate-400 text-sm mt-2">Selamanya gratis untuk tim kecil</p>
                    </div>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> 1 device</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Monitoring 24/7</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Alert via Telegram</li>
                        <li class="flex items-center gap-2"><span class="text-slate-600">✗</span> API Access</li>
                        <li class="flex items-center gap-2"><span class="text-slate-600">✗</span> Priority Support</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 border border-slate-600 rounded-lg font-semibold text-center hover:border-slate-400 transition">Mulai Gratis</a>
                </div>

                <!-- Pro -->
                <div class="card-glass p-8 rounded-xl space-y-6 border border-green-500/50 bg-green-500/5 relative">
                    <div class="absolute top-0 right-0 px-3 py-1 bg-gradient-to-r from-cyan-500 to-green-500 text-white text-xs font-bold rounded-bl-lg">POPULAR</div>
                    <div>
                        <h3 class="text-2xl font-bold">Professional</h3>
                        <p class="text-4xl font-black gradient-text mt-2">Rp 500K<span class="text-sm font-normal text-slate-400">/bulan</span></p>
                        <p class="text-slate-400 text-sm mt-2">Untuk tim yang berkembang</p>
                    </div>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Hingga 10 devices</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Monitoring 24/7</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Alert via Telegram</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> REST API Access</li>
                        <li class="flex items-center gap-2"><span class="text-slate-600">✗</span> Priority Support</li>
                    </ul>
                    <a href="{{ route('register') }}" class="block w-full py-3 gradient-btn rounded-lg font-semibold text-center transition">Mulai Trial</a>
                </div>

                <!-- Enterprise -->
                <div class="card-glass p-8 rounded-xl space-y-6 border border-slate-700">
                    <div>
                        <h3 class="text-2xl font-bold">Enterprise</h3>
                        <p class="text-4xl font-black gradient-text mt-2">Custom</p>
                        <p class="text-slate-400 text-sm mt-2">Untuk organisasi besar</p>
                    </div>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Unlimited devices</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Monitoring 24/7</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Custom alerts</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> REST API Access</li>
                        <li class="flex items-center gap-2"><span class="text-green-400">✓</span> Priority Support</li>
                    </ul>
                    <a href="#" class="block w-full py-3 border border-slate-600 rounded-lg font-semibold text-center hover:border-slate-400 transition">Hubungi Sales</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl lg:text-5xl font-bold font-display mb-4">Pertanyaan Umum</h2>
                <p class="text-lg text-slate-400">Jawaban atas pertanyaan yang sering diajukan</p>
            </div>

            <div class="space-y-4">
                <details class="card-glass p-6 rounded-lg cursor-pointer group">
                    <summary class="flex items-center justify-between font-bold text-lg">
                        <span>Apa perbedaan EWS dengan sistem lain?</span>
                        <span class="text-2xl group-open:rotate-180 transition">›</span>
                    </summary>
                    <p class="text-slate-400 mt-4">EWS menggunakan kombinasi OLS Regression dan Fuzzy Logic Mamdani untuk memberikan prediksi yang akurat dengan alert real-time via Telegram. Kami fokus pada kemudahan penggunaan dan integrasi.</p>
                </details>

                <details class="card-glass p-6 rounded-lg cursor-pointer group">
                    <summary class="flex items-center justify-between font-bold text-lg">
                        <span>Berapa lama data disimpan?</span>
                        <span class="text-2xl group-open:rotate-180 transition">›</span>
                    </summary>
                    <p class="text-slate-400 mt-4">Semua data sensor dan prediksi disimpan selamanya untuk analisis historis. Anda dapat mengakses dan mengunduh data kapan saja.</p>
                </details>

                <details class="card-glass p-6 rounded-lg cursor-pointer group">
                    <summary class="flex items-center justify-between font-bold text-lg">
                        <span>Apakah ada biaya setup atau hidden fee?</span>
                        <span class="text-2xl group-open:rotate-180 transition">›</span>
                    </summary>
                    <p class="text-slate-400 mt-4">Tidak ada hidden fee. Harga yang kami tawarkan sudah termasuk semua fitur yang Anda butuhkan. Setup gratis dan bisa dilakukan sendiri.</p>
                </details>

                <details class="card-glass p-6 rounded-lg cursor-pointer group">
                    <summary class="flex items-center justify-between font-bold text-lg">
                        <span>Bagaimana jika device offline?</span>
                        <span class="text-2xl group-open:rotate-180 transition">›</span>
                    </summary>
                    <p class="text-slate-400 mt-4">Sistem akan otomatis mendeteksi device offline dan memberikan notifikasi. Data prediksi menggunakan data historis terakhir yang ada.</p>
                </details>

                <details class="card-glass p-6 rounded-lg cursor-pointer group">
                    <summary class="flex items-center justify-between font-bold text-lg">
                        <span>Apakah API tersedia untuk integrasi?</span>
                        <span class="text-2xl group-open:rotate-180 transition">›</span>
                    </summary>
                    <p class="text-slate-400 mt-4">Ya, REST API tersedia untuk paket Professional dan Enterprise. Dokumentasi lengkap tersedia di dashboard Anda.</p>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 lg:py-32 px-6 lg:px-8 border-t border-slate-800">
        <div class="max-w-4xl mx-auto text-center space-y-8">
            <h2 class="text-4xl lg:text-5xl font-bold font-display">Lindungi Wilayah Anda Sekarang</h2>
            <p class="text-lg text-slate-300">Bergabung dengan sistem early warning terdepan untuk monitoring banjir rob real-time</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 gradient-btn rounded-lg font-bold transition">Buka Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 gradient-btn rounded-lg font-bold transition">Mulai Gratis</a>
                    <a href="{{ route('login') }}" class="px-8 py-4 border border-slate-600 rounded-lg font-bold hover:border-slate-400 transition">Login</a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-12 px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div>
                    <p class="font-bold font-display gradient-text text-lg mb-4">EWS</p>
                    <p class="text-sm text-slate-400">Sistem peringatan dini untuk prediksi banjir rob real-time.</p>
                </div>
                <div>
                    <p class="font-bold mb-4">Product</p>
                    <ul class="text-sm text-slate-400 space-y-2">
                        <li><a href="#fitur" class="hover:text-white transition">Fitur</a></li>
                        <li><a href="#harga" class="hover:text-white transition">Harga</a></li>
                        <li><a href="#" class="hover:text-white transition">Dokumentasi</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-bold mb-4">Company</p>
                    <ul class="text-sm text-slate-400 space-y-2">
                        <li><a href="#" class="hover:text-white transition">Tentang</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <p class="font-bold mb-4">Legal</p>
                    <ul class="text-sm text-slate-400 space-y-2">
                        <li><a href="#" class="hover:text-white transition">Privacy</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms</a></li>
                        <li><a href="#" class="hover:text-white transition">Status</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-8 text-center text-sm text-slate-400">
                <p>&copy; 2026 EWS Banjir Rob. Built for Ketapang Coastal Protection.</p>
            </div>
        </div>
    </footer>
</body>
</html>
