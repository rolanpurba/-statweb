<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            One-Way ANOVA — {{ $dataset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('analysis.create', $dataset->id) }}" class="text-indigo-600 hover:underline">
                    &larr; Pilih Analisis Lain
                </a>
                <a href="{{ route('datasets.show', $dataset->id) }}" class="text-gray-500 hover:underline text-sm">
                    Kembali ke Dataset
                </a>
            </div>

            {{-- Statistik Per Kelompok --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">{{ $columnValue }} berdasarkan {{ $columnGroup }}</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kelompok</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">N</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mean</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Std Dev</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach ($groupStats as $name => $stat)
                        <tr>
                            <td class="px-4 py-2 font-medium">{{ $name }}</td>
                            <td class="px-4 py-2">{{ $stat['n'] }}</td>
                            <td class="px-4 py-2">{{ $stat['mean'] }}</td>
                            <td class="px-4 py-2">{{ $stat['std'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50">
                        <td class="px-4 py-2 font-medium">Total</td>
                        <td class="px-4 py-2">{{ $result['N'] }}</td>
                        <td class="px-4 py-2">{{ $result['grand_mean'] }}</td>
                        <td class="px-4 py-2">—</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            {{-- Tabel ANOVA --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">Tabel ANOVA</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sumber Variasi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SS</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">df</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">MS</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">F</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">p-value</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium">Antar Kelompok (Between)</td>
                        <td class="px-4 py-2">{{ $result['ssb'] }}</td>
                        <td class="px-4 py-2">{{ $result['dfb'] }}</td>
                        <td class="px-4 py-2">{{ $result['msb'] }}</td>
                        <td class="px-4 py-2 font-medium">{{ $result['f'] }}</td>
                        <td class="px-4 py-2 font-medium">{{ $result['p_value'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium">Dalam Kelompok (Within)</td>
                        <td class="px-4 py-2">{{ $result['ssw'] }}</td>
                        <td class="px-4 py-2">{{ $result['dfw'] }}</td>
                        <td class="px-4 py-2">{{ $result['msw'] }}</td>
                        <td class="px-4 py-2">—</td>
                        <td class="px-4 py-2">—</td>
                    </tr>
                    </tbody>
                </table>

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

            {{-- Interpretasi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Cara membaca hasil:</strong>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>H₀:</strong> Semua rata-rata kelompok sama (μ₁ = μ₂ = ... = μk)</li>
                        <li><strong>H₁:</strong> Minimal ada satu rata-rata kelompok yang berbeda</li>
                        <li>Jika p-value &lt; 0.05 → tolak H₀ (ada perbedaan signifikan antar kelompok)</li>
                        <li>Jika p-value ≥ 0.05 → gagal tolak H₀ (tidak ada perbedaan signifikan)</li>
                        <li>F hitung: <strong>{{ $result['f'] }}</strong> | p-value: <strong>{{ $result['p_value'] }}</strong>
                            → @if($result['significant']) <span class="text-red-600">Tolak H₀</span>
                            @else <span class="text-green-600">Gagal tolak H₀</span>
                            @endif
                        </li>
                        <li class="text-xs text-blue-500 mt-1">Jika H₀ ditolak, lakukan Post-hoc test (misal Tukey HSD) untuk mengetahui kelompok mana yang berbeda secara spesifik.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
