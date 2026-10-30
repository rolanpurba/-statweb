<x-app-layout>
    <x-slot name="header">Distribusi Frekuensi</x-slot>

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
        <p class="text-xs text-slate-400 sw-mono mb-5">Total data valid: {{ $total }}</p>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                <tr class="bg-slate-50">
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Kategori</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Frekuensi</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Persentase</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach ($frequencies as $category => $count)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-2 font-medium text-slate-700">{{ $category }}</td>
                        <td class="px-4 py-2 sw-mono text-slate-600">{{ $count }}</td>
                        <td class="px-4 py-2 sw-mono text-slate-600">{{ number_format(($count / $total) * 100, 1) }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-4">Bar Chart</h3>
        <canvas id="categoricalChart" height="100"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('categoricalChart'), {
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
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Frekuensi' }, ticks: { stepSize: 1 } },
                    x: { title: { display: true, text: '{{ $columnName }}' } },
                },
            },
        });
    </script>
</x-app-layout>
