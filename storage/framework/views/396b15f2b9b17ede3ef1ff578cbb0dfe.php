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
     <?php $__env->slot('header', null, []); ?> Statistik Deskriptif <?php $__env->endSlot(); ?>

    <div class="mb-4 flex justify-between items-center">
        <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1"><?php echo e($columnName); ?></h3>
        <p class="text-xs text-slate-400 sw-mono mb-5">N = <?php echo e($stats['n']); ?></p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-2">
            <?php $__currentLoopData = [
                'mean' => 'Mean', 'median' => 'Median', 'mode' => 'Modus', 'std_dev' => 'Std Dev',
                'variance' => 'Variansi', 'min' => 'Min', 'max' => 'Max', 'range' => 'Range',
                'q1' => 'Q1', 'q3' => 'Q3',
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-400 uppercase tracking-wide"><?php echo e($lbl); ?></p>
                    <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">
                        <?php echo e(is_numeric($stats[$key]) ? number_format($stats[$key], 4) : $stats[$key]); ?>

                    </p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <p class="text-xs text-slate-400 mt-4">
            Standar deviasi & variansi memakai rumus sampel (n-1), konsisten dengan STDEV.S / VAR.S di Excel.
            Kuartil memakai interpolasi linear, konsisten dengan QUARTILE.INC di Excel.
        </p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-slate-800 sw-display">Histogram</h3>
            <?php if($latestResult): ?>
                <button onclick="exportPDF()" id="exportBtn"
                        class="text-white px-3 py-1.5 text-xs font-medium rounded-lg transition flex items-center gap-2"
                        style="background: #0F2C59;">
                    <span id="exportLabel">Export PDF</span>
                </button>
            <?php endif; ?>
        </div>
        <canvas id="histogramChart" height="100"></canvas>
    </div>

    <?php if($latestResult): ?>
        <form id="exportForm" action="<?php echo e(route('export.analysis_pdf', [$dataset->id, $latestResult->id])); ?>" method="POST" style="display:none;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="chart_image" id="chartImage">
        </form>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chart = new Chart(document.getElementById('histogramChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($histogram['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Frekuensi',
                    data: <?php echo json_encode($histogram['values'], 15, 512) ?>,
                    backgroundColor: 'rgba(15, 44, 89, 0.6)',
                    borderColor: 'rgba(15, 44, 89, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                animation: { duration: 500 },
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Frekuensi' }, ticks: { stepSize: 1 } },
                    x: { title: { display: true, text: '<?php echo e($columnName); ?>' } },
                },
            },
        });

        function exportPDF() {
            const btn = document.getElementById('exportBtn');
            const label = document.getElementById('exportLabel');
            btn.disabled = true;
            label.textContent = 'Menyiapkan...';

            setTimeout(() => {
                const canvas = document.getElementById('histogramChart');
                const base64 = canvas.toDataURL('image/png', 1.0);
                document.getElementById('chartImage').value = base64;
                document.getElementById('exportForm').submit();

                setTimeout(() => {
                    btn.disabled = false;
                    label.textContent = 'Export PDF';
                }, 2000);
            }, 600);
        }
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/descriptive.blade.php ENDPATH**/ ?>