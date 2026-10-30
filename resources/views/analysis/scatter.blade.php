<x-app-layout>
    <x-slot name="header">Scatter Plot</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">{{ $columnX }} vs {{ $columnY }}</h3>
        <p class="text-xs text-slate-400 sw-mono">Jumlah pasangan data valid: {{ count($points) }}</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <canvas id="scatterChart" height="100"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('scatterChart'), {
            type: 'scatter',
            data: {
                datasets: [{
                    label: '{{ $columnX }} vs {{ $columnY }}',
                    data: @json($points),
                    backgroundColor: 'rgba(14, 116, 144, 0.6)',
                    borderColor: 'rgba(14, 116, 144, 1)',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { title: { display: true, text: '{{ $columnX }}' } },
                    y: { title: { display: true, text: '{{ $columnY }}' } },
                },
            },
        });
    </script>
</x-app-layout>
