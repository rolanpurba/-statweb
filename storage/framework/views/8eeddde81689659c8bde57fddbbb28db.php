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
            <?php echo e($analysisResult->analysis_label); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('analysis.history', $dataset->id)); ?>" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Riwayat
                </a>
                <span class="text-xs text-gray-400">
                    Dijalankan: <?php echo e($analysisResult->created_at->format('d M Y, H:i')); ?>

                </span>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Dataset</p>
                        <p class="font-medium"><?php echo e($dataset->name); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Jenis Analisis</p>
                        <p class="font-medium"><?php echo e(str_replace('_', ' ', ucfirst($analysisResult->analysis_type))); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Parameter</p>
                        <div class="space-y-0.5">
                            <?php $__currentLoopData = $analysisResult->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="text-gray-600">
                                    <span class="font-medium"><?php echo e($key); ?>:</span>
                                    <?php echo e(is_array($val) ? implode(', ', $val) : $val); ?>

                                </p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Waktu</p>
                        <p class="text-gray-600"><?php echo e($analysisResult->created_at->format('d M Y, H:i:s')); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Hasil</h3>

                <?php $result = $analysisResult->result; ?>

                <?php if($analysisResult->analysis_type === 'descriptive'): ?>
                    <?php $stats = $result['stats']; ?>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = [
                            'n' => 'N', 'mean' => 'Mean', 'median' => 'Median',
                            'mode' => 'Modus', 'std_dev' => 'Standar Deviasi',
                            'variance' => 'Variansi', 'min' => 'Minimum',
                            'max' => 'Maksimum', 'range' => 'Range',
                            'q1' => 'Q1 (Kuartil 1)', 'q3' => 'Q3 (Kuartil 3)'
                        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-700 w-1/2"><?php echo e($label); ?></td>
                                <td class="px-4 py-2">
                                    <?php echo e(is_numeric($stats[$key]) ? number_format($stats[$key], 4) : $stats[$key]); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                <?php elseif(in_array($analysisResult->analysis_type, ['normality', 'ttest_one_sample', 'ttest_independent', 'ttest_paired', 'anova', 'regression_simple', 'regression_multiple', 'correlation', 'chi_square'])): ?>
                    
                    <?php
                        $displayResult = isset($result['result']) ? $result['result'] : $result;
                    ?>
                    <table class="min-w-full divide-y divide-gray-200 text-sm mb-4">
                        <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $displayResult; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(!is_array($val)): ?>
                                <tr>
                                    <td class="px-4 py-2 font-medium text-gray-700 w-1/2">
                                        <?php echo e(ucwords(str_replace('_', ' ', $key))); ?>

                                    </td>
                                    <td class="px-4 py-2">
                                        <?php if(is_bool($val)): ?>
                                            <span class="px-2 py-0.5 text-xs rounded <?php echo e($val ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'); ?>">
                                                    <?php echo e($val ? 'Ya (Signifikan)' : 'Tidak (Tidak Signifikan)'); ?>

                                                </span>
                                        <?php else: ?>
                                            <?php echo e($val); ?>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    
                    <?php if($analysisResult->analysis_type === 'regression_multiple' && isset($result['coefficients'])): ?>
                        <h4 class="font-medium text-gray-700 mb-2 mt-4">Koefisien</h4>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Variabel</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Koefisien</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">t</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">p-value</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Signifikan</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <?php $__currentLoopData = $result['coefficients']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coef): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-4 py-2 font-medium"><?php echo e($coef['label']); ?></td>
                                    <td class="px-4 py-2"><?php echo e($coef['coefficient']); ?></td>
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
                    <?php endif; ?>

                <?php elseif($analysisResult->analysis_type === 'categorical'): ?>
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Kategori</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Frekuensi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Persentase</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $result['frequencies']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $freq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="px-4 py-2"><?php echo e($cat); ?></td>
                                <td class="px-4 py-2"><?php echo e($freq); ?></td>
                                <td class="px-4 py-2"><?php echo e(number_format(($freq / $result['total']) * 100, 1)); ?>%</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                <?php else: ?>
                    
                    <pre class="text-xs bg-gray-50 p-4 rounded overflow-x-auto"><?php echo e(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                <?php endif; ?>

                <div class="mt-6 pt-4 border-t border-gray-100 flex gap-3">
                    <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
                       class="text-sm text-green-600 hover:underline">
                        Jalankan Analisis Baru →
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="<?php echo e(route('analysis.history', $dataset->id)); ?>"
                       class="text-sm text-indigo-600 hover:underline">
                        Lihat Semua Riwayat →
                    </a>
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/history_show.blade.php ENDPATH**/ ?>