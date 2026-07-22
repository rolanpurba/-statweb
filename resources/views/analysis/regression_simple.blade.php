<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Regresi Linear Sederhana — {{ $dataset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('analysis.create', $dataset->id) }}" class="text-indigo-600 hover:underline">
                    &larr; Pilih Analisis Lain
                </a>
                <a href="{{ route('datasets.show', $dataset->id) }}" class="text-gray-500 hover:underline text-sm">
                    Kembali ke Dataset
                </a>
            </div>

            {{-- Persamaan Regresi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">{{ $columnX }} → {{ $columnY }}</h3>
                <p class="text-sm text-gray-500 mb-4">N = {{ $result['n'] }}</p>

                <div class="p-4 bg-indigo-50 text-indigo-800 rounded-lg text-center mb-6">
                    <p class="text-xs text-indigo-500 mb-1">Persamaan Regresi</p>
                    <p class="text-xl font-bold font-mono">{{ $result['equation'] }}</p>
                </div>

                {{-- Model Summary --}}
                <h4 class="font-medium text-gray-700 mb-2">Model Summary</h4>
                <table class="min-w-full divide-y divide-gray-200 mb-6">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700 w-1/2">R² (R-squared)</td>
                        <td class="px-4 py-2">{{ $result['r2'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">R² Adjusted</td>
                        <td class="px-4 py-2">{{ $result['r2_adj'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Standard Error</td>
                        <td class="px-4 py-2">{{ $result['se'] }}</td>
                    </tr>
                    </tbody>
                </table>

                {{-- ANOVA Table --}}
                <h4 class="font-medium text-gray-700 mb-2">ANOVA</h4>
                <table class="min-w-full divide-y divide-gray-200 mb-6 text-sm">
                    <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Sumber</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">df</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">F</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">p-value</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2">Regresi</td>
                        <td class="px-4 py-2">{{ $result['df_reg'] }}</td>
                        <td class="px-4 py-2 font-medium">{{ $result['f'] }}</td>
                        <td class="px-4 py-2 font-medium">{{ $result['f_pvalue'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2">Residual</td>
                        <td class="px-4 py-2">{{ $result['df_res'] }}</td>
                        <td class="px-4 py-2">—</td>
                        <td class="px-4 py-2">—</td>
                    </tr>
                    </tbody>
                </table>

                {{-- Koefisien --}}
                <h4 class="font-medium text-gray-700 mb-2">Koefisien</h4>
                <table class="min-w-full divide-y divide-gray-200 mb-4 text-sm">
                    <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Variabel</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Koefisien</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">Std Error</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">t</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-500">p-value</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium">(Intercept)</td>
                        <td class="px-4 py-2">{{ $result['b0'] }}</td>
                        <td class="px-4 py-2">{{ $result['se_b0'] }}</td>
                        <td class="px-4 py-2">{{ $result['t_b0'] }}</td>
                        <td class="px-4 py-2">
                            {{ $result['p_b0'] }}
                            @if ($result['p_b0'] < 0.05)
                                <span class="text-xs text-red-600 ml-1">*</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $columnX }}</td>
                        <td class="px-4 py-2">{{ $result['b1'] }}</td>
                        <td class="px-4 py-2">{{ $result['se_b1'] }}</td>
                        <td class="px-4 py-2">{{ $result['t_b1'] }}</td>
                        <td class="px-4 py-2">
                            {{ $result['p_b1'] }}
                            @if ($result['p_b1'] < 0.05)
                                <span class="text-xs text-red-600 ml-1">*</span>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
                <p class="text-xs text-gray-400">* signifikan pada α = 0.05</p>

                <div class="mt-4">
                    @if ($result['significant'])
                        <span class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded font-medium">
                            ✗ {{ $result['conclusion'] }}
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded font-medium">
                            ✓ {{ $result['conclusion'] }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Scatter Plot + Garis Regresi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">Scatter Plot & Garis Regresi</h3>
                <canvas id="regressionChart" height="100"></canvas>
            </div>

            {{-- Interpretasi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Cara membaca hasil:</strong>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>R²</strong>: {{ $result['r2'] }} — model menjelaskan {{ round($result['r2'] * 100, 1) }}% variasi pada {{ $columnY }}</li>
                        <li><strong>Intercept (b0)</strong>: {{ $result['b0'] }} — nilai {{ $columnY }} ketika {{ $columnX }} = 0</li>
                        <li><strong>Slope (b1)</strong>: {{ $result['b1'] }} — setiap kenaikan 1 satuan {{ $columnX }}, {{ $columnY }} berubah sebesar {{ $result['b1'] }}</li>
                        <li><strong>F-test</strong>: F = {{ $result['f'] }}, p = {{ $result['f_pvalue'] }}
                            → @if($result['significant']) <span class="text-red-600">Model signifikan</span>
                            @else <span class="text-green-600">Model tidak signifikan</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('regressionChart');
        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Data',
                        data: @json($points),
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        pointRadius: 5,
                        type: 'scatter',
                    },
                    {
                        label: 'Garis Regresi',
                        data: @json($regressionLine),
                        type: 'line',
                        borderColor: 'rgba(239, 68, 68, 0.9)',
                        borderWidth: 2,
                        pointRadius: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                },
                scales: {
                    x: { title: { display: true, text: '{{ $columnX }}' } },
                    y: { title: { display: true, text: '{{ $columnY }}' } },
                },
            },
        });
    </script>
</x-app-layout>
