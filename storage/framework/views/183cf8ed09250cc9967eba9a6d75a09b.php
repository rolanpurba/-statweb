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
     <?php $__env->slot('header', null, []); ?> Dataset <?php $__env->endSlot(); ?>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
        <h3 class="text-sm font-semibold text-slate-700 sw-display mb-4">Upload Dataset Baru</h3>

        <?php if(session('success')): ?>
            <div class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg text-sm">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('datasets.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">Nama Dataset</label>
                    <input type="text" name="name" required
                           placeholder="Contoh: Data Nilai Ujian Siswa"
                           class="w-full border-slate-300 rounded-lg text-sm focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">File (CSV / Excel)</label>
                    <input type="file" name="file" required accept=".csv,.xlsx,.xls"
                           class="w-full text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">Format: CSV, XLSX, XLS. Maksimal 50MB.</p>
                </div>
            </div>
            <button type="submit"
                    class="text-white px-5 py-2 rounded-lg text-sm font-medium transition" style="background: #0F2C59;">
                Upload
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 sw-display">Dataset Tersimpan</h3>
        </div>

        <?php if($datasets->isEmpty()): ?>
            <div class="px-6 py-12 text-center text-slate-400 text-sm">
                Belum ada dataset yang diupload.
            </div>
        <?php else: ?>
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-2 text-left text-xs font-medium text-slate-400 uppercase">Nama</th>
                        <th class="px-6 py-2 text-left text-xs font-medium text-slate-400 uppercase">Diupload</th>
                        <th class="px-6 py-2"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3.5 text-sm font-medium text-slate-800"><?php echo e($dataset->name); ?></td>
                            <td class="px-6 py-3.5 text-xs text-slate-400 sw-mono"><?php echo e($dataset->created_at->format('d M Y, H:i')); ?></td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-xs font-medium mr-4" style="color: #0F2C59;">Lihat</a>
                                <form action="<?php echo e(route('datasets.destroy', $dataset->id)); ?>" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-600 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="sm:hidden divide-y divide-slate-100">
                <?php $__currentLoopData = $datasets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dataset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="px-4 py-3.5">
                        <p class="text-sm font-medium text-slate-800"><?php echo e($dataset->name); ?></p>
                        <p class="text-xs text-slate-400 sw-mono mt-0.5 mb-2"><?php echo e($dataset->created_at->format('d M Y, H:i')); ?></p>
                        <div class="flex gap-4">
                            <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-xs font-medium" style="color: #0F2C59;">Lihat</a>
                            <form action="<?php echo e(route('datasets.destroy', $dataset->id)); ?>" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-xs text-red-500 font-medium">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/datasets/index.blade.php ENDPATH**/ ?>