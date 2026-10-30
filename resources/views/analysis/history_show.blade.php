<x-app-layout>
    <x-slot name="header">{{ $analysisResult->analysis_label }}</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.history', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Riwayat
        </a>
        <div class="flex gap-2">
            <a href="{{ route('export.analysis_pdf', [$dataset->id, $analysisResult->id]) }}"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Export PDF
            </a>
            <a href="{{ route('analysis.create', $dataset->id) }}"
               class="bg-cyan-700 text-white px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-cyan-800 transition">
                Analisis Baru
            </a>
        </div>
    </div>

    {{-- Info --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 mb-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Dataset</p>
                <p class="font-medium text-slate-700">{{ $dataset->name }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Jenis Analisis</p>
                <p class="font-medium text-slate-700">{{ str_replace('_', ' ', ucfirst($analysisResult->analysis_type)) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Parameter</p>
                @foreach ($analysisResult->parameters as $key => $val)
                    <p class="text-xs text-slate-600 sw-mono">{{ $key }}: {{ is_array($val) ? implode(', ', $val) : $val }}</p>
                @endforeach
            </div>
            <div>
                <p class="text-xs text-slate-400 uppercase tracking-wide mb-1">Dijalankan</p>
                <p class="text-xs text-slate-600 sw-mono">{{ $analysisResult->created_at->format('d M Y, H:i:s') }}</p>
            </div>
        </div>
    </div>

    {{-- Hasil --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5">
        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-4">Hasil</h3>

        @php $result = $analysisResult->result; @endphp

        @if ($analysisResult->analysis_type === 'descriptive')
            @php $stats = $result['stats']; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach ([
                    'n' => 'N', 'mean' => 'Mean', 'median' => 'Median', 'mode' => 'Modus',
                    'std_dev' => 'Std Dev', 'variance' => 'Variansi', 'min' => 'Min',
                    'max' => 'Max', 'range' => 'Range', 'q1' => 'Q1', 'q3' => 'Q3'
                ] as $key => $lbl)
                    <div class="bg-slate-50 rounded-lg p-3">
                        <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $lbl }}</p>
                        <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">
                            {{ is_numeric($stats[$key]) ? number_format($stats[$key], 4) : $stats[$key] }}
                        </p>
                    </div>
                @endforeach
            </div>

        @elseif ($analysisResult->analysis_type === 'categorical')
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
                    @foreach ($result['frequencies'] as $cat => $freq)
                        <tr>
                            <td class="px-4 py-2 text-slate-700">{{ $cat }}</td>
                            <td class="px-4 py-2 sw-mono text-slate-600">{{ $freq }}</td>
                            <td class="px-4 py-2 sw-mono text-slate-600">{{ number_format(($freq / $result['total']) * 100, 1) }}%</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        @else
            @php $displayResult = isset($result['result']) ? $result['result'] : $result; @endphp
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <tbody class="divide-y divide-slate-100">
                    @foreach ($displayResult as $key => $val)
                        @if (!is_array($val))
                            <tr class="hover:bg-slate-50/60">
                                <td class="py-2 font-medium text-slate-600 w-1/2">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </td>
                                <td class="py-2 sw-mono text-slate-700">
                                    @if (is_bool($val))
                                        @if ($val)
                                            <span class="px-2 py-0.5 text-xs bg-red-50 text-red-700 rounded">Ya (Signifikan)</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs bg-emerald-50 text-emerald-700 rounded">Tidak Signifikan</span>
                                        @endif
                                    @else
                                        {{ $val }}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>

            @if ($analysisResult->analysis_type === 'regression_multiple' && isset($result['coefficients']))
                <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-5 mb-2">Koefisien</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead>
                        <tr class="bg-slate-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Variabel</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Koefisien</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">t</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">p-value</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Sig.</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach ($result['coefficients'] as $coef)
                            <tr class="hover:bg-slate-50/60">
                                <td class="px-4 py-2 font-medium text-slate-700">{{ $coef['label'] }}</td>
                                <td class="px-4 py-2 sw-mono text-slate-600">{{ $coef['coefficient'] }}</td>
                                <td class="px-4 py-2 sw-mono text-slate-600">{{ $coef['t'] }}</td>
                                <td class="px-4 py-2 sw-mono {{ $coef['p_value'] < 0.05 ? 'text-red-600 font-medium' : 'text-slate-600' }}">{{ $coef['p_value'] }}</td>
                                <td class="px-4 py-2">
                                    @if ($coef['significant'])
                                        <span class="px-2 py-0.5 text-xs bg-red-50 text-red-700 rounded">Ya *</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs bg-slate-100 text-slate-500 rounded">Tidak</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endif

        <div class="mt-5 pt-4 border-t border-slate-100 flex gap-3 text-xs">
            <a href="{{ route('analysis.create', $dataset->id) }}" class="text-cyan-700 hover:underline font-medium">
                Jalankan Analisis Baru →
            </a>
            <span class="text-slate-300">|</span>
            <a href="{{ route('analysis.history', $dataset->id) }}" class="text-slate-500 hover:text-slate-700">
                Lihat Semua Riwayat →
            </a>
        </div>
    </div>
</x-app-layout>
