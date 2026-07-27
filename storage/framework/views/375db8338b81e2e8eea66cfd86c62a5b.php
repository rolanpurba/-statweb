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
            <?php echo e($dataset->name); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <?php if(session('success')): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            
            <div class="mb-4 flex justify-between items-center">
                <a href="<?php echo e(route('datasets.index')); ?>" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Daftar Dataset
                    <div class="flex gap-2">
                        <a href="<?php echo e(route('analysis.history', $dataset->id)); ?>"
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 text-sm">
                            Riwayat Analisis
                        </a>
                        <a href="<?php echo e(route('analysis.create', $dataset->id)); ?>"
                           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                            Analisis Statistik
                        </a>
                    </div>
            </div>

            
            <div class="flex gap-0 mb-0">
                <button onclick="showTab('data')" id="tab-data"
                        class="tab-btn px-6 py-2 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600 bg-white rounded-tl">
                    Data View
                </button>
                <button onclick="showTab('variable')" id="tab-variable"
                        class="tab-btn px-6 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 bg-gray-50 rounded-tr">
                    Variable View
                </button>
            </div>

            
            <div id="panel-data" class="bg-white shadow-sm rounded-b rounded-tr p-6">

                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-500">
                        Total baris: <?php echo e($dataRows->total()); ?> | Halaman <?php echo e($dataRows->currentPage()); ?> dari <?php echo e($dataRows->lastPage()); ?>

                    </p>
                    <button onclick="addRow()"
                            class="bg-indigo-600 text-white px-3 py-1.5 text-sm rounded hover:bg-indigo-700">
                        + Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm" id="dataTable">
                        <thead>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 w-12">#</th>
                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 whitespace-nowrap">
                                    <?php echo e($col->label ?: $col->column_name); ?>

                                    <span class="text-gray-300 font-normal ml-1">
                                            <?php if($col->measure === 'scale'): ?> ⚖
                                        <?php elseif($col->measure === 'ordinal'): ?> ↕
                                        <?php else: ?> ≡
                                        <?php endif; ?>
                                        </span>
                                </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <th class="px-3 py-2 w-12"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="tableBody">
                        <?php $__currentLoopData = $dataRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr id="row-<?php echo e($row->id); ?>">
                                <td class="px-3 py-1.5 text-gray-400 text-xs"><?php echo e($row->row_number); ?></td>
                                <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="px-3 py-1.5"
                                        contenteditable="true"
                                        data-row-id="<?php echo e($row->id); ?>"
                                        data-column="<?php echo e($col->column_name); ?>"
                                        onblur="saveCell(this)"
                                        class="editable-cell focus:outline-none focus:bg-blue-50 cursor-text min-w-24">
                                        <?php echo e($row->data[$col->column_name] ?? ''); ?>

                                    </td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <td class="px-3 py-1.5 text-center">
                                    <button onclick="deleteRow(<?php echo e($row->id); ?>)"
                                            class="text-red-400 hover:text-red-600 text-xs">✕</button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                
                <div class="mt-4">
                    <?php echo e($dataRows->links()); ?>

                </div>

                <p class="text-xs text-gray-400 mt-2">
                    Klik cell untuk mengedit langsung. Perubahan tersimpan otomatis saat pindah ke cell lain.
                </p>
            </div>

            
            <div id="panel-variable" class="bg-white shadow-sm rounded-b rounded-tr p-6 hidden">

                <?php if(session('success')): ?>
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('datasets.update_variables', $dataset->id)); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Label</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Decimals</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Measure</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="columns[<?php echo e($i); ?>][id]" value="<?php echo e($col->id); ?>">
                                <tr>
                                    <td class="px-4 py-2 font-medium text-gray-700"><?php echo e($col->column_name); ?></td>
                                    <td class="px-4 py-2">
                                        <select name="columns[<?php echo e($i); ?>][data_type]"
                                                class="border-gray-300 rounded shadow-sm text-sm w-full">
                                            <option value="numeric" <?php echo e($col->data_type === 'numeric' ? 'selected' : ''); ?>>Numeric</option>
                                            <option value="categorical" <?php echo e($col->data_type === 'categorical' ? 'selected' : ''); ?>>String</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" name="columns[<?php echo e($i); ?>][label]"
                                               value="<?php echo e($col->label); ?>"
                                               placeholder="Label opsional"
                                               class="border-gray-300 rounded shadow-sm text-sm w-full">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="columns[<?php echo e($i); ?>][decimals]"
                                               value="<?php echo e($col->decimals ?? 2); ?>"
                                               min="0" max="10"
                                               class="border-gray-300 rounded shadow-sm text-sm w-16">
                                    </td>
                                    <td class="px-4 py-2">
                                        <select name="columns[<?php echo e($i); ?>][measure]"
                                                class="border-gray-300 rounded shadow-sm text-sm w-full">
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
                        <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Tab switching
        function showTab(tab) {
            document.getElementById('panel-data').classList.toggle('hidden', tab !== 'data');
            document.getElementById('panel-variable').classList.toggle('hidden', tab !== 'variable');
            document.getElementById('tab-data').className = tab === 'data'
                ? 'tab-btn px-6 py-2 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600 bg-white rounded-tl'
                : 'tab-btn px-6 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 bg-gray-50 rounded-tl';
            document.getElementById('tab-variable').className = tab === 'variable'
                ? 'tab-btn px-6 py-2 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600 bg-white rounded-tr'
                : 'tab-btn px-6 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 bg-gray-50 rounded-tr';
        }

        // Save cell via AJAX
        function saveCell(cell) {
            const rowId = cell.dataset.rowId;
            const columnName = cell.dataset.column;
            const value = cell.innerText.trim();

            cell.style.backgroundColor = '#fef9c3'; // kuning sementara (saving)

            fetch('<?php echo e(route("datasets.update_cell", $dataset->id)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: JSON.stringify({ row_id: rowId, column_name: columnName, value: value }),
            })
                .then(res => res.json())
                .then(data => {
                    cell.style.backgroundColor = data.success ? '#f0fdf4' : '#fef2f2';
                    setTimeout(() => cell.style.backgroundColor = '', 1000);
                })
                .catch(() => { cell.style.backgroundColor = '#fef2f2'; });
        }

        // Add row via AJAX
        function addRow() {
            fetch('<?php echo e(route("datasets.add_row", $dataset->id)); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: JSON.stringify({}),
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;
                    const tbody = document.getElementById('tableBody');
                    const columns = <?php echo json_encode($columns->pluck('column_name'), 15, 512) ?>;
                    let cells = `<td class="px-3 py-1.5 text-gray-400 text-xs">${data.row_number}</td>`;
                    columns.forEach(col => {
                        cells += `<td class="px-3 py-1.5 editable-cell focus:outline-none focus:bg-blue-50 cursor-text min-w-24"
                        contenteditable="true"
                        data-row-id="${data.row_id}"
                        data-column="${col}"
                        onblur="saveCell(this)"></td>`;
                    });
                    cells += `<td class="px-3 py-1.5 text-center">
                    <button onclick="deleteRow(${data.row_id})" class="text-red-400 hover:text-red-600 text-xs">✕</button>
                </td>`;
                    const tr = document.createElement('tr');
                    tr.id = `row-${data.row_id}`;
                    tr.innerHTML = cells;
                    tbody.appendChild(tr);
                });
        }

        // Delete row via AJAX
        function deleteRow(rowId) {
            if (!confirm('Yakin ingin menghapus baris ini?')) return;

            fetch('<?php echo e(route("datasets.delete_row", $dataset->id)); ?>', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                },
                body: JSON.stringify({ row_id: rowId }),
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`row-${rowId}`)?.remove();
                    }
                });
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