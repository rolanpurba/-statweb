<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StatWeb — Analisis Statistik Berbasis Web</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sw-bg font-sans antialiased">

{{-- Navbar --}}
<nav class="border-b border-slate-200 bg-white sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-mono font-bold text-sm bg-sw-navy text-sw-gold border border-sw-navy">
                Σ
            </div>
            <span class="font-display font-semibold text-lg tracking-tight text-sw-navy">StatWeb</span>
        </div>
        <a href="{{ route('dashboard') }}"
           class="bg-sw-navy hover:bg-sw-navy-light text-white px-4 py-1.5 rounded-lg text-sm font-medium transition shadow-sm">
            Mulai Sekarang
        </a>
    </div>
</nav>

{{-- Hero --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
    <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-medium mb-6 font-mono">
            <span class="w-1.5 h-1.5 rounded-full bg-sw-gold"></span>
            Tanpa login — langsung pakai
        </div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6 text-sw-navy">
            Analisis Statistik<br>
            <span class="text-sw-gold">Langsung di Browser</span>
        </h1>
        <p class="text-lg text-slate-500 mb-8 leading-relaxed max-w-2xl">
            StatWeb adalah alat analisis statistik berbasis web yang terinspirasi dari SPSS.
            Upload dataset CSV atau Excel, jalankan berbagai uji statistik, dan export hasilnya ke PDF —
            tanpa install software apapun.
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('dashboard') }}"
               class="bg-sw-navy hover:bg-sw-navy-light text-white px-6 py-3 rounded-xl text-sm font-semibold transition shadow-sm">
                Coba Sekarang
            </a>
            <a href="{{ route('datasets.index') }}"
               class="bg-white text-slate-700 px-6 py-3 rounded-xl text-sm font-semibold hover:bg-slate-50 transition border border-slate-200">
                Upload Dataset
            </a>
        </div>
    </div>
</section>

{{-- Fitur Utama --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <h2 class="font-display text-2xl font-bold mb-2 text-sw-navy">Semua yang kamu butuhkan</h2>
    <p class="text-slate-500 text-sm mb-8">8 jenis analisis statistik, visualisasi grafik, dan export PDF dalam satu platform.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        @foreach ([
            ['Statistik Deskriptif', 'Mean, median, modus, std dev, kuartil, dan lebih banyak lagi'],
            ['Uji Hipotesis', 'Uji-t (3 jenis), ANOVA, uji normalitas Shapiro-Wilk & KS'],
            ['Korelasi & Regresi', 'Pearson, Spearman, regresi linear sederhana & berganda'],
            ['Chi-square Test', 'Uji hubungan antar variabel kategorik + Cramér\'s V'],
        ] as [$title, $desc])
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition border-t-[3px] border-t-sw-gold">
                <h3 class="font-display text-sm font-semibold mb-1 text-sw-navy">{{ $title }}</h3>
                <p class="text-xs text-slate-500 leading-relaxed">{{ $desc }}</p>
            </div>
        @endforeach
    </div>

    {{-- Cara Kerja --}}
    <div class="rounded-2xl p-8 sm:p-10 mb-12 bg-sw-navy">
        <h2 class="font-display text-xl font-bold text-white mb-8 text-center">Cara Kerja StatWeb</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            @foreach ([
                ['01', 'Upload Dataset', 'Upload file CSV atau Excel kamu. StatWeb otomatis mendeteksi tipe data tiap kolom.'],
                ['02', 'Pilih Analisis', 'Pilih jenis analisis yang kamu butuhkan. Tersedia 8 jenis analisis statistik lengkap.'],
                ['03', 'Export Hasil', 'Hasil analisis lengkap dengan grafik bisa langsung diexport ke PDF untuk laporan.'],
            ] as [$num, $title, $desc])
                <div class="text-center">
                    <div class="font-mono text-4xl font-bold mb-3 text-sw-gold/40">{{ $num }}</div>
                    <h3 class="font-display text-sm font-semibold text-white mb-2">{{ $title }}</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Dua Kolom Fitur --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="font-display text-sm font-semibold mb-4 pb-3 border-b border-slate-100 text-sw-navy">
                Fitur Analisis
            </h3>
            <div class="space-y-2.5">
                @foreach ([
                    'Statistik Deskriptif (mean, median, modus, std dev, kuartil)',
                    'Distribusi Frekuensi untuk data kategorik',
                    'Uji Normalitas (Shapiro-Wilk & Kolmogorov-Smirnov)',
                    'Uji-t Satu Sampel, Independen, Berpasangan',
                    'One-Way ANOVA',
                    'Korelasi Pearson & Spearman',
                    'Chi-square Test + Cramér\'s V',
                    'Regresi Linear Sederhana & Berganda (OLS)',
                ] as $fitur)
                    <div class="flex items-start gap-2.5 text-sm text-slate-600">
                        <span class="mt-0.5 flex-shrink-0 font-bold text-sw-gold">✓</span>
                        {{ $fitur }}
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="font-display text-sm font-semibold mb-4 pb-3 border-b border-slate-100 text-sw-navy">
                Fitur Platform
            </h3>
            <div class="space-y-2.5">
                @foreach ([
                    'Tidak perlu login — langsung pakai tanpa daftar',
                    'Upload CSV dan Excel (XLSX, XLS) hingga 50MB',
                    'Data View editable — edit data langsung di browser',
                    'Variable View — atur tipe, label, measure per kolom',
                    'Visualisasi grafik (histogram, bar chart, scatter plot)',
                    'Riwayat analisis tersimpan otomatis (Output Viewer)',
                    'Export hasil analisis ke PDF lengkap dengan grafik',
                    'Export dataset ke CSV',
                ] as $fitur)
                    <div class="flex items-start gap-2.5 text-sm text-slate-600">
                        <span class="mt-0.5 flex-shrink-0 font-bold text-sw-gold">✓</span>
                        {{ $fitur }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="rounded-2xl p-10 text-center bg-sw-navy">
        <div class="font-mono text-sm mb-3 text-sw-gold">— StatWeb —</div>
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mb-3">
            Mulai analisis data kamu sekarang
        </h2>
        <p class="text-slate-400 text-sm mb-6">Gratis, tanpa instalasi, tanpa login. Langsung dari browser.</p>
        <a href="{{ route('dashboard') }}"
           class="inline-block bg-sw-gold hover:bg-sw-gold-hover text-sw-navy px-8 py-3 rounded-xl text-sm font-semibold transition shadow-sm">
            Buka StatWeb
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="border-t border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded flex items-center justify-center font-mono font-bold text-xs bg-sw-navy text-sw-gold">Σ</div>
            <span class="font-display text-sm font-semibold text-sw-navy">StatWeb</span>
        </div>
        <p class="text-xs text-slate-400 font-mono">Aplikasi web analisis statistik — Proyek Akhir</p>
    </div>
</footer>

</body>
</html>
