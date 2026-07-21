<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Chi-square Test — {{ $dataset->name }}
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

            {{-- Tabel Frekuensi Observasi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">Tabel Frekuensi Observasi</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-500">{{ $column1 }} \ {{ $column2 }}</th>
                            @foreach ($colLabels as $col)
                                <th class="px-3 py-2 text-left font-medium text-gray-500">{{ $col }}</th>
                            @endforeach
                            <th class="px-3 py-2 text-left font-medium text-gray-500">Total</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($rowLabels as $row)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $row }}</td>
                                @foreach ($colLabels as $col)
                                    <td class="px-3 py-2">{{ $observed[$row][$col] ?? 0 }}</td>
                                @endforeach
                                <td class="px-3 py-2 font-medium">{{ $rowTotals[$row] }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-50">
                            <td class="px-3 py-2 font-medium">Total</td>
                            @foreach ($colLabels as $col)
                                <td class="px-3 py-2 font-medium">{{ $colTotals[$col] }}</td>
                            @endforeach
                            <td class="px-3 py-2 font-medium">{{ $N }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabel Frekuensi Expected --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-2">Tabel Frekuensi Harapan (Expected)</h3>
                <p class="text-sm text-gray-500 mb-4">Expected = (Total Baris × Total Kolom) / N</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                        <tr>
                            <th class="px-3 py-2 text-left font-medium text-gray-500">{{ $column1 }} \ {{ $column2 }}</th>
                            @foreach ($colLabels as $col)
                                <th class="px-3 py-2 text-left font-medium text-gray-500">{{ $col }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($rowLabels as $row)
                            <tr>
                                <td class="px-3 py-2 font-medium">{{ $row }}</td>
                                @foreach ($colLabels as $col)
                                    <td class="px-3 py-2">{{ $expected[$row][$col] ?? 0 }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Hasil Chi-square --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Hasil Uji Chi-square</h3>
                <table class="min-w-full divide-y divide-gray-200 mb-4">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700 w-1/2">Chi-square (χ²)</td>
                        <td class="px-4 py-2">{{ $result['chi_sq'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">df</td>
                        <td class="px-4 py-2">{{ $result['df'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">p-value</td>
                        <td class="px-4 py-2">{{ $result['p_value'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">N</td>
                        <td class="px-4 py-2">{{ $result['n'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Cramér's V (effect size)</td>
                        <td class="px-4 py-2">{{ $result['cramers_v'] }}</td>
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
                    <strong>Cara membaca hasil:</strong>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>H₀:</strong> Tidak ada hubungan antara {{ $column1 }} dan {{ $column2 }}</li>
                        <li><strong>H₁:</strong> Ada hubungan antara {{ $column1 }} dan {{ $column2 }}</li>
                        <li>Jika p-value &lt; 0.05 → tolak H₀ (ada hubungan signifikan)</li>
                        <li>Jika p-value ≥ 0.05 → gagal tolak H₀ (tidak ada hubungan signifikan)</li>
                        <li><strong>Cramér's V</strong>: ukuran kekuatan hubungan (0 = tidak ada, 1 = sangat kuat)</li>
                        <li class="text-xs text-blue-500 mt-1">Asumsi: Setiap sel expected ≥ 5. Jika tidak, pertimbangkan Fisher's Exact Test.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
