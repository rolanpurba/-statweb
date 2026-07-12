<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Statistik Deskriptif — {{ $dataset->name }}
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Kolom: {{ $columnName }}</h3>
                <p class="text-sm text-gray-500 mb-4">Jumlah data valid (N): {{ $stats['n'] }}</p>

                <table class="min-w-full divide-y divide-gray-200">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Mean (Rata-rata)</td>
                        <td class="px-4 py-2">{{ number_format($stats['mean'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Median</td>
                        <td class="px-4 py-2">{{ number_format($stats['median'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Modus</td>
                        <td class="px-4 py-2">{{ $stats['mode'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Standar Deviasi</td>
                        <td class="px-4 py-2">{{ number_format($stats['std_dev'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Variansi</td>
                        <td class="px-4 py-2">{{ number_format($stats['variance'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Minimum</td>
                        <td class="px-4 py-2">{{ number_format($stats['min'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Maksimum</td>
                        <td class="px-4 py-2">{{ number_format($stats['max'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Range</td>
                        <td class="px-4 py-2">{{ number_format($stats['range'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Q1 (Kuartil 1)</td>
                        <td class="px-4 py-2">{{ number_format($stats['q1'], 4) }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Q3 (Kuartil 3)</td>
                        <td class="px-4 py-2">{{ number_format($stats['q3'], 4) }}</td>
                    </tr>
                    </tbody>
                </table>

                <p class="text-xs text-gray-400 mt-4">
                    Catatan: Standar deviasi & variansi dihitung menggunakan rumus sampel (n-1),
                    konsisten dengan STDEV.S / VAR.S di Excel. Kuartil menggunakan metode interpolasi
                    linear, konsisten dengan QUARTILE.INC di Excel.
                </p>
            </div>

            {{-- Histogram --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Histogram — {{ $columnName }}</h3>
                <canvas id="histogramChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('histogramChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($histogram['labels']),
                datasets: [{
                    label: 'Frekuensi',
                    data: @json($histogram['values']),
                    backgroundColor: 'rgba(79, 70, 229, 0.6)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: { display: false },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Frekuensi' },
                        ticks: { stepSize: 1 },
                    },
                    x: {
                        title: { display: true, text: '{{ $columnName }}' },
                    },
                },
            },
        });
    </script>
</x-app-layout>
