<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StatWeb — Analisis Statistik Berbasis Web</title>
    <meta name="description" content="StatWeb adalah alat analisis statistik berbasis web terinspirasi SPSS. Upload dataset CSV atau Excel, jalankan 11 jenis uji statistik, dan ekspor laporan PDF langsung dari browser.">

    {{-- Open Graph / Social Sharing --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="StatWeb — Analisis Statistik Berbasis Web">
    <meta property="og:description" content="Alat analisis statistik modern langsung di browser. Gratis, tanpa instalasi, dan tanpa login.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="StatWeb">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="StatWeb — Analisis Statistik Berbasis Web">
    <meta name="twitter:description" content="Analisis statistik SPSS-like langsung di browser. Gratis tanpa login.">

    {{-- Favicon --}}
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='6' fill='%23162032'/><text x='16' y='22' font-family='monospace' font-size='18' font-weight='bold' fill='%23F6C90E' text-anchor='middle'>Σ</text></svg>" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sw-bg font-sans antialiased text-slate-800">

{{-- Navbar --}}
<nav class="border-b border-slate-200 bg-white sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
            <div class="size-8 rounded-lg flex items-center justify-center font-mono font-bold text-sm bg-sw-navy text-sw-gold border border-sw-navy transition group-hover:scale-105" aria-hidden="true">
                Σ
            </div>
            <span class="font-display font-semibold text-lg text-sw-navy">StatWeb</span>
        </a>
        <a href="{{ route('dashboard') }}"
           class="bg-sw-navy hover:bg-sw-navy-light text-white px-4 py-2 min-h-[38px] inline-flex items-center rounded-lg text-sm font-medium transition shadow-sm">
            Mulai Sekarang
        </a>
    </div>
</nav>

{{-- Hero --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
    <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-xs font-medium mb-6 font-mono">
            <span class="size-1.5 rounded-full bg-sw-gold" aria-hidden="true"></span>
            Tanpa login — langsung pakai
        </div>
        <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6 text-sw-navy text-balance">
            Analisis Statistik<br>
            <span class="text-sw-gold">Langsung di Browser</span>
        </h1>
        <p class="text-lg text-slate-600 mb-8 leading-relaxed max-w-2xl text-pretty">
            StatWeb adalah alat analisis statistik berbasis web yang terinspirasi dari SPSS.
            Upload dataset CSV atau Excel, jalankan berbagai uji statistik, dan ekspor hasilnya ke laporan PDF —
            tanpa instalasi software apapun.
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('dashboard') }}"
               class="bg-sw-navy hover:bg-sw-navy-light text-white px-6 py-3 min-h-[44px] inline-flex items-center justify-center rounded-xl text-sm font-semibold transition shadow-sm">
                Coba Sekarang
            </a>
            <a href="{{ route('datasets.index') }}"
               class="bg-white text-slate-700 px-6 py-3 min-h-[44px] inline-flex items-center justify-center rounded-xl text-sm font-semibold hover:bg-slate-50 transition border border-slate-200">
                Upload Dataset
            </a>
        </div>
    </div>
</section>

{{-- Fitur Utama --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="mb-8">
        <h2 class="font-display text-2xl font-bold mb-2 text-sw-navy text-balance">Semua yang kamu butuhkan</h2>
        <p class="text-slate-500 text-sm text-pretty">11+ metode analisis statistik lengkap, visualisasi grafik otomatis, dan export PDF dalam satu platform.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        @foreach ([
            ['Statistik Deskriptif', 'Mean, median, modus, std dev, variansi, kuartil, dan range data'],
            ['Uji Hipotesis Lengkap', 'Uji-t (Satu Sampel, Independen, Berpasangan), ANOVA, dan Uji Normalitas'],
            ['Korelasi & Regresi', 'Pearson, Spearman, Regresi Linear Sederhana dan Berganda (OLS)'],
            ['Uji Asosiasi Chi-Square', 'Uji independensi antar variabel kategorik dan koefisien Cramér\'s V'],
        ] as [$title, $desc])
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition border-t-[3px] border-t-sw-gold">
                <h3 class="font-display text-sm font-semibold mb-1.5 text-sw-navy">{{ $title }}</h3>
                <p class="text-xs text-slate-500 leading-relaxed text-pretty">{{ $desc }}</p>
            </div>
        @endforeach
    </div>

    {{-- Cara Kerja --}}
    <div class="rounded-2xl p-8 sm:p-10 mb-12 bg-sw-navy">
        <h2 class="font-display text-xl sm:text-2xl font-bold text-white mb-8 text-center text-balance">Cara Kerja StatWeb</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            @foreach ([
                ['01', 'Upload Dataset', 'Upload file CSV atau Excel kamu. StatWeb otomatis mendeteksi tipe data numerik dan kategorik tiap kolom.'],
                ['02', 'Pilih Analisis', 'Pilih jenis analisis yang kamu butuhkan. Tersedia 11 jenis analisis statistik yang terorganisir rapi.'],
                ['03', 'Ekspor Laporan', 'Hasil analisis lengkap dengan interpretasi hipotesis dan grafik bisa langsung diekspor ke PDF berkualitas tinggi.'],
            ] as [$num, $title, $desc])
                <div class="text-center">
                    <div class="font-mono text-4xl font-bold mb-3 text-sw-gold/70 tabular-nums" aria-hidden="true">{{ $num }}</div>
                    <h3 class="font-display text-sm font-semibold text-white mb-2 text-balance">{{ $title }}</h3>
                    <p class="text-xs text-slate-300 leading-relaxed text-pretty">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Dua Kolom Fitur --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-display text-sm font-semibold mb-4 pb-3 border-b border-slate-100 text-sw-navy">
                Fitur Analisis Statistik (11 Fitur)
            </h3>
            <div class="space-y-2.5">
                @foreach ([
                    'Statistik Deskriptif (mean, median, modus, std dev, variansi, kuartil)',
                    'Tabel Distribusi Frekuensi untuk data kategorik',
                    'Scatter Plot interaktif dengan sebaran titik',
                    'Uji Normalitas (Shapiro-Wilk untuk N<50 & Kolmogorov-Smirnov)',
                    'Uji-t Satu Sampel (One-Sample t-Test)',
                    'Uji-t Independen (Two-Sample t-Test)',
                    'Uji-t Berpasangan (Paired t-Test)',
                    'One-Way ANOVA (Analisis Variansi Satu Arah)',
                    'Korelasi Pearson (Parametrik) & Spearman (Non-parametrik)',
                    'Uji Independensi Chi-Square + Ukuran Asosiasi Cramér\'s V',
                    'Regresi Linear Sederhana & Berganda (OLS Coefficients, R², F-test)',
                ] as $fitur)
                    <div class="flex items-start gap-2.5 text-sm text-slate-600">
                        <span class="mt-0.5 flex-shrink-0 font-bold text-sw-gold" aria-hidden="true">✓</span>
                        <span>{{ $fitur }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <h3 class="font-display text-sm font-semibold mb-4 pb-3 border-b border-slate-100 text-sw-navy">
                Fitur Platform & Manajemen Data
            </h3>
            <div class="space-y-2.5">
                @foreach ([
                    'Tidak perlu login — langsung pakai tanpa registrasi akun',
                    'Upload file CSV dan Excel (XLSX, XLS) hingga 20MB',
                    'Data View editable — edit data sel langsung di tabel browser',
                    'Variable View — kustomisasi tipe, label, desimal, dan measure',
                    'Visualisasi grafik interaktif (Histogram, Bar Chart, Scatter Plot)',
                    'Riwayat analisis tersimpan otomatis per dataset (Output Viewer)',
                    'Ekspor laporan hasil analisis ke dokumen PDF lengkap dengan grafik',
                    'Ekspor dataset ke file CSV dengan proteksi formula injection',
                ] as $fitur)
                    <div class="flex items-start gap-2.5 text-sm text-slate-600">
                        <span class="mt-0.5 flex-shrink-0 font-bold text-sw-gold" aria-hidden="true">✓</span>
                        <span>{{ $fitur }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    <div class="rounded-2xl p-10 text-center bg-sw-navy shadow-sm">
        <div class="font-mono text-sm mb-3 text-sw-gold tracking-wide">— StatWeb —</div>
        <h2 class="font-display text-2xl sm:text-3xl font-bold text-white mb-3 text-balance">
            Mulai analisis data kamu sekarang
        </h2>
        <p class="text-slate-300 text-sm mb-6 text-pretty">Gratis, tanpa instalasi, tanpa login. Langsung dari browser Anda.</p>
        <a href="{{ route('dashboard') }}"
           class="min-h-[44px] inline-flex items-center justify-center bg-sw-gold hover:bg-sw-gold-hover text-sw-navy px-8 py-3 rounded-xl text-sm font-semibold transition shadow-sm">
            Buka StatWeb
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="border-t border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center gap-2">
            <div class="size-6 rounded flex items-center justify-center font-mono font-bold text-xs bg-sw-navy text-sw-gold" aria-hidden="true">Σ</div>
            <span class="font-display text-sm font-semibold text-sw-navy">StatWeb</span>
        </div>
        <p class="text-xs text-slate-400 font-mono">Aplikasi Web Analisis Statistik — Proyek Akhir</p>
    </div>
</footer>

</body>
</html>
