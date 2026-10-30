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
     <?php $__env->slot('header', null, []); ?> <?php echo e($dataset->name); ?> <?php $__env->endSlot(); ?>

    <?php if(session('success')): ?>
        <div class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="flex flex-wrap gap-2 justify-between items-center mb-4">
        <a href="<?php echo e(route('datasets.index')); ?>" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Daftar Dataset
        </a>
        <div class="flex gap-2 flex-wrap">
            <a href="<?php echo e(route('export.dataset_csv', $dataset->id)); ?>"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Export CSV
            </a>
            <a href="<?php echo e(route('analysis.history', $dataset->id)); ?>"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Riwayat Analisis
            </a>
            <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
               class="text-white px-3 py-1.5 text-xs font-medium rounded-lg transition" style="background: #0F2C59;">
                Analisis Statistik
            </a>
        </div>
    </div>

    
    <div class="flex gap-0">
        <button onclick="showTab('data')" id="tab-data"
                class="tab-btn px-5 py-2 text-sm font-medium border-b-2 rounded-t-lg transition"
                style="border-color: #F6C90E; color: #0F2C59; background: white;">
            Data View
        </button>
        <button onclick="showTab('variable')" id="tab-variable"
                class="tab-btn px-5 py-2 text-sm font-medium border-b-2 border-transparent text-slate-400 bg-slate-50 rounded-t-lg transition">
            Variable View
        </button>
    </div>

    
    <div id="panel-data" class="bg-white border border-slate-200 rounded-b-xl rounded-tr-xl p-5">

        <div class="flex justify-between items-center mb-4">
            <p class="text-xs text-slate-400 sw-mono">
                <?php echo e($dataRows->total()); ?> baris · Hal <?php echo e($dataRows->currentPage()); ?>/<?php echo e($dataRows->lastPage()); ?>

            </p>
            <button onclick="addRow()"
                    class="text-white px-3 py-1.5 text-xs font-medium rounded-lg transition" style="background: #0F2C59;">
                + Tambah Baris
            </button>
        </div>

        <div class="overflow-x-auto border border-slate-100 rounded-lg">
            <table class="min-w-full divide-y divide-slate-100 text-sm" id="dataTable">
                <thead>
                <tr class="bg-slate-50">
                    <th class="px-3 py-2 text-left text-xs font-medium text-slate-400 w-12">#</th>
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 whitespace-nowrap">
                            <?php echo e($col->label ?: $col->column_name); ?>

                            <span class="text-slate-300 font-normal ml-1">
                                    <?php if($col->measure === 'scale'): ?> ⚖
                                <?php elseif($col->measure === 'ordinal'): ?> ↕
                                <?php else: ?> ≡
                                <?php endif; ?>
                                </span>
                        </th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <th class="px-3 py-2 w-10"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="tableBody">
                <?php $__currentLoopData = $dataRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr id="row-<?php echo e($row->id); ?>" class="hover:bg-slate-50/60">
                        <td class="px-3 py-1.5 text-slate-300 text-xs sw-mono"><?php echo e($row->row_number); ?></td>
                        <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <td class="px-3 py-1.5 sw-mono text-slate-700"
                                contenteditable="true"
                                data-row-id="<?php echo e($row->id); ?>"
                                data-column="<?php echo e($col->column_name); ?>"
                                onblur="saveCell(this)"
                                class="editable-cell cursor-text min-w-24 rounded">
                                <?php echo e($row->data[$col->column_name] ?? ''); ?>

                            </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <td class="px-3 py-1.5 text-center">
                            <button onclick="deleteRow(<?php echo e($row->id); ?>)"
                                    class="text-slate-300 hover:text-red-500 text-xs transition">✕</button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4"><?php echo e($dataRows->links()); ?></div>

        <p class="text-xs text-slate-400 mt-3">
            Klik cell untuk mengedit langsung. Perubahan tersimpan otomatis saat pindah ke cell lain.
        </p>
    </div>

    
    <div id="panel-variable" class="bg-white border border-slate-200 rounded-b-xl rounded-tr-xl p-5 hidden">
        <form action="<?php echo e(route('datasets.update_variables', $dataset->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="overflow-x-auto border border-slate-100 rounded-lg">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead>
                    <tr class="bg-slate-50">
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Type</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Label</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Decimals</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-slate-500">Measure</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <input type="hidden" name="columns[<?php echo e($i); ?>][id]" value="<?php echo e($col->id); ?>">
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-2 font-medium text-slate-700 sw-mono text-xs"><?php echo e($col->column_name); ?></td>
                            <td class="px-4 py-2">
                                <select name="columns[<?php echo e($i); ?>][data_type]" class="border-slate-300 rounded-lg text-xs w-full focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                                    <option value="numeric" <?php echo e($col->data_type === 'numeric' ? 'selected' : ''); ?>>Numeric</option>
                                    <option value="categorical" <?php echo e($col->data_type === 'categorical' ? 'selected' : ''); ?>>String</option>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="columns[<?php echo e($i); ?>][label]" value="<?php echo e($col->label); ?>"
                                       placeholder="Label opsional"
                                       class="border-slate-300 rounded-lg text-xs w-full focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="columns[<?php echo e($i); ?>][decimals]" value="<?php echo e($col->decimals ?? 2); ?>"
                                       min="0" max="10"
                                       class="border-slate-300 rounded-lg text-xs w-16 focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                            </td>
                            <td class="px-4 py-2">
                                <select name="columns[<?php echo e($i); ?>][measure]" class="border-slate-300 rounded-lg text-xs w-full focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                                    <option value="nominal" <?php echo e(($col->measure ?? 'nominal') === 'nominal' ? 'selected' : ''); ?>>Nominal</option>
                                    <option value="ordinal" <?php echo e(($col->measure ?? '') === 'ordinal' ? 'selected' : ''); ?>>Ordinal</option>
                                    <option value="scale" <?php echo e(($col->measure ?? '') === 'scale' ? 'selected' : ''); ?>>Scale</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <button type="submit" class="text-white px-4 py-2 rounded-lg text-sm font-medium transition" style="background: #0F2C59;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script>
        function showTab(tab) {
            document.getElementById('panel-data').classList.toggle('hidden', tab !== 'data');
            document.getElementById('panel-variable').classList.toggle('hidden', tab !== 'variable');
            document.getElementById('tab-data').style.cssText = tab === 'data'
                ? 'border-color: #F6C90E; color: #0F2C59; background: white; border-bottom-width: 2px;'
                : 'border-color: transparent; color: #94a3b8; background: #f8fafc; border-bottom-width: 2px;';
            document.getElementById('tab-variable').style.cssText = tab === 'variable'
                ? 'border-color: #F6C90E; color: #0F2C59; background: white; border-bottom-width: 2px;'
                : 'border-color: transparent; color: #94a3b8; background: #f8fafc; border-bottom-width: 2px;';
        }

        function saveCell(cell) {
            const rowId = cell.dataset.rowId;
            const columnName = cell.dataset.column;
            const value = cell.innerText.trim();
            cell.style.backgroundColor = '#fefce8';
            fetch('<?php echo e(route("datasets.update_cell", $dataset->id)); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                body: JSON.stringify({ row_id: rowId, column_name: columnName, value: value }),
            })
                .then(res => res.json())
                .then(data => {
                    cell.style.backgroundColor = data.success ? '#f0fdf4' : '#fef2f2';
                    setTimeout(() => cell.style.backgroundColor = '', 800);
                })
                .catch(() => { cell.style.backgroundColor = '#fef2f2'; });
        }

        function addRow() {
            fetch('<?php echo e(route("datasets.add_row", $dataset->id)); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                body: JSON.stringify({}),
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;
                    const tbody = document.getElementById('tableBody');
                    const columns = <?php echo json_encode($columns->pluck('column_name'), 15, 512) ?>;
                    let cells = `<td class="px-3 py-1.5 text-slate-300 text-xs sw-mono">${data.row_number}</td>`;
                    columns.forEach(col => {
                        cells += `<td class="px-3 py-1.5 sw-mono text-slate-700 cursor-text min-w-24 rounded"
                        contenteditable="true" data-row-id="${data.row_id}" data-column="${col}"
                        onblur="saveCell(this)"></td>`;
                    });
                    cells += `<td class="px-3 py-1.5 text-center">
                    <button onclick="deleteRow(${data.row_id})" class="text-slate-300 hover:text-red-500 text-xs transition">✕</button>
                </td>`;
                    const tr = document.createElement('tr');
                    tr.id = `row-${data.row_id}`;
                    tr.className = 'hover:bg-slate-50/60';
                    tr.innerHTML = cells;
                    tbody.appendChild(tr);
                });
        }

        function deleteRow(rowId) {
            if (!confirm('Yakin ingin menghapus baris ini?')) return;
            fetch('<?php echo e(route("datasets.delete_row", $dataset->id)); ?>', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
                body: JSON.stringify({ row_id: rowId }),
            })
                .then(res => res.json())
                .then(data => { if (data.success) document.getElementById(`row-${rowId}`)?.remove(); });
        }
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
<?php /**PATH C:\Users\LOQ\Herd\statweb\resources\views/datasets/show.blade.php ENDPATH**/ ?>