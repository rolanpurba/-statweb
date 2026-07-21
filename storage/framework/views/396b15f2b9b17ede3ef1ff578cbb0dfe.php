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
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Statistik Deskriptif — <?php echo e($dataset->name); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>" class="text-indigo-600 hover:underline">
                    &larr; Pilih Kolom Lain
                </a>
                <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-gray-500 hover:underline text-sm">
                    Kembali ke Dataset
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Kolom: <?php echo e($columnName); ?></h3>
                <p class="text-sm text-gray-500 mb-4">Jumlah data valid (N): <?php echo e($stats['n']); ?></p>

                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Mean (Rata-rata)</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['mean'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Median</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['median'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Modus</td>
                        <td class="px-4 py-2"><?php echo e($stats['mode']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Standar Deviasi</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['std_dev'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Variansi</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['variance'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Minimum</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['min'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Maksimum</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['max'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Range</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['range'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Q1 (Kuartil 1)</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['q1'], 4)); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Q3 (Kuartil 3)</td>
                        <td class="px-4 py-2"><?php echo e(number_format($stats['q3'], 4)); ?></td>
                    </tr>
                    </tbody>
                </table>

                <p class="text-xs text-gray-400 mt-4">
                    Catatan: Standar deviasi & variansi dihitung menggunakan rumus sampel (n-1),
                    konsisten dengan STDEV.S / VAR.S di Excel. Kuartil menggunakan metode interpolasi
                    linear, konsisten dengan QUARTILE.INC di Excel.
                </p>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Histogram — <?php echo e($columnName); ?></h3>
                <canvas id="histogramChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('histogramChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($histogram['labels'], 15, 512) ?>,
                datasets: [{
                    label: 'Frekuensi',
                    data: <?php echo json_encode($histogram['values'], 15, 512) ?>,
                    backgroundColor: 'rgba(79, 70, 229, 0.6)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Frekuensi' },
                        ticks: { stepSize: 1 },
                    },
                    x: {
                        title: { display: true, text: '<?php echo e($columnName); ?>' },
                    },
                },
            },
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/descriptive.blade.php ENDPATH**/ ?>