<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Distribusi Frekuensi — {{ $dataset->name }}
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
                <p class="text-sm text-gray-500 mb-4">Total data valid: {{ $total }}</p>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Frekuensi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Persentase</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach ($frequencies as $category => $count)
                        <tr>
                            <td class="px-4 py-2">{{ $category }}</td>
                            <td class="px-4 py-2">{{ $count }}</td>
                            <td class="px-4 py-2">{{ number_format(($count / $total) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Bar Chart --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Bar Chart — {{ $columnName }}</h3>
                <canvas id="categoricalChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('categoricalChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json(array_keys($frequencies)),
                datasets: [{
                    label: 'Frekuensi',
                    data: @json(array_values($frequencies)),
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
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
