<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Scatter Plot — {{ $dataset->name }}
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
                <h3 class="text-lg font-medium mb-1">{{ $columnX }} vs {{ $columnY }}</h3>
                <p class="text-sm text-gray-500">Jumlah pasangan data valid: {{ count($points) }}</p>
            </div>

            {{-- Scatter Chart --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <canvas id="scatterChart" height="100"></canvas>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('scatterChart');

        new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: '{{ $columnX }} vs {{ $columnY }}',
                    data: @json($points),
                    backgroundColor: 'rgba(147, 51, 234, 0.6)',
                    borderColor: 'rgba(147, 51, 234, 1)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    x: {
                        title: { display: true, text: '{{ $columnX }}' },
                    },
                    y: {
                        title: { display: true, text: '{{ $columnY }}' },
                    },
                },
            },
        });
    </script>
</x-app-layout>
