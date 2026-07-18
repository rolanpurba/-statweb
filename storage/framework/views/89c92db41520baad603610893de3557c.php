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
            Uji-t Berpasangan — <?php echo e($dataset->name); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>" class="text-indigo-600 hover:underline">
                    &larr; Pilih Analisis Lain
                </a>
                <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-gray-500 hover:underline text-sm">
                    Kembali ke Dataset
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1"><?php echo e($column1); ?> vs <?php echo e($column2); ?></h3>
                <p class="text-sm text-gray-500 mb-4">N pasangan data valid: <?php echo e($result['n']); ?></p>

                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded p-4">
                        <p class="font-medium text-sm mb-2"><?php echo e($column1); ?> (Sebelum)</p>
                        <p class="text-sm">Mean = <?php echo e($result['mean1']); ?></p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="font-medium text-sm mb-2"><?php echo e($column2); ?> (Sesudah)</p>
                        <p class="text-sm">Mean = <?php echo e($result['mean2']); ?></p>
                    </div>
                </div>

                
                <table class="min-w-full divide-y divide-gray-200 mb-4">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700 w-1/2">Mean selisih (d̄)</td>
                        <td class="px-4 py-2"><?php echo e($result['d_mean']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Std Dev selisih</td>
                        <td class="px-4 py-2"><?php echo e($result['d_std']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Standard Error</td>
                        <td class="px-4 py-2"><?php echo e($result['se']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">t hitung</td>
                        <td class="px-4 py-2"><?php echo e($result['t']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">df (derajat bebas)</td>
                        <td class="px-4 py-2"><?php echo e($result['df']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">p-value (2-tailed)</td>
                        <td class="px-4 py-2"><?php echo e($result['p_value']); ?></td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Kesimpulan</td>
                        <td class="px-4 py-2">
                            <?php if($result['significant']): ?>
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded font-medium">
                                        ✗ <?php echo e($result['conclusion']); ?>

                                    </span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-medium">
                                        ✓ <?php echo e($result['conclusion']); ?>

                                    </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    </tbody>
                </table>

                
                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Cara membaca hasil:</strong>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>H₀:</strong> Tidak ada perbedaan rata-rata antara <?php echo e($column1); ?> dan <?php echo e($column2); ?> (d̄ = 0)</li>
                        <li><strong>H₁:</strong> Ada perbedaan rata-rata antara <?php echo e($column1); ?> dan <?php echo e($column2); ?> (d̄ ≠ 0)</li>
                        <li>Jika p-value &lt; 0.05 → tolak H₀ (ada perubahan signifikan)</li>
                        <li>Jika p-value ≥ 0.05 → gagal tolak H₀ (tidak ada perubahan signifikan)</li>
                        <li>p-value kamu: <strong><?php echo e($result['p_value']); ?></strong>
                            → <?php if($result['significant']): ?> <span class="text-red-600">Tolak H₀</span>
                            <?php else: ?> <span class="text-green-600">Gagal tolak H₀</span>
                            <?php endif; ?>
                        </li>
                        <li class="mt-1 text-xs text-blue-500">Uji-t berpasangan cocok untuk desain pre-test/post-test atau pengukuran berulang pada subjek yang sama.</li>
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/ttest_paired.blade.php ENDPATH**/ ?>