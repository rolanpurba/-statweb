<x-app-layout>
    <x-slot name="header">Statistik Deskriptif</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">{{ $columnName }}</h3>
        <p class="text-xs text-slate-400 sw-mono mb-5">N = {{ $stats['n'] }}</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-2">
            @foreach ([
                'mean' => 'Mean', 'median' => 'Median', 'mode' => 'Modus', 'std_dev' => 'Std Dev',
                'variance' => 'Variansi', 'min' => 'Min', 'max' => 'Max', 'range' => 'Range',
                'q1' => 'Q1', 'q3' => 'Q3',
            ] as $key => $lbl)
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $lbl }}</p>
                    <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">
                        {{ is_numeric($stats[$key]) ? number_format($stats[$key], 4) : $stats[$key] }}
                    </p>
                </div>
            @endforeach
        </div>

        <p class="text-xs text-slate-400 mt-4">
            Standar deviasi & variansi memakai rumus sampel (n-1), konsisten dengan STDEV.S / VAR.S di Excel.
            Kuartil memakai interpolasi linear, konsisten dengan QUARTILE.INC di Excel.
        </p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-slate-800 sw-display">Histogram</h3>
            @if ($latestResult)
                <button onclick="exportPDF()" id="exportBtn"
                        class="text-white px-3 py-1.5 text-xs font-medium rounded-lg transition flex items-center gap-2"
                        style="background: #0F2C59;">
                    <span id="exportLabel">Export PDF</span>
                </button>
            @endif
        </div>
        <canvas id="histogramChart" height="100"></canvas>
    </div>

    @if ($latestResult)
        <form id="exportForm" action="{{ route('export.analysis_pdf', [$dataset->id, $latestResult->id]) }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="chart_image" id="chartImage">
        </form>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chart = new Chart(document.getElementById('histogramChart'), {
            type: 'bar',
            data: {
                labels: @json($histogram['labels']),
                datasets: [{
                    label: 'Frekuensi',
                    data: @json($histogram['values']),
                    backgroundColor: 'rgba(15, 44, 89, 0.6)',
                    borderColor: 'rgba(15, 44, 89, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                animation: { duration: 500 },
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Frekuensi' }, ticks: { stepSize: 1 } },
                    x: { title: { display: true, text: '{{ $columnName }}' } },
                },
            },
        });

        function exportPDF() {
            const btn = document.getElementById('exportBtn');
            const label = document.getElementById('exportLabel');
            btn.disabled = true;
            label.textContent = 'Menyiapkan...';

            setTimeout(() => {
                const canvas = document.getElementById('histogramChart');
                const base64 = canvas.toDataURL('image/png', 1.0);
                document.getElementById('chartImage').value = base64;
                document.getElementById('exportForm').submit();

                setTimeout(() => {
                    btn.disabled = false;
                    label.textContent = 'Export PDF';
                }, 2000);
            }, 600);
        }
    </script>
</x-app-layout>
