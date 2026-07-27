<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $analysisResult->analysis_label }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('analysis.history', $dataset->id) }}" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Riwayat
                </a>
                <span class="text-xs text-gray-400">
                    Dijalankan: {{ $analysisResult->created_at->format('d M Y, H:i') }}
                </span>
            </div>

            {{-- Info Analisis --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Dataset</p>
                        <p class="font-medium">{{ $dataset->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Jenis Analisis</p>
                        <p class="font-medium">{{ str_replace('_', ' ', ucfirst($analysisResult->analysis_type)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Parameter</p>
                        <div class="space-y-0.5">
                            @foreach ($analysisResult->parameters as $key => $val)
                                <p class="text-gray-600">
                                    <span class="font-medium">{{ $key }}:</span>
                                    {{ is_array($val) ? implode(', ', $val) : $val }}
                                </p>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase mb-1">Waktu</p>
                        <p class="text-gray-600">{{ $analysisResult->created_at->format('d M Y, H:i:s') }}</p>
                    </div>
                </div>
            </div>

            {{-- Hasil Analisis --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Hasil</h3>

                @php $result = $analysisResult->result; @endphp

                @if ($analysisResult->analysis_type === 'descriptive')
                    @php $stats = $result['stats']; @endphp
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <tbody class="divide-y divide-gray-200">
                        @foreach ([
                            'n' => 'N', 'mean' => 'Mean', 'median' => 'Median',
                            'mode' => 'Modus', 'std_dev' => 'Standar Deviasi',
                            'variance' => 'Variansi', 'min' => 'Minimum',
                            'max' => 'Maksimum', 'range' => 'Range',
                            'q1' => 'Q1 (Kuartil 1)', 'q3' => 'Q3 (Kuartil 3)'
                        ] as $key => $label)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-700 w-1/2">{{ $label }}</td>
                                <td class="px-4 py-2">
                                    {{ is_numeric($stats[$key]) ? number_format($stats[$key], 4) : $stats[$key] }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                @elseif (in_array($analysisResult->analysis_type, ['normality', 'ttest_one_sample', 'ttest_independent', 'ttest_paired', 'anova', 'regression_simple', 'regression_multiple', 'correlation', 'chi_square']))
                    {{-- Tampilkan result sebagai tabel key-value --}}
                    @php
                        $displayResult = isset($result['result']) ? $result['result'] : $result;
                    @endphp
                    <table class="min-w-full divide-y divide-gray-200 text-sm mb-4">
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($displayResult as $key => $val)
                            @if (!is_array($val))
                                <tr>
                                    <td class="px-4 py-2 font-medium text-gray-700 w-1/2">
                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                    </td>
                                    <td class="px-4 py-2">
                                        @if (is_bool($val))
                                            <span class="px-2 py-0.5 text-xs rounded {{ $val ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                    {{ $val ? 'Ya (Signifikan)' : 'Tidak (Tidak Signifikan)' }}
                                                </span>
                                        @else
                                            {{ $val }}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>

                    {{-- Koefisien regresi berganda --}}
                    @if ($analysisResult->analysis_type === 'regression_multiple' && isset($result['coefficients']))
                        <h4 class="font-medium text-gray-700 mb-2 mt-4">Koefisien</h4>
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Variabel</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Koefisien</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">t</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">p-value</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Signifikan</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            @foreach ($result['coefficients'] as $coef)
                                <tr>
                                    <td class="px-4 py-2 font-medium">{{ $coef['label'] }}</td>
                                    <td class="px-4 py-2">{{ $coef['coefficient'] }}</td>
                                    <td class="px-4 py-2">{{ $coef['t'] }}</td>
                                    <td class="px-4 py-2">{{ $coef['p_value'] }}</td>
                                    <td class="px-4 py-2">
                                        @if ($coef['significant'])
                                            <span class="px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded">Ya *</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-500 rounded">Tidak</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif

                @elseif ($analysisResult->analysis_type === 'categorical')
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Kategori</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Frekuensi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Persentase</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($result['frequencies'] as $cat => $freq)
                            <tr>
                                <td class="px-4 py-2">{{ $cat }}</td>
                                <td class="px-4 py-2">{{ $freq }}</td>
                                <td class="px-4 py-2">{{ number_format(($freq / $result['total']) * 100, 1) }}%</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                @else
                    {{-- Fallback: tampilkan raw JSON --}}
                    <pre class="text-xs bg-gray-50 p-4 rounded overflow-x-auto">{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif

                <div class="mt-6 pt-4 border-t border-gray-100 flex gap-3">
                    <a href="{{ route('analysis.create', $dataset->id) }}"
                       class="text-sm text-green-600 hover:underline">
                        Jalankan Analisis Baru →
                    </a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('analysis.history', $dataset->id) }}"
                       class="text-sm text-indigo-600 hover:underline">
                        Lihat Semua Riwayat →
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
