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
            Regresi Linear Berganda — <?php echo e($dataset->name); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>" class="text-indigo-600 hover:underline">
                    &larr; Pilih Analisis Lain
                </a>
                <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-gray-500 hover:underline text-sm">
                    Kembali ke Dataset
                </a>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Variabel Dependen: <?php echo e($columnY); ?></h3>
                <p class="text-sm text-gray-500 mb-4">
                    Prediktor (X): <?php echo e(implode(', ', $columnsX)); ?> | N = <?php echo e($result['n']); ?>

                </p>

                <div class="p-4 bg-indigo-50 text-indigo-800 rounded-lg text-center mb-6">
                    <p class="text-xs text-indigo-500 mb-1">Persamaan Regresi</p>
                    <p class="text-lg font-bold font-mono"><?php echo e($result['equation']); ?></p>
                </div>

                
                <h4 class="font-medium text-gray-700 mb-2">Model Summary</h4>
                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700 w-1/2">R² (R-squared)</td>
                        <td class="px-4 py-2"><?php echo e($result['r2']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">R² Adjusted</td>
                        <td class="px-4 py-2"><?php echo e($result['r2_adj']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Standard Error</td>
                        <td class="px-4 py-2"><?php echo e($result['se']); ?></td>
                    </tr>
                    </tbody>
                </table>

                
                <h4 class="font-medium text-gray-700 mb-2">ANOVA</h4>
                <table class="min-w-full divide-y divide-gray-200 mb-6 text-sm">
                    <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Sumber</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">df</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">F</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">p-value</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2">Regresi</td>
                        <td class="px-4 py-2"><?php echo e($result['df_reg']); ?></td>
                        <td class="px-4 py-2 font-medium"><?php echo e($result['f']); ?></td>
                        <td class="px-4 py-2 font-medium"><?php echo e($result['f_pvalue']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">Residual</td>
                        <td class="px-4 py-2"><?php echo e($result['df_res']); ?></td>
                        <td class="px-4 py-2">—</td>
                        <td class="px-4 py-2">—</td>
                    </tr>
                    </tbody>
                </table>

                
                <h4 class="font-medium text-gray-700 mb-2">Koefisien</h4>
                <table class="min-w-full divide-y divide-gray-200 mb-4 text-sm">
                    <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Variabel</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Koefisien</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Std Error</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">t</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">p-value</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Signifikan</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <?php $__currentLoopData = $coefficients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coef): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-2 font-medium"><?php echo e($coef['label']); ?></td>
                            <td class="px-4 py-2"><?php echo e($coef['coefficient']); ?></td>
                            <td class="px-4 py-2"><?php echo e($coef['se']); ?></td>
                            <td class="px-4 py-2"><?php echo e($coef['t']); ?></td>
                            <td class="px-4 py-2"><?php echo e($coef['p_value']); ?></td>
                            <td class="px-4 py-2">
                                <?php if($coef['significant']): ?>
                                    <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded">Ya *</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-500 rounded">Tidak</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <p class="text-xs text-gray-400 mb-4">* signifikan pada α = 0.05</p>

                <div class="mb-4">
                    <?php if($result['significant']): ?>
                        <span class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded font-medium">
                            ✗ <?php echo e($result['conclusion']); ?>

                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded font-medium">
                            ✓ <?php echo e($result['conclusion']); ?>

                        </span>
                    <?php endif; ?>
                </div>

                
                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Cara membaca hasil:</strong>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>R²</strong>: <?php echo e($result['r2']); ?> — model menjelaskan <?php echo e(round($result['r2'] * 100, 1)); ?>% variasi pada <?php echo e($columnY); ?></li>
                        <li><strong>R² Adjusted</strong>: <?php echo e($result['r2_adj']); ?> — R² yang sudah disesuaikan dengan jumlah prediktor</li>
                        <li><strong>F-test</strong>: F = <?php echo e($result['f']); ?>, p = <?php echo e($result['f_pvalue']); ?>

                            → <?php if($result['significant']): ?> <span class="text-red-600">Model signifikan secara keseluruhan</span>
                            <?php else: ?> <span class="text-green-600">Model tidak signifikan secara keseluruhan</span>
                            <?php endif; ?>
                        </li>
                        <li>Lihat kolom p-value tiap koefisien untuk mengetahui prediktor mana yang berkontribusi signifikan.</li>
                        <li class="text-xs text-blue-500 mt-1">Gunakan R² Adjusted (bukan R²) untuk membandingkan model dengan jumlah prediktor berbeda.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/regression_multiple.blade.php ENDPATH**/ ?>