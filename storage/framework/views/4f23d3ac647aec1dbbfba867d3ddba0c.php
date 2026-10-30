<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> Dashboard <?php $__env->endSlot(); ?>

    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 transition hover:border-[#F6C90E]/50 hover:shadow-sm">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Dataset Tersimpan</p>
            <p class="sw-mono text-3xl font-semibold text-slate-800 counter" data-target="<?php echo e($totalDatasets); ?>">0</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 transition hover:border-[#F6C90E]/50 hover:shadow-sm">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Total Baris Data</p>
            <p class="sw-mono text-3xl font-semibold text-slate-800 counter" data-target="<?php echo e($totalRows); ?>">0</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 transition hover:border-[#F6C90E]/50 hover:shadow-sm">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Upload Terakhir</p>
            <p class="text-sm font-medium text-slate-700 mt-2.5">
                <?php echo e($latestDataset ? $latestDataset->created_at->diffForHumans() : '—'); ?>

            </p>
            <?php if($latestDataset): ?>
                <p class="text-xs text-slate-400 mt-0.5 truncate"><?php echo e($latestDataset->name); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-slate-700 sw-display">Dataset Terbaru</h3>
                <a href="<?php echo e(route('datasets.index')); ?>" class="text-xs font-medium hover:underline" style="color: #0F2C59;">
                    Lihat semua
                </a>
            </div>

            <?php if($recentDatasets->isEmpty()): ?>
                <div class="px-5 py-12 text-center text-slate-400 text-sm">
                    Belum ada dataset. Upload file CSV atau Excel untuk memulai.
                    <div class="mt-3">
                        <a href="<?php echo e(route('datasets.index')); ?>"
                           class="inline-block text-white px-4 py-2 rounded-lg text-sm font-medium transition" style="background: #0F2C59;">
                            Upload dataset
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $recentDatasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between px-5 py-3.5 transition hover:bg-slate-50 group">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate group-hover:text-[#0F2C59] transition"><?php echo e($dataset->name); ?></p>
                                <p class="text-xs text-slate-400 sw-mono mt-0.5">
                                    <?php echo e($dataset->columns->count()); ?> kolom · <?php echo e($dataset->rows()->count()); ?> baris · <?php echo e($dataset->created_at->format('d M Y')); ?>

                                </p>
                            </div>
                            <div class="flex gap-3 flex-shrink-0 ml-4">
                                <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-xs text-slate-500 hover:text-slate-700 font-medium">Data</a>
                                <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>" class="text-xs font-medium" style="color: #0F2C59;">Analisis</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="space-y-4">

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-700 sw-display">Aksi Cepat</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    <a href="<?php echo e(route('datasets.index')); ?>" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition group">
                        <span class="text-sm text-slate-700">Upload Dataset Baru</span>
                        <span class="text-sm transition group-hover:translate-x-0.5 inline-block" style="color: #F6C90E;">→</span>
                    </a>
                    <?php if($latestDataset): ?>
                        <a href="<?php echo e(route('analysis.create', $latestDataset->id)); ?>" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition group">
                            <span class="text-sm text-slate-700">Analisis Dataset Terakhir</span>
                            <span class="text-sm transition group-hover:translate-x-0.5 inline-block" style="color: #F6C90E;">→</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-xl overflow-hidden" style="background: #0F2C59;">
                <div class="px-5 py-4 border-b border-white/10">
                    <h3 class="text-sm font-semibold text-white sw-display">Fitur Analisis</h3>
                </div>
                <div class="px-5 py-4 space-y-2">
                    <?php $__currentLoopData = [
                        'Statistik Deskriptif',
                        'Distribusi Frekuensi',
                        'Uji Normalitas',
                        'Uji-t (3 jenis)',
                        'One-Way ANOVA',
                        'Korelasi Pearson & Spearman',
                        'Chi-square Test',
                        'Regresi Linear',
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fitur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-2 text-xs text-slate-300 hover:text-white transition">
                            <span class="w-1 h-1 rounded-full flex-shrink-0" style="background: #F6C90E;"></span>
                            <?php echo e($fitur); ?>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.counter').forEach(el => {
            const target = parseInt(el.dataset.target, 10);
            if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
            const duration = 700;
            const start = performance.now();
            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target).toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target.toLocaleString('id-ID');
            }
            requestAnimationFrame(tick);
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>

<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/dashboard.blade.php ENDPATH**/ ?>