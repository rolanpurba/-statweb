<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StatWeb — Analisis Statistik Berbasis Web</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sw-display { font-family: 'Space Grotesk', sans-serif; }
        .sw-mono { font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="bg-[#F7F9FA] antialiased">


<nav class="border-b border-slate-200 bg-white sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-md flex items-center justify-center sw-mono font-bold text-sm" style="background: #0F2C59; color: #F6C90E;">
                Σ
            </div>
            <span class="sw-display font-semibold text-lg tracking-tight" style="color: #0F2C59;">StatWeb</span>
        </div>
        <a href="<?php echo e(route('dashboard')); ?>"
           class="text-white px-4 py-1.5 rounded-lg text-sm font-medium transition" style="background: #0F2C59;">
            Mulai Sekarang
        </a>
    </div>
</nav>


<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
    <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-3 py-1 rounded-full text-xs font-medium mb-6 sw-mono">
            <span class="w-1.5 h-1.5 rounded-full" style="background: #F6C90E;"></span>
            Tanpa login — langsung pakai
        </div>
        <h1 class="sw-display text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6" style="color: #0F2C59;">
            Analisis Statistik<br>
            <span style="color: #F6C90E;">Langsung di Browser</span>
        </h1>
        <p class="text-lg text-slate-500 mb-8 leading-relaxed max-w-2xl">
            StatWeb adalah alat analisis statistik berbasis web yang terinspirasi dari SPSS.
            Upload dataset CSV atau Excel, jalankan berbagai uji statistik, dan export hasilnya ke PDF —
            tanpa install software apapun.
        </p>
        <div class="flex flex-wrap gap-3">
            <a href="<?php echo e(route('dashboard')); ?>"
               class="text-white px-6 py-3 rounded-xl text-sm font-semibold transition" style="background: #0F2C59;">
                Coba Sekarang
            </a>
            <a href="<?php echo e(route('datasets.index')); ?>"
               class="bg-white text-slate-700 px-6 py-3 rounded-xl text-sm font-semibold hover:bg-slate-50 transition border border-slate-200">
                Upload Dataset
            </a>
        </div>
    </div>
</section>


<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <h2 class="sw-display text-2xl font-bold mb-2" style="color: #0F2C59;">Semua yang kamu butuhkan</h2>
    <p class="text-slate-500 text-sm mb-8">8 jenis analisis statistik, visualisasi grafik, dan export PDF dalam satu platform.</p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        <?php $__currentLoopData = [
            ['Statistik Deskriptif', 'Mean, median, modus, std dev, kuartil, dan lebih banyak lagi'],
            ['Uji Hipotesis', 'Uji-t (3 jenis), ANOVA, uji normalitas Shapiro-Wilk & KS'],
            ['Korelasi & Regresi', 'Pearson, Spearman, regresi linear sederhana & berganda'],
            ['Chi-square Test', 'Uji hubungan antar variabel kategorik + Cramér\'s V'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl border border-slate-200 p-5 hover:shadow-sm transition" style="border-top: 3px solid #F6C90E;">
                <h3 class="sw-display text-sm font-semibold mb-1" style="color: #0F2C59;"><?php echo e($title); ?></h3>
                <p class="text-xs text-slate-500 leading-relaxed"><?php echo e($desc); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <div class="rounded-2xl p-8 sm:p-10 mb-12" style="background: #0F2C59;">
        <h2 class="sw-display text-xl font-bold text-white mb-8 text-center">Cara Kerja StatWeb</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <?php $__currentLoopData = [
                ['01', 'Upload Dataset', 'Upload file CSV atau Excel kamu. StatWeb otomatis mendeteksi tipe data tiap kolom.'],
                ['02', 'Pilih Analisis', 'Pilih jenis analisis yang kamu butuhkan. Tersedia 8 jenis analisis statistik lengkap.'],
                ['03', 'Export Hasil', 'Hasil analisis lengkap dengan grafik bisa langsung diexport ke PDF untuk laporan.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num, $title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-center">
                    <div class="sw-mono text-4xl font-bold mb-3" style="color: rgba(246,201,14,0.4);"><?php echo e($num); ?></div>
                    <h3 class="sw-display text-sm font-semibold text-white mb-2"><?php echo e($title); ?></h3>
                    <p class="text-xs text-slate-400 leading-relaxed"><?php echo e($desc); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="sw-display text-sm font-semibold mb-4 pb-3 border-b border-slate-100" style="color: #0F2C59;">
                Fitur Analisis
            </h3>
            <div class="space-y-2.5">
                <?php $__currentLoopData = [
                    'Statistik Deskriptif (mean, median, modus, std dev, kuartil)',
                    'Distribusi Frekuensi untuk data kategorik',
                    'Uji Normalitas (Shapiro-Wilk & Kolmogorov-Smirnov)',
                    'Uji-t Satu Sampel, Independen, Berpasangan',
                    'One-Way ANOVA',
                    'Korelasi Pearson & Spearman',
                    'Chi-square Test + Cramér\'s V',
                    'Regresi Linear Sederhana & Berganda (OLS)',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fitur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-2.5 text-sm text-slate-600">
                        <span class="mt-0.5 flex-shrink-0 font-bold" style="color: #F6C90E;">✓</span>
                        <?php echo e($fitur); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="sw-display text-sm font-semibold mb-4 pb-3 border-b border-slate-100" style="color: #0F2C59;">
                Fitur Platform
            </h3>
            <div class="space-y-2.5">
                <?php $__currentLoopData = [
                    'Tidak perlu login — langsung pakai tanpa daftar',
                    'Upload CSV dan Excel (XLSX, XLS) hingga 50MB',
                    'Data View editable — edit data langsung di browser',
                    'Variable View — atur tipe, label, measure per kolom',
                    'Visualisasi grafik (histogram, bar chart, scatter plot)',
                    'Riwayat analisis tersimpan otomatis (Output Viewer)',
                    'Export hasil analisis ke PDF lengkap dengan grafik',
                    'Export dataset ke CSV',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fitur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-2.5 text-sm text-slate-600">
                        <span class="mt-0.5 flex-shrink-0 font-bold" style="color: #F6C90E;">✓</span>
                        <?php echo e($fitur); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>


<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
    <div class="rounded-2xl p-10 text-center" style="background: #0F2C59;">
        <div class="sw-mono text-sm mb-3" style="color: #F6C90E;">— StatWeb —</div>
        <h2 class="sw-display text-2xl sm:text-3xl font-bold text-white mb-3">
            Mulai analisis data kamu sekarang
        </h2>
        <p class="text-slate-400 text-sm mb-6">Gratis, tanpa instalasi, tanpa login. Langsung dari browser.</p>
        <a href="<?php echo e(route('dashboard')); ?>"
           class="inline-block text-white px-8 py-3 rounded-xl text-sm font-semibold transition"
           style="background: #F6C90E; color: #0F2C59;">
            Buka StatWeb
        </a>
    </div>
</section>


<footer class="border-t border-slate-200 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded flex items-center justify-center sw-mono font-bold text-xs" style="background: #0F2C59; color: #F6C90E;">Σ</div>
            <span class="sw-display text-sm font-semibold" style="color: #0F2C59;">StatWeb</span>
        </div>
        <p class="text-xs text-slate-400 sw-mono">Aplikasi web analisis statistik — Proyek Akhir</p>
    </div>
</footer>

</body>
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/welcome.blade.php ENDPATH**/ ?>