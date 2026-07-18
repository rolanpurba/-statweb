<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Uji Normalitas — {{ $dataset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('analysis.create', $dataset->id) }}" class="text-indigo-600 hover:underline">
                    &larr; Pilih Kolom Lain
                </a>
                <a href="{{ route('datasets.show', $dataset->id) }}" class="text-gray-500 hover:underline text-sm">
                    Kembali ke Dataset
                </a>
            </div>

            {{-- Hasil Uji --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Kolom: {{ $columnName }}</h3>
                <p class="text-sm text-gray-500 mb-4">N = {{ $n }} | Metode: {{ $testName }}</p>

                <table class="min-w-full divide-y divide-gray-200 mb-4">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Uji yang digunakan</td>
                        <td class="px-4 py-2">{{ $testName }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Statistik</td>
                        <td class="px-4 py-2">{{ $result['statistic'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">p-value</td>
                        <td class="px-4 py-2">{{ $result['p_value'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Kesimpulan</td>
                        <td class="px-4 py-2">
                            @if ($result['normal'])
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded font-medium">
                                        ✓ {{ $result['conclusion'] }}
                                    </span>
                            @else
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded font-medium">
                                        ✗ {{ $result['conclusion'] }}
                                    </span>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Interpretasi:</strong>
                    @if ($result['normal'])
                        Data berdistribusi normal. Kamu dapat melanjutkan dengan uji parametrik
                        seperti Uji-t atau ANOVA.
                    @else
                        Data tidak berdistribusi normal. Pertimbangkan menggunakan uji nonparametrik
                        seperti Mann-Whitney atau Wilcoxon sebagai alternatif.
                    @endif
                </div>
            </div>

            {{-- Q-Q Plot --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-2">Q-Q Plot</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Titik mendekati garis diagonal = data mendekati distribusi normal
                </p>
                <canvas id="qqChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const points = @json($qqPoints);

        // Hitung garis referensi diagonal (dari titik min ke max)
        const xVals = points.map(p => p.x);
        const yVals = points.map(p => p.y);
        const minVal = Math.min(...xVals, ...yVals);
        const maxVal = Math.max(...xVals, ...yVals);

        const ctx = document.getElementById('qqChart');
        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [
                    {
                        label: 'Data',
                        data: points,
                        backgroundColor: 'rgba(99, 102, 241, 0.7)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        pointRadius: 5,
                    },
                    {
                        label: 'Garis Normal',
                        data: [{ x: minVal, y: minVal }, { x: maxVal, y: maxVal }],
                        type: 'line',
                        borderColor: 'rgba(239, 68, 68, 0.8)',
                        borderWidth: 2,
                        borderDash: [5, 5],
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
                    x: {
                        title: { display: true, text: 'Kuantil Teoritis (Normal)' },
                    },
                    y: {
                        title: { display: true, text: 'Kuantil Empiris (Data)' },
                    },
                },
            },
        });
    </script>
</x-app-layout>
