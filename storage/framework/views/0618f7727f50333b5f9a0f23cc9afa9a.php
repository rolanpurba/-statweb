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
            Riwayat Analisis — <?php echo e($dataset->name); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Dataset
                </a>
                <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                    + Analisis Baru
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        Output Viewer
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Semua hasil analisis yang pernah dijalankan pada dataset ini
                    </p>
                </div>

                <?php if($histories->isEmpty()): ?>
                    <div class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada analisis yang dijalankan pada dataset ini.
                        <div class="mt-2">
                            <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
                               class="text-indigo-600 hover:underline">
                                Jalankan analisis pertama →
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Analisis</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Tipe</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Dijalankan</th>
                            <th class="px-6 py-2"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <?php $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <p class="text-sm font-medium text-gray-800"><?php echo e($history->analysis_label); ?></p>
                                </td>
                                <td class="px-6 py-3">
                                        <span class="px-2 py-0.5 text-xs rounded-full
                                            <?php if(str_contains($history->analysis_type, 'regression')): ?> bg-emerald-100 text-emerald-700
                                            <?php elseif(str_contains($history->analysis_type, 'ttest')): ?> bg-orange-100 text-orange-700
                                            <?php elseif($history->analysis_type === 'anova'): ?> bg-cyan-100 text-cyan-700
                                            <?php elseif($history->analysis_type === 'correlation'): ?> bg-blue-100 text-blue-700
                                            <?php elseif($history->analysis_type === 'normality'): ?> bg-yellow-100 text-yellow-700
                                            <?php elseif($history->analysis_type === 'chi_square'): ?> bg-pink-100 text-pink-700
                                            <?php elseif($history->analysis_type === 'descriptive'): ?> bg-indigo-100 text-indigo-700
                                            <?php else: ?> bg-gray-100 text-gray-600
                                            <?php endif; ?>
                                        ">
                                            <?php echo e(str_replace('_', ' ', ucfirst($history->analysis_type))); ?>

                                        </span>
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-400">
                                    <?php echo e($history->created_at->format('d M Y, H:i')); ?>

                                    <span class="text-gray-300 ml-1">(<?php echo e($history->created_at->diffForHumans()); ?>)</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="<?php echo e(route('analysis.history_show', [$dataset->id, $history->id])); ?>"
                                       class="text-xs text-indigo-600 hover:underline">
                                        Lihat →
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <div class="px-6 py-4">
                        <?php echo e($histories->links()); ?>

                    </div>
                <?php endif; ?>
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/history.blade.php ENDPATH**/ ?>