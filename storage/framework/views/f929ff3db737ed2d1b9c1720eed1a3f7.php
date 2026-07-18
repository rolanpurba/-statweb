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
            Uji Normalitas — <?php echo e($dataset->name); ?>

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
                <p class="text-sm text-gray-500 mb-4">N = <?php echo e($n); ?> | Metode: <?php echo e($testName); ?></p>

                <table class="min-w-full divide-y divide-gray-200 mb-4">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Uji yang digunakan</td>
                        <td class="px-4 py-2"><?php echo e($testName); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Statistik</td>
                        <td class="px-4 py-2"><?php echo e($result['statistic']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">p-value</td>
                        <td class="px-4 py-2"><?php echo e($result['p_value']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Kesimpulan</td>
                        <td class="px-4 py-2">
                            <?php if($result['normal']): ?>
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-medium">
                                        ✓ <?php echo e($result['conclusion']); ?>

                                    </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded font-medium">
                                        ✗ <?php echo e($result['conclusion']); ?>

                                    </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Interpretasi:</strong>
                    <?php if($result['normal']): ?>
                        Data berdistribusi normal. Kamu dapat melanjutkan dengan uji parametrik
                        seperti Uji-t atau ANOVA.
                    <?php else: ?>
                        Data tidak berdistribusi normal. Pertimbangkan menggunakan uji nonparametrik
                        seperti Mann-Whitney atau Wilcoxon sebagai alternatif.
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-2">Q-Q Plot</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Titik mendekati garis diagonal = data mendekati distribusi normal
                </p>
                <canvas id="qqChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const points = <?php echo json_encode($qqPoints, 15, 512) ?>;

        // Hitung garis referensi diagonal (dari titik min ke max)
        const xVals = points.map(p => p.x);
        const yVals = points.map(p => p.y);
        const minVal = Math.min(...xVals, ...yVals);
        const maxVal = Math.max(...xVals, ...yVals);

        const ctx = document.getElementById('qqChart');
        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Data',
                        data: points,
                        backgroundColor: 'rgba(99, 102, 241, 0.7)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        pointRadius: 5,
                    },
                    {
                        label: 'Garis Normal',
                        data: [{ x: minVal, y: minVal }, { x: maxVal, y: maxVal }],
                        type: 'line',
                        borderColor: 'rgba(239, 68, 68, 0.8)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                },
                scales: {
                    x: {
                        title: { display: true, text: 'Kuantil Teoritis (Normal)' },
                    },
                    y: {
                        title: { display: true, text: 'Kuantil Empiris (Data)' },
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/normality.blade.php ENDPATH**/ ?>