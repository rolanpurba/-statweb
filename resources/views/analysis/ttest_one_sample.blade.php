<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Uji-t Satu Sampel — {{ $dataset->name }}
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

            {{-- Hasil --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Kolom: {{ $columnName }}</h3>
                <p class="text-sm text-gray-500 mb-4">Nilai uji (Test Value): <strong>{{ $result['test_value'] }}</strong></p>

                <table class="min-w-full divide-y divide-gray-200 mb-4">
                    <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700 w-1/2">N (jumlah data)</td>
                        <td class="px-4 py-2">{{ $result['n'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Mean sampel</td>
                        <td class="px-4 py-2">{{ $result['mean'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Standar Deviasi</td>
                        <td class="px-4 py-2">{{ $result['std_dev'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">Standard Error</td>
                        <td class="px-4 py-2">{{ $result['se'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">t hitung</td>
                        <td class="px-4 py-2">{{ $result['t'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">df (derajat bebas)</td>
                        <td class="px-4 py-2">{{ $result['df'] }}</td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2 font-medium text-gray-700">p-value (2-tailed)</td>
                        <td class="px-4 py-2">{{ $result['p_value'] }}</td>
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

                {{-- Penjelasan --}}
                <div class="p-4 bg-blue-50 text-blue-700 rounded text-sm">
                    <strong>Cara membaca hasil:</strong>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><strong>H₀:</strong> Rata-rata populasi = {{ $result['test_value'] }}</li>
                        <li><strong>H₁:</strong> Rata-rata populasi ≠ {{ $result['test_value'] }}</li>
                        <li>Jika p-value &lt; 0.05 → tolak H₀ (ada perbedaan signifikan)</li>
                        <li>Jika p-value ≥ 0.05 → gagal tolak H₀ (tidak ada perbedaan signifikan)</li>
                        <li>p-value kamu: <strong>{{ $result['p_value'] }}</strong>
                            → @if($result['significant']) <span class="text-red-600">Tolak H₀</span>
                            @else <span class="text-green-600">Gagal tolak H₀</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
