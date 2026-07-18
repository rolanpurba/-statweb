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
            Analisis Statistik — <?php echo e($dataset->name); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4">
                <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Dataset
                </a>
            </div>

            <?php if($errors->any()): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Statistik Deskriptif</h3>
                <p class="text-sm text-gray-500 mb-4">Mean, median, std dev, dll — untuk kolom numerik</p>
                <?php if($numericColumns->isEmpty()): ?>
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('analysis.descriptive', $dataset->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                <option value="">-- Pilih Kolom Numerik --</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Hitung Statistik Deskriptif
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Distribusi Frekuensi</h3>
                <p class="text-sm text-gray-500 mb-4">Jumlah kemunculan tiap kategori — untuk kolom kategorik</p>
                <?php if($categoricalColumns->isEmpty()): ?>
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom kategorik.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('analysis.categorical', $dataset->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                <option value="">-- Pilih Kolom Kategorik --</option>
                                <?php $__currentLoopData = $categoricalColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Hitung Distribusi Frekuensi
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Scatter Plot</h3>
                <p class="text-sm text-gray-500 mb-4">Lihat hubungan antara 2 kolom numerik</p>
                <?php if($numericColumns->count() < 2): ?>
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('analysis.scatter', $dataset->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sumbu X</label>
                                <select name="column_x" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sumbu Y</label>
                                <select name="column_y" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                            Buat Scatter Plot
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Uji Normalitas</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Shapiro-Wilk (n &lt; 50) atau Kolmogorov-Smirnov (n ≥ 50) — prasyarat sebelum uji parametrik
                </p>
                <?php if($numericColumns->isEmpty()): ?>
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('analysis.normality', $dataset->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="mb-4">
                            <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                <option value="">-- Pilih Kolom Numerik --</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                            Uji Normalitas
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Uji-t Satu Sampel</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bandingkan rata-rata sampel dengan nilai tertentu (misal: apakah rata-rata = 75?)
                </p>
                <?php if($numericColumns->isEmpty()): ?>
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('analysis.ttest_one_sample', $dataset->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Numerik</label>
                                <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Uji (Test Value)</label>
                                <input type="number" name="test_value" step="any" required
                                       placeholder="contoh: 75"
                                       class="w-full border-gray-300 rounded shadow-sm">
                            </div>
                        </div>
                        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                            Jalankan Uji-t Satu Sampel
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Uji-t Independen</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bandingkan rata-rata 2 kelompok berbeda (kolom kelompok harus memiliki tepat 2 kategori)
                </p>
                <?php if($numericColumns->isEmpty() || $categoricalColumns->isEmpty()): ?>
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 1 kolom numerik dan 1 kolom kategorik (2 kategori).</p>
                <?php else: ?>
                    <form action="<?php echo e(route('analysis.ttest_independent', $dataset->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Nilai (Numerik)</label>
                                <select name="column_value" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Kelompok (Kategorik)</label>
                                <select name="column_group" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    <?php $__currentLoopData = $categoricalColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-rose-500 text-white px-4 py-2 rounded hover:bg-rose-600">
                            Jalankan Uji-t Independen
                        </button>
                    </form>
                <?php endif; ?>
            </div>

            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-1">Uji-t Berpasangan</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bandingkan 2 pengukuran pada subjek yang sama (misal: nilai sebelum vs sesudah treatment)
                </p>
                <?php if($numericColumns->count() < 2): ?>
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
                <?php else: ?>
                    <form action="<?php echo e(route('analysis.ttest_paired', $dataset->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom 1 (Sebelum)</label>
                                <select name="column_1" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom 2 (Sesudah)</label>
                                <select name="column_2" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">
                            Jalankan Uji-t Berpasangan
                        </button>
                    </form>
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/analysis/create.blade.php ENDPATH**/ ?>