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
     <?php $__env->slot('header', null, []); ?> Riwayat Analisis <?php $__env->endSlot(); ?>

    <div class="mb-4 flex justify-between items-center">
        <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Dataset
        </a>
        <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
           class="bg-cyan-700 text-white px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-cyan-800 transition">
            + Analisis Baru
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-slate-700 sw-display">Output Viewer</h3>
                <p class="text-xs text-slate-400 mt-0.5"><?php echo e($dataset->name); ?></p>
            </div>
            <a href="<?php echo e(route('export.history_pdf', $dataset->id)); ?>"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Export Semua PDF
            </a>
        </div>

        <?php if($histories->isEmpty()): ?>
            <div class="px-6 py-12 text-center text-slate-400 text-sm">
                Belum ada analisis yang dijalankan.
                <div class="mt-2">
                    <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
                       class="text-cyan-700 hover:underline font-medium">
                        Jalankan analisis pertama →
                    </a>
                </div>
            </div>
        <?php else: ?>
            
            <div class="hidden sm:block divide-y divide-slate-100">
                <?php $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center justify-between px-6 py-3.5 hover:bg-slate-50/60 transition">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate"><?php echo e($history->analysis_label); ?></p>
                            <p class="text-xs text-slate-400 sw-mono mt-0.5">
                                <?php echo e($history->created_at->format('d M Y, H:i')); ?> ·
                                <?php echo e($history->created_at->diffForHumans()); ?>

                            </p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span class="px-2 py-0.5 text-xs rounded-full
                                <?php if(str_contains($history->analysis_type, 'regression')): ?> bg-emerald-100 text-emerald-700
                                <?php elseif(str_contains($history->analysis_type, 'ttest')): ?> bg-orange-100 text-orange-700
                                <?php elseif($history->analysis_type === 'anova'): ?> bg-cyan-100 text-cyan-700
                                <?php elseif($history->analysis_type === 'correlation'): ?> bg-blue-100 text-blue-700
                                <?php elseif($history->analysis_type === 'normality'): ?> bg-yellow-100 text-yellow-700
                                <?php elseif($history->analysis_type === 'chi_square'): ?> bg-pink-100 text-pink-700
                                <?php elseif($history->analysis_type === 'descriptive'): ?> bg-indigo-100 text-indigo-700
                                <?php else: ?> bg-slate-100 text-slate-600
                                <?php endif; ?>">
                                <?php echo e(str_replace('_', ' ', ucfirst($history->analysis_type))); ?>

                            </span>
                            <a href="<?php echo e(route('analysis.history_show', [$dataset->id, $history->id])); ?>"
                               class="text-xs text-cyan-700 hover:text-cyan-800 font-medium">
                                Lihat →
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            
            <div class="sm:hidden divide-y divide-slate-100">
                <?php $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-4 py-3.5">
                        <p class="text-sm font-medium text-slate-800"><?php echo e($history->analysis_label); ?></p>
                        <p class="text-xs text-slate-400 sw-mono mt-0.5 mb-2"><?php echo e($history->created_at->format('d M Y, H:i')); ?></p>
                        <div class="flex gap-3 items-center">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-600">
                                <?php echo e(str_replace('_', ' ', ucfirst($history->analysis_type))); ?>

                            </span>
                            <a href="<?php echo e(route('analysis.history_show', [$dataset->id, $history->id])); ?>"
                               class="text-xs text-cyan-700 font-medium">Lihat →</a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="px-6 py-4 border-t border-slate-100">
                <?php echo e($histories->links()); ?>

            </div>
        <?php endif; ?>
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/history.blade.php ENDPATH**/ ?>