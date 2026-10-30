<x-app-layout>
    <x-slot name="header">Korelasi {{ $methodName }}</x-slot>

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
        <p class="text-xs text-slate-400 sw-mono mb-5">Metode: {{ $methodName }} · N = {{ $result['n'] }}</p>

        <div class="flex justify-center mb-6">
            <div class="text-center bg-slate-50 rounded-xl p-6">
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Koefisien Korelasi (r)</p>
                <p class="sw-mono text-5xl font-bold {{ $result['r'] >= 0 ? 'text-cyan-700' : 'text-red-600' }}">
                    {{ $result['r'] }}
                </p>
                <p class="text-sm text-slate-500 mt-2">
                    <span class="font-medium">{{ $result['direction'] }}</span> — {{ $result['strength'] }}
                </p>
            </div>
        </div>

        <table class="min-w-full divide-y divide-slate-100 text-sm mb-5">
            <tbody class="divide-y divide-slate-100">
            <tr><td class="py-2 font-medium text-slate-600 w-1/2">t hitung</td><td class="py-2 sw-mono text-slate-700">{{ $result['t'] }}</td></tr>
            <tr><td class="py-2 font-medium text-slate-600">df</td><td class="py-2 sw-mono text-slate-700">{{ $result['df'] }}</td></tr>
            <tr><td class="py-2 font-medium text-slate-600">p-value (2-tailed)</td><td class="py-2 sw-mono text-slate-700">{{ $result['p_value'] }}</td></tr>
            <tr>
                <td class="py-2 font-medium text-slate-600">Kesimpulan</td>
                <td class="py-2">
                    @if ($result['significant'])
                        <span class="px-2 py-1 text-xs bg-red-50 text-red-700 rounded-lg font-medium">✗ Tolak H₀</span>
                    @else
                        <span class="px-2 py-1 text-xs bg-emerald-50 text-emerald-700 rounded-lg font-medium">✓ Gagal Tolak H₀</span>
                    @endif
                </td>
            </tr>
            </tbody>
        </table>

        <div class="p-4 bg-cyan-50/60 text-cyan-900 rounded-lg text-sm mb-4">{{ $result['conclusion'] }}</div>

        <div class="p-3 bg-slate-50 rounded-lg text-xs text-slate-500 space-y-0.5">
            <p class="font-medium text-slate-600 mb-1">Pedoman kekuatan korelasi:</p>
            @foreach (['|r| 0.00–0.19 = Sangat Lemah', '|r| 0.20–0.39 = Lemah', '|r| 0.40–0.59 = Sedang', '|r| 0.60–0.79 = Kuat', '|r| 0.80–1.00 = Sangat Kuat'] as $g)
                <p>{{ $g }}</p>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-4">Scatter Plot</h3>
        <canvas id="correlationChart" height="100"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('correlationChart'), {
            type: 'scatter',
            data: {
                datasets: [{
                    data: @json($points),
                    backgroundColor: 'rgba(14, 116, 144, 0.6)',
                    borderColor: 'rgba(14, 116, 144, 1)',
                    pointRadius: 5,
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
