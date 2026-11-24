<x-app-layout>
    <x-slot name="header">{{ $dataset->name }}</x-slot>

    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-wrap gap-2 justify-between items-center mb-4">
        <a href="{{ route('datasets.index') }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Daftar Dataset
        </a>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('export.dataset_csv', $dataset->id) }}"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Export CSV
            </a>
            <a href="{{ route('analysis.history', $dataset->id) }}"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Riwayat Analisis
            </a>
            <a href="{{ route('analysis.create', $dataset->id) }}"
               class="bg-sw-navy hover:bg-sw-navy-light text-white px-3 py-1.5 text-xs font-medium rounded-lg transition shadow-sm">
                Analisis Statistik
            </a>
        </div>
    </div>

    {{-- Tab --}}
    <div class="flex gap-0">
        <button onclick="showTab('data')" id="tab-data"
                class="tab-btn px-5 py-2 text-sm font-semibold border-b-2 rounded-t-lg transition border-sw-gold text-sw-navy bg-white">
            Data View
        </button>
        <button onclick="showTab('variable')" id="tab-variable"
                class="tab-btn px-5 py-2 text-sm font-medium border-b-2 border-transparent text-slate-400 bg-slate-50 rounded-t-lg transition hover:text-slate-600">
            Variable View
        </button>
    </div>

    {{-- DATA VIEW --}}
    <div id="panel-data" class="bg-white border border-slate-200 rounded-b-xl rounded-tr-xl p-5 shadow-sm">

        <div class="flex justify-between items-center mb-4">
            <p class="text-xs text-slate-400 font-mono">
                {{ $dataRows->total() }} baris · Hal {{ $dataRows->currentPage() }}/{{ $dataRows->lastPage() }}
            </p>
            <button onclick="addRow()"
                    class="bg-sw-navy hover:bg-sw-navy-light text-white px-3 py-1.5 text-xs font-medium rounded-lg transition shadow-sm">
                + Tambah Baris
            </button>
        </div>

        <div class="overflow-x-auto border border-slate-100 rounded-lg max-h-[600px] overflow-y-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm" id="dataTable">
                <thead class="sticky top-0 bg-slate-50 z-10 shadow-sm">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-slate-400 w-12 bg-slate-50">#</th>
                    @foreach ($columns as $col)
                        <th class="px-3 py-2 text-left text-xs font-medium text-slate-500 whitespace-nowrap">
                            {{ $col->label ?: $col->column_name }}
                            <span class="text-slate-300 font-normal ml-1">
                                    @if ($col->measure === 'scale') ⚖
                                @elseif ($col->measure === 'ordinal') ↕
                                @else ≡
                                @endif
                                </span>
                        </th>
                    @endforeach
                    <th class="px-3 py-2 w-10"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100" id="tableBody">
                @foreach ($dataRows as $row)
                    <tr id="row-{{ $row->id }}" class="hover:bg-slate-50/60">
                        <td class="px-3 py-1.5 text-slate-300 text-xs sw-mono">{{ $row->row_number }}</td>
                        @foreach ($columns as $col)
                            <td class="px-3 py-1.5 sw-mono text-slate-700"
                                contenteditable="true"
                                data-row-id="{{ $row->id }}"
                                data-column="{{ $col->column_name }}"
                                onblur="saveCell(this)"
                                class="editable-cell cursor-text min-w-24 rounded">
                                {{ $row->data[$col->column_name] ?? '' }}
                            </td>
                        @endforeach
                        <td class="px-3 py-1.5 text-center">
                            <button onclick="deleteRow({{ $row->id }})"
                                    class="text-slate-300 hover:text-red-500 text-xs transition">✕</button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $dataRows->links() }}</div>

        <p class="text-xs text-slate-400 mt-3">
            Klik cell untuk mengedit langsung. Perubahan tersimpan otomatis saat pindah ke cell lain.
        </p>
    </div>

    {{-- VARIABLE VIEW --}}
    <div id="panel-variable" class="bg-white border border-slate-200 rounded-b-xl rounded-tr-xl p-5 hidden">
        <form action="{{ route('datasets.update_variables', $dataset->id) }}" method="POST">
            @csrf
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
                    @foreach ($columns as $i => $col)
                        <input type="hidden" name="columns[{{ $i }}][id]" value="{{ $col->id }}">
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-2 font-medium text-slate-700 sw-mono text-xs">{{ $col->column_name }}</td>
                            <td class="px-4 py-2">
                                <select name="columns[{{ $i }}][data_type]" class="border-slate-300 rounded-lg text-xs w-full focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                                    <option value="numeric" {{ $col->data_type === 'numeric' ? 'selected' : '' }}>Numeric</option>
                                    <option value="categorical" {{ $col->data_type === 'categorical' ? 'selected' : '' }}>String</option>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="columns[{{ $i }}][label]" value="{{ $col->label }}"
                                       placeholder="Label opsional"
                                       class="border-slate-300 rounded-lg text-xs w-full focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="columns[{{ $i }}][decimals]" value="{{ $col->decimals ?? 2 }}"
                                       min="0" max="10"
                                       class="border-slate-300 rounded-lg text-xs w-16 focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                            </td>
                            <td class="px-4 py-2">
                                <select name="columns[{{ $i }}][measure]" class="border-slate-300 rounded-lg text-xs w-full focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                                    <option value="nominal" {{ ($col->measure ?? 'nominal') === 'nominal' ? 'selected' : '' }}>Nominal</option>
                                    <option value="ordinal" {{ ($col->measure ?? '') === 'ordinal' ? 'selected' : '' }}>Ordinal</option>
                                    <option value="scale" {{ ($col->measure ?? '') === 'scale' ? 'selected' : '' }}>Scale</option>
                                </select>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-sw-navy hover:bg-sw-navy-light text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">
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
                ? 'border-color: #F6C90E; color: #162032; background: white; border-bottom-width: 2px;'
                : 'border-color: transparent; color: #94a3b8; background: #f8fafc; border-bottom-width: 2px;';
            document.getElementById('tab-variable').style.cssText = tab === 'variable'
                ? 'border-color: #F6C90E; color: #162032; background: white; border-bottom-width: 2px;'
                : 'border-color: transparent; color: #94a3b8; background: #f8fafc; border-bottom-width: 2px;';
        }

        function saveCell(cell) {
            const rowId = cell.dataset.rowId;
            const columnName = cell.dataset.column;
            const value = cell.innerText.trim();
            cell.style.backgroundColor = '#fefce8';
            fetch('{{ route("datasets.update_cell", $dataset->id) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
            fetch('{{ route("datasets.add_row", $dataset->id) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({}),
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;
                    const tbody = document.getElementById('tableBody');
                    const columns = @json($columns->pluck('column_name'));

                    const tr = document.createElement('tr');
                    tr.id = `row-${data.row_id}`;
                    tr.className = 'hover:bg-slate-50/60';

                    // Nomor baris
                    const numTd = document.createElement('td');
                    numTd.className = 'px-3 py-1.5 text-slate-300 text-xs font-mono';
                    numTd.textContent = data.row_number;
                    tr.appendChild(numTd);

                    // Sel kolom data (aman dari XSS melalui atribut dataset)
                    columns.forEach(col => {
                        const td = document.createElement('td');
                        td.className = 'px-3 py-1.5 font-mono text-slate-700 cursor-text min-w-24 rounded';
                        td.contentEditable = 'true';
                        td.dataset.rowId = data.row_id;
                        td.dataset.column = col;
                        td.onblur = function() { saveCell(this); };
                        tr.appendChild(td);
                    });

                    // Tombol hapus baris
                    const actionTd = document.createElement('td');
                    actionTd.className = 'px-3 py-1.5 text-center';
                    const delBtn = document.createElement('button');
                    delBtn.className = 'text-slate-300 hover:text-red-500 text-xs transition';
                    delBtn.textContent = '✕';
                    delBtn.onclick = function() { deleteRow(data.row_id); };
                    actionTd.appendChild(delBtn);
                    tr.appendChild(actionTd);

                    tbody.appendChild(tr);
                });
        }

        function deleteRow(rowId) {
            if (!confirm('Yakin ingin menghapus baris ini?')) return;
            fetch('{{ route("datasets.delete_row", $dataset->id) }}', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ row_id: rowId }),
            })
                .then(res => res.json())
                .then(data => { if (data.success) document.getElementById(`row-${rowId}`)?.remove(); });
        }
    </script>
</x-app-layout>
