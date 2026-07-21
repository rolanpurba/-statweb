<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Korelasi {{ $methodName }} — {{ $dataset->name }}
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

            {{-- Hasil Korelasi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">{{ $columnX }} vs {{ $columnY }}</h3>
                <p class="text-sm text-gray-500 mb-4">Metode: {{ $methodName }} | N = {{ $result['n'] }}</p>

                {{-- Koefisien Korelasi --}}
                <div class="flex items-center justify-center mb-6">
                    <div class="text-center p-6 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Koefisien Korelasi (r)</p>
                        <p class="text-5xl font-bold {{ $result['r'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            {{ $result['r'] }}
                        </p>
                        <p class="text-sm mt-2">
                            <span class="font-medium">{{ $result['direction'] }}</span> —
                            <span>{{ $result['strength'] }}</span>
                        </p>
                    </div>
                </div>

                <table class="min-w-full divide-y divide-gray-200 mb-4">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700 w-1/2">Koefisien r</td>
                        <td class="px-4 py-2">{{ $result['r'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">t hitung</td>
                        <td class="px-4 py-2">{{ $result['t'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">df</td>
                        <td class="px-4 py-2">{{ $result['df'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">p-value (2-tailed)</td>
                        <td class="px-4 py-2">{{ $result['p_value'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Kekuatan Korelasi</td>
                        <td class="px-4 py-2">{{ $result['strength'] }} ({{ $result['direction'] }})</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Kesimpulan</td>
                        <td class="px-4 py-2">
                            @if ($result['significant'])
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded font-medium">
                                        ✗ {{ $result['conclusion'] }}
                                    </span>
                            @else
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-medium">
                                        ✓ {{ $result['conclusion'] }}
                                    </span>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Pedoman kekuatan korelasi:</strong>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li>|r| = 0.00 - 0.19 → Sangat Lemah</li>
                        <li>|r| = 0.20 - 0.39 → Lemah</li>
                        <li>|r| = 0.40 - 0.59 → Sedang</li>
                        <li>|r| = 0.60 - 0.79 → Kuat</li>
                        <li>|r| = 0.80 - 1.00 → Sangat Kuat</li>
                        <li class="mt-1 text-xs text-blue-500">Gunakan Pearson jika data normal, Spearman jika data tidak normal atau berskala ordinal.</li>
                    </ul>
                </div>
            </div>

            {{-- Scatter Plot --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-2">Scatter Plot</h3>
                <canvas id="correlationChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('correlationChart');
        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: '{{ $columnX }} vs {{ $columnY }}',
                    data: @json($points),
                    backgroundColor: 'rgba(37, 99, 235, 0.6)',
                    borderColor: 'rgba(37, 99, 235, 1)',
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
