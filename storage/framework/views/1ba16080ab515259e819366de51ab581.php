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
     <?php $__env->slot('header', null, []); ?> Analisis Statistik <?php $__env->endSlot(); ?>

    <div class="mb-4">
        <a href="<?php echo e(route('datasets.show', $dataset->id)); ?>" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Dataset
        </a>
    </div>

    <?php if($errors->any()): ?>
        <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
        $card = 'bg-white rounded-xl border border-slate-200 p-5 mb-4';
        $label = 'block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5';
        $select = 'w-full border-slate-300 rounded-lg text-sm focus:border-[#F6C90E] focus:ring-[#F6C90E]';
        $btn = 'text-white px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto';
    ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Statistik Deskriptif</h3>
            <p class="text-xs text-slate-400 mb-4">Mean, median, std dev, dll — untuk kolom numerik</p>
            <?php if($numericColumns->isEmpty()): ?>
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.descriptive', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <select name="column" required class="<?php echo e($select); ?> mb-3">
                        <option value="">-- Pilih Kolom Numerik --</option>
                        <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Hitung</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Distribusi Frekuensi</h3>
            <p class="text-xs text-slate-400 mb-4">Frekuensi tiap kategori — untuk kolom kategorik</p>
            <?php if($categoricalColumns->isEmpty()): ?>
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom kategorik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.categorical', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <select name="column" required class="<?php echo e($select); ?> mb-3">
                        <option value="">-- Pilih Kolom Kategorik --</option>
                        <?php $__currentLoopData = $categoricalColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Hitung</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Scatter Plot</h3>
            <p class="text-xs text-slate-400 mb-4">Hubungan antara 2 kolom numerik</p>
            <?php if($numericColumns->count() < 2): ?>
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.scatter', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Sumbu X</label>
                            <select name="column_x" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Sumbu Y</label>
                            <select name="column_y" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Buat Scatter Plot</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji Normalitas</h3>
            <p class="text-xs text-slate-400 mb-4">Shapiro-Wilk (n&lt;50) / Kolmogorov-Smirnov (n≥50)</p>
            <?php if($numericColumns->isEmpty()): ?>
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.normality', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <select name="column" required class="<?php echo e($select); ?> mb-3">
                        <option value="">-- Pilih Kolom Numerik --</option>
                        <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Uji Normalitas</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji-t Satu Sampel</h3>
            <p class="text-xs text-slate-400 mb-4">Bandingkan rata-rata sampel dengan nilai tertentu</p>
            <?php if($numericColumns->isEmpty()): ?>
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.ttest_one_sample', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Kolom</label>
                            <select name="column" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Nilai Uji</label>
                            <input type="number" name="test_value" step="any" required placeholder="75" class="<?php echo e($select); ?>">
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Jalankan</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji-t Independen</h3>
            <p class="text-xs text-slate-400 mb-4">Bandingkan 2 kelompok berbeda (tepat 2 kategori)</p>
            <?php if($numericColumns->isEmpty() || $categoricalColumns->isEmpty()): ?>
                <p class="text-slate-400 text-sm">Butuh minimal 1 kolom numerik & 1 kategorik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.ttest_independent', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Nilai</label>
                            <select name="column_value" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Kelompok</label>
                            <select name="column_group" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $categoricalColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Jalankan</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji-t Berpasangan</h3>
            <p class="text-xs text-slate-400 mb-4">2 pengukuran pada subjek yang sama</p>
            <?php if($numericColumns->count() < 2): ?>
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.ttest_paired', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Sebelum</label>
                            <select name="column_1" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Sesudah</label>
                            <select name="column_2" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Jalankan</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">One-Way ANOVA</h3>
            <p class="text-xs text-slate-400 mb-4">Bandingkan rata-rata 3+ kelompok sekaligus</p>
            <?php if($numericColumns->isEmpty() || $categoricalColumns->isEmpty()): ?>
                <p class="text-slate-400 text-sm">Butuh minimal 1 kolom numerik & 1 kategorik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.anova', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Nilai</label>
                            <select name="column_value" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Kelompok</label>
                            <select name="column_group" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $categoricalColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Jalankan ANOVA</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Korelasi</h3>
            <p class="text-xs text-slate-400 mb-4">Pearson atau Spearman antara 2 kolom numerik</p>
            <?php if($numericColumns->count() < 2): ?>
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.correlation', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Kolom X</label>
                            <select name="column_x" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Kolom Y</label>
                            <select name="column_y" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-5 mb-3 text-sm">
                        <label class="flex items-center gap-1.5 text-slate-600">
                            <input type="radio" name="method" value="pearson" checked class="focus:ring-[#F6C90E]"> Pearson
                        </label>
                        <label class="flex items-center gap-1.5 text-slate-600">
                            <input type="radio" name="method" value="spearman" class="focus:ring-[#F6C90E]"> Spearman
                        </label>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Hitung Korelasi</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Chi-square Test</h3>
            <p class="text-xs text-slate-400 mb-4">Hubungan antara 2 variabel kategorik</p>
            <?php if($categoricalColumns->count() < 2): ?>
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom kategorik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.chi_square', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Variabel 1</label>
                            <select name="column_1" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $categoricalColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Variabel 2</label>
                            <select name="column_2" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $categoricalColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Jalankan</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Regresi Linear Sederhana</h3>
            <p class="text-xs text-slate-400 mb-4">1 variabel X memprediksi 1 variabel Y</p>
            <?php if($numericColumns->count() < 2): ?>
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.regression_simple', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="<?php echo e($label); ?>">Variabel X</label>
                            <select name="column_x" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="<?php echo e($label); ?>">Variabel Y</label>
                            <select name="column_y" required class="<?php echo e($select); ?>">
                                <option value="">--</option>
                                <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Jalankan</button>
                </form>
            <?php endif; ?>
        </div>

        
        <div class="<?php echo e($card); ?>">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Regresi Linear Berganda</h3>
            <p class="text-xs text-slate-400 mb-4">2+ variabel X memprediksi 1 variabel Y</p>
            <?php if($numericColumns->count() < 3): ?>
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 3 kolom numerik.</p>
            <?php else: ?>
                <form action="<?php echo e(route('analysis.regression_multiple', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="<?php echo e($label); ?>">Variabel Y</label>
                        <select name="column_y" required class="<?php echo e($select); ?>">
                            <option value="">--</option>
                            <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($col->column_name); ?>"><?php echo e($col->column_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="<?php echo e($label); ?>">Variabel X (pilih 2+)</label>
                        <div class="space-y-1.5 p-3 border border-slate-200 rounded-lg max-h-32 overflow-y-auto">
                            <?php $__currentLoopData = $numericColumns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="columns_x[]" value="<?php echo e($col->column_name); ?>" class="rounded focus:ring-[#F6C90E]">
                                    <?php echo e($col->column_name); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <button type="submit" class="<?php echo e($btn); ?>" style="background: #0F2C59;">Jalankan</button>
                </form>
            <?php endif; ?>
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