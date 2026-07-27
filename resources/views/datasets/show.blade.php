<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $dataset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Navigasi Tab --}}
            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('datasets.index') }}" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Daftar Dataset
                    <div class="flex gap-2">
                        <a href="{{ route('analysis.history', $dataset->id) }}"
                           class="bg-gray-100 text-gray-700 px-4 py-2 rounded hover:bg-gray-200 text-sm">
                            Riwayat Analisis
                        </a>
                        <a href="{{ route('analysis.create', $dataset->id) }}"
                           class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                            Analisis Statistik
                        </a>
                    </div>
            </div>

            {{-- Tab --}}
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

            {{-- DATA VIEW --}}
            <div id="panel-data" class="bg-white shadow-sm rounded-b rounded-tr p-6">

                <div class="flex justify-between items-center mb-4">
                    <p class="text-sm text-gray-500">
                        Total baris: {{ $dataRows->total() }} | Halaman {{ $dataRows->currentPage() }} dari {{ $dataRows->lastPage() }}
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
                            @foreach ($columns as $col)
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 whitespace-nowrap">
                                    {{ $col->label ?: $col->column_name }}
                                    <span class="text-gray-300 font-normal ml-1">
                                            @if ($col->measure === 'scale') ⚖
                                        @elseif ($col->measure === 'ordinal') ↕
                                        @else ≡
                                        @endif
                                        </span>
                                </th>
                            @endforeach
                            <th class="px-3 py-2 w-12"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200" id="tableBody">
                        @foreach ($dataRows as $row)
                            <tr id="row-{{ $row->id }}">
                                <td class="px-3 py-1.5 text-gray-400 text-xs">{{ $row->row_number }}</td>
                                @foreach ($columns as $col)
                                    <td class="px-3 py-1.5"
                                        contenteditable="true"
                                        data-row-id="{{ $row->id }}"
                                        data-column="{{ $col->column_name }}"
                                        onblur="saveCell(this)"
                                        class="editable-cell focus:outline-none focus:bg-blue-50 cursor-text min-w-24">
                                        {{ $row->data[$col->column_name] ?? '' }}
                                    </td>
                                @endforeach
                                <td class="px-3 py-1.5 text-center">
                                    <button onclick="deleteRow({{ $row->id }})"
                                            class="text-red-400 hover:text-red-600 text-xs">✕</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $dataRows->links() }}
                </div>

                <p class="text-xs text-gray-400 mt-2">
                    Klik cell untuk mengedit langsung. Perubahan tersimpan otomatis saat pindah ke cell lain.
                </p>
            </div>

            {{-- VARIABLE VIEW --}}
            <div id="panel-variable" class="bg-white shadow-sm rounded-b rounded-tr p-6 hidden">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('datasets.update_variables', $dataset->id) }}" method="POST">
                    @csrf

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
                            @foreach ($columns as $i => $col)
                                <input type="hidden" name="columns[{{ $i }}][id]" value="{{ $col->id }}">
                                <tr>
                                    <td class="px-4 py-2 font-medium text-gray-700">{{ $col->column_name }}</td>
                                    <td class="px-4 py-2">
                                        <select name="columns[{{ $i }}][data_type]"
                                                class="border-gray-300 rounded shadow-sm text-sm w-full">
                                            <option value="numeric" {{ $col->data_type === 'numeric' ? 'selected' : '' }}>Numeric</option>
                                            <option value="categorical" {{ $col->data_type === 'categorical' ? 'selected' : '' }}>String</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="text" name="columns[{{ $i }}][label]"
                                               value="{{ $col->label }}"
                                               placeholder="Label opsional"
                                               class="border-gray-300 rounded shadow-sm text-sm w-full">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="columns[{{ $i }}][decimals]"
                                               value="{{ $col->decimals ?? 2 }}"
                                               min="0" max="10"
                                               class="border-gray-300 rounded shadow-sm text-sm w-16">
                                    </td>
                                    <td class="px-4 py-2">
                                        <select name="columns[{{ $i }}][measure]"
                                                class="border-gray-300 rounded shadow-sm text-sm w-full">
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

            fetch('{{ route("datasets.update_cell", $dataset->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
            fetch('{{ route("datasets.add_row", $dataset->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({}),
            })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return;
                    const tbody = document.getElementById('tableBody');
                    const columns = @json($columns->pluck('column_name'));
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

            fetch('{{ route("datasets.delete_row", $dataset->id) }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
</x-app-layout>
