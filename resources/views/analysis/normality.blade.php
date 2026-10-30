<x-app-layout>
    <x-slot name="header">Uji Normalitas</x-slot>

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
        <p class="text-xs text-slate-400 sw-mono mb-5">N = {{ $n }} · Metode: {{ $testName }}</p>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-4">
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">Statistik</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['statistic'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">p-value</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['p_value'] }}</p>
            </div>
            <div class="col-span-2 sm:col-span-1 flex items-center">
                @if ($result['normal'])
                    <span class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 rounded-lg font-medium">✓ Normal</span>
                @else
                    <span class="px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded-lg font-medium">✗ Tidak Normal</span>
                @endif
            </div>
        </div>

        <div class="p-4 bg-cyan-50/60 text-cyan-900 rounded-lg text-sm">
            <strong>Interpretasi:</strong>
            @if ($result['normal'])
                Data berdistribusi normal. Kamu dapat melanjutkan dengan uji parametrik seperti Uji-t atau ANOVA.
            @else
                Data tidak berdistribusi normal. Pertimbangkan menggunakan uji nonparametrik sebagai alternatif.
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Q-Q Plot</h3>
        <p class="text-xs text-slate-400 mb-4">Titik mendekati garis diagonal = data mendekati distribusi normal</p>
        <canvas id="qqChart" height="100"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const points = @json($qqPoints);
        const xVals = points.map(p => p.x);
        const yVals = points.map(p => p.y);
        const minVal = Math.min(...xVals, ...yVals);
        const maxVal = Math.max(...xVals, ...yVals);

        new Chart(document.getElementById('qqChart'), {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Data',
                        data: points,
                        backgroundColor: 'rgba(14, 116, 144, 0.7)',
                        pointRadius: 5,
                    },
                    {
                        label: 'Garis Normal',
                        data: [{ x: minVal, y: minVal }, { x: maxVal, y: maxVal }],
                        type: 'line',
                        borderColor: 'rgba(220, 38, 38, 0.8)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Kuantil Teoritis (Normal)' } },
                    y: { title: { display: true, text: 'Kuantil Empiris (Data)' } },
                },
            },
        });
    </script>
</x-app-layout>
