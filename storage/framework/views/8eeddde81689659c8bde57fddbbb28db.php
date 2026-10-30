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
     <?php $__env->slot('header', null, []); ?> <?php echo e($analysisResult->analysis_label); ?> <?php $__env->endSlot(); ?>

    <div class="mb-4 flex justify-between items-center">
        <a href="<?php echo e(route('analysis.history', $dataset->id)); ?>" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Riwayat
        </a>
        <div class="flex gap-2">
            <a href="<?php echo e(route('export.analysis_pdf', [$dataset->id, $analysisResult->id])); ?>"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Export PDF
            </a>
            <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
               class="bg-cyan-700 text-white px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-cyan-800 transition">
                Analisis Baru
            </a>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Dataset</p>
                <p class="font-medium text-slate-700"><?php echo e($dataset->name); ?></p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Jenis Analisis</p>
                <p class="font-medium text-slate-700"><?php echo e(str_replace('_', ' ', ucfirst($analysisResult->analysis_type))); ?></p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Parameter</p>
                <?php $__currentLoopData = $analysisResult->parameters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="text-xs text-slate-600 sw-mono"><?php echo e($key); ?>: <?php echo e(is_array($val) ? implode(', ', $val) : $val); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Dijalankan</p>
                <p class="text-xs text-slate-600 sw-mono"><?php echo e($analysisResult->created_at->format('d M Y, H:i:s')); ?></p>
            </div>
        </div>
    </div>

    
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-4">Hasil</h3>

        <?php $result = $analysisResult->result; ?>

        <?php if($analysisResult->analysis_type === 'descriptive'): ?>
            <?php $stats = $result['stats']; ?>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <?php $__currentLoopData = [
                    'n' => 'N', 'mean' => 'Mean', 'median' => 'Median', 'mode' => 'Modus',
                    'std_dev' => 'Std Dev', 'variance' => 'Variansi', 'min' => 'Min',
                    'max' => 'Max', 'range' => 'Range', 'q1' => 'Q1', 'q3' => 'Q3'
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $lbl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 uppercase tracking-wide"><?php echo e($lbl); ?></p>
                        <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">
                            <?php echo e(is_numeric($stats[$key]) ? number_format($stats[$key], 4) : $stats[$key]); ?>

                        </p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        <?php elseif($analysisResult->analysis_type === 'categorical'): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Kategori</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Frekuensi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Persentase</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $result['frequencies']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat => $freq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="px-4 py-2 text-slate-700"><?php echo e($cat); ?></td>
                            <td class="px-4 py-2 sw-mono text-slate-600"><?php echo e($freq); ?></td>
                            <td class="px-4 py-2 sw-mono text-slate-600"><?php echo e(number_format(($freq / $result['total']) * 100, 1)); ?>%</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <?php $displayResult = isset($result['result']) ? $result['result'] : $result; ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $displayResult; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(!is_array($val)): ?>
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-2 font-medium text-slate-600 w-1/2">
                                    <?php echo e(ucwords(str_replace('_', ' ', $key))); ?>

                                </td>
                                <td class="py-2 sw-mono text-slate-700">
                                    <?php if(is_bool($val)): ?>
                                        <?php if($val): ?>
                                            <span class="px-2 py-0.5 text-xs bg-red-50 text-red-700 rounded">Ya (Signifikan)</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 text-xs bg-emerald-50 text-emerald-700 rounded">Tidak Signifikan</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php echo e($val); ?>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <?php if($analysisResult->analysis_type === 'regression_multiple' && isset($result['coefficients'])): ?>
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-5 mb-2">Koefisien</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Variabel</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Koefisien</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">t</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">p-value</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Sig.</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        <?php $__currentLoopData = $result['coefficients']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coef): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-2 font-medium text-slate-700"><?php echo e($coef['label']); ?></td>
                                <td class="px-4 py-2 sw-mono text-slate-600"><?php echo e($coef['coefficient']); ?></td>
                                <td class="px-4 py-2 sw-mono text-slate-600"><?php echo e($coef['t']); ?></td>
                                <td class="px-4 py-2 sw-mono <?php echo e($coef['p_value'] < 0.05 ? 'text-red-600 font-medium' : 'text-slate-600'); ?>"><?php echo e($coef['p_value']); ?></td>
                                <td class="px-4 py-2">
                                    <?php if($coef['significant']): ?>
                                        <span class="px-2 py-0.5 text-xs bg-red-50 text-red-700 rounded">Ya *</span>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 text-xs bg-slate-100 text-slate-500 rounded">Tidak</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="mt-5 pt-4 border-t border-slate-100 flex gap-3 text-xs">
            <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>" class="text-cyan-700 hover:underline font-medium">
                Jalankan Analisis Baru →
            </a>
            <span class="text-slate-300">|</span>
            <a href="<?php echo e(route('analysis.history', $dataset->id)); ?>" class="text-slate-500 hover:text-slate-700">
                Lihat Semua Riwayat →
            </a>
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