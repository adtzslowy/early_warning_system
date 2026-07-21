<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tentang - EWS Banjir Rob</title>
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
                <a href="{{ route('tentang') }}" class="text-sm text-[var(--color-accent)] font-semibold">Tentang</a>
                <a href="/#fitur" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Fitur</a>
                <a href="/#prediksi" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Prediksi</a>
                <a href="/#alert" class="text-sm text-[var(--color-text-muted)] hover:text-[var(--color-accent)] transition">Alert</a>
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

    <!-- Page Header -->
    <section class="pt-20 lg:pt-28 pb-12 px-6 lg:px-8">
        <div class="max-w-4xl mx-auto text-center space-y-6">
            <h1 class="text-4xl lg:text-5xl font-black font-display leading-tight">
                Tentang <span class="gradient-text">EWS Banjir Rob</span>
            </h1>
            <p class="text-lg text-slate-300 leading-relaxed max-w-2xl mx-auto">
                Platform peringatan dini yang dibangun untuk membantu wilayah pesisir mengantisipasi banjir rob
                lebih awal, menggunakan data sensor real-time dan model prediksi berbasis data.
            </p>
        </div>
    </section>

    <!-- Latar Belakang -->
    <section class="py-12 px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-4">
            <h2 class="text-2xl lg:text-3xl font-bold font-display">Latar Belakang</h2>
            <p class="text-slate-300 leading-relaxed">
                Banjir rob adalah genangan air laut yang masuk ke daratan akibat pasang air laut, dan menjadi
                ancaman rutin bagi wilayah pesisir. Dampaknya mengganggu aktivitas warga, merusak properti, dan
                sulit diprediksi tanpa pemantauan yang berkelanjutan. EWS Banjir Rob dikembangkan untuk menjawab
                kebutuhan itu: memantau ketinggian air secara terus-menerus dan memberi peringatan sebelum risiko
                meningkat, bukan setelah air sudah naik.
            </p>
        </div>
    </section>

    <!-- Cara Kerja -->
    <section class="py-12 px-6 lg:px-8 bg-[var(--color-surface)]">
        <div class="max-w-5xl mx-auto space-y-8">
            <div class="text-center space-y-2">
                <h2 class="text-2xl lg:text-3xl font-bold font-display">Cara Kerja Sistem</h2>
                <p class="text-slate-400">Empat tahap dari data sensor hingga peringatan diterima pengguna</p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="card-glass p-6 space-y-3">
                    <p class="text-sm font-bold gradient-text">01</p>
                    <h3 class="font-bold">Pengambilan Data</h3>
                    <p class="text-sm text-slate-400">Sensor IoT di lapangan merekam ketinggian air, laju kenaikan, dan kecepatan angin onshore secara berkala.</p>
                </div>
                <div class="card-glass p-6 space-y-3">
                    <p class="text-sm font-bold gradient-text">02</p>
                    <h3 class="font-bold">Prediksi OLS Regression</h3>
                    <p class="text-sm text-slate-400">Data historis diolah dengan regresi linear (OLS) untuk memproyeksikan ketinggian air beberapa jam ke depan.</p>
                </div>
                <div class="card-glass p-6 space-y-3">
                    <p class="text-sm font-bold gradient-text">03</p>
                    <h3 class="font-bold">Penilaian Fuzzy Logic</h3>
                    <p class="text-sm text-slate-400">Hasil prediksi dinilai dengan Fuzzy Mamdani untuk menentukan skor dan kategori risiko (aman, waspada, bahaya).</p>
                </div>
                <div class="card-glass p-6 space-y-3">
                    <p class="text-sm font-bold gradient-text">04</p>
                    <h3 class="font-bold">Alert Real-time</h3>
                    <p class="text-sm text-slate-400">Ketika risiko meningkat, notifikasi dikirim ke pengguna agar keputusan bisa diambil lebih cepat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Teknologi -->
    <section class="py-12 px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-4">
            <h2 class="text-2xl lg:text-3xl font-bold font-display">Teknologi yang Digunakan</h2>
            <ul class="space-y-3 text-slate-300">
                <li class="flex gap-3">
                    <span class="gradient-text font-bold">•</span>
                    <span><span class="font-semibold text-[var(--color-text)]">OLS Regression</span> — memprediksi tren ketinggian air berdasarkan pola data historis.</span>
                </li>
                <li class="flex gap-3">
                    <span class="gradient-text font-bold">•</span>
                    <span><span class="font-semibold text-[var(--color-text)]">Fuzzy Logic Mamdani</span> — mengubah hasil prediksi menjadi skor risiko yang mudah dipahami.</span>
                </li>
                <li class="flex gap-3">
                    <span class="gradient-text font-bold">•</span>
                    <span><span class="font-semibold text-[var(--color-text)]">Sensor IoT</span> — memantau ketinggian air dan kondisi lingkungan secara real-time di lokasi rawan rob.</span>
                </li>
                <li class="flex gap-3">
                    <span class="gradient-text font-bold">•</span>
                    <span><span class="font-semibold text-[var(--color-text)]">Notifikasi Real-time</span> — menyampaikan peringatan ke pengguna dalam hitungan detik setelah risiko terdeteksi.</span>
                </li>
            </ul>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-6">
            <h2 class="text-2xl lg:text-3xl font-bold font-display">Mulai Pantau Risiko Rob di Wilayah Anda</h2>
            @auth
                <a href="{{ route('dashboard') }}" class="inline-block px-8 py-4 gradient-btn rounded-lg font-bold transition">
                    Buka Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-block px-8 py-4 gradient-btn rounded-lg font-bold transition">
                    Daftar Gratis
                </a>
            @endauth
        </div>
    </section>
</body>
</html>
