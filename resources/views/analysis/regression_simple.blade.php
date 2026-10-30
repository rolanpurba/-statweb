<x-app-layout>
    <x-slot name="header">Regresi Linear Sederhana</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">{{ $columnX }} → {{ $columnY }}</h3>
        <p class="text-xs text-slate-400 sw-mono mb-5">N = {{ $result['n'] }}</p>

        <div class="p-4 bg-[#0F172A] rounded-xl text-center mb-5">
            <p class="text-xs text-slate-400 mb-1">Persamaan Regresi</p>
            <p class="sw-mono text-lg font-bold text-cyan-300">{{ $result['equation'] }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">R²</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['r2'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">R² Adj</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['r2_adj'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">F hitung</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['f'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">p-value</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['f_pvalue'] }}</p>
            </div>
        </div>

        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Koefisien</h4>
        <div class="overflow-x-auto mb-5">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                <tr class="bg-slate-50">
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Variabel</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Koefisien</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Std Error</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">t</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">p-value</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-2 font-medium text-slate-700">(Intercept)</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['b0'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['se_b0'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['t_b0'] }}</td>
                    <td class="px-4 py-2 sw-mono {{ $result['p_b0'] < 0.05 ? 'text-red-600 font-medium' : 'text-slate-600' }}">{{ $result['p_b0'] }}</td>
                </tr>
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-2 font-medium text-slate-700">{{ $columnX }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['b1'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['se_b1'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['t_b1'] }}</td>
                    <td class="px-4 py-2 sw-mono {{ $result['p_b1'] < 0.05 ? 'text-red-600 font-medium' : 'text-slate-600' }}">{{ $result['p_b1'] }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="p-4 bg-cyan-50/60 text-cyan-900 rounded-lg text-sm mb-4">
            <strong>R² = {{ $result['r2'] }}</strong> — model menjelaskan {{ round($result['r2'] * 100, 1) }}% variasi pada {{ $columnY }}
        </div>

        @if ($result['significant'])
            <span class="px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded-lg font-medium">✗ {{ $result['conclusion'] }}</span>
        @else
            <span class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 rounded-lg font-medium">✓ {{ $result['conclusion'] }}</span>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-4">Scatter Plot & Garis Regresi</h3>
        <canvas id="regressionChart" height="100"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('regressionChart'), {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Data',
                        data: @json($points),
                        backgroundColor: 'rgba(14, 116, 144, 0.6)',
                        pointRadius: 5,
                        type: 'scatter',
                    },
                    {
                        label: 'Garis Regresi',
                        data: @json($regressionLine),
                        type: 'line',
                        borderColor: 'rgba(220, 38, 38, 0.9)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: '{{ $columnX }}' } },
                    y: { title: { display: true, text: '{{ $columnY }}' } },
                },
            },
        });
    </script>
</x-app-layout>
