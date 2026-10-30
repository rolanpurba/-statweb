<x-app-layout>
    <x-slot name="header">Chi-square Test</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">{{ $column1 }} × {{ $column2 }}</h3>
            <p class="text-xs text-slate-400 sw-mono mb-5">N = {{ $N }}</p>

            <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Frekuensi Observasi</h4>
            <div class="overflow-x-auto mb-4">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead>
                    <tr class="bg-slate-50">
                        <th class="px-3 py-2 text-left text-xs font-medium text-slate-400">{{ $column1 }} \ {{ $column2 }}</th>
                        @foreach ($colLabels as $col)
                            <th class="px-3 py-2 text-left text-xs font-medium text-slate-400">{{ $col }}</th>
                        @endforeach
                        <th class="px-3 py-2 text-left text-xs font-medium text-slate-400">Total</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach ($rowLabels as $row)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-3 py-2 font-medium text-slate-700">{{ $row }}</td>
                            @foreach ($colLabels as $col)
                                <td class="px-3 py-2 sw-mono text-slate-600">{{ $observed[$row][$col] ?? 0 }}</td>
                            @endforeach
                            <td class="px-3 py-2 sw-mono font-medium text-slate-700">{{ $rowTotals[$row] }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-slate-50">
                        <td class="px-3 py-2 font-medium text-slate-700">Total</td>
                        @foreach ($colLabels as $col)
                            <td class="px-3 py-2 sw-mono font-medium text-slate-700">{{ $colTotals[$col] }}</td>
                        @endforeach
                        <td class="px-3 py-2 sw-mono font-medium text-slate-700">{{ $N }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Hasil Uji</h4>
            <table class="min-w-full divide-y divide-slate-100 text-sm mb-4">
                <tbody class="divide-y divide-slate-100">
                <tr><td class="py-2 font-medium text-slate-600 w-1/2">Chi-square (χ²)</td><td class="py-2 sw-mono text-slate-700">{{ $result['chi_sq'] }}</td></tr>
                <tr><td class="py-2 font-medium text-slate-600">df</td><td class="py-2 sw-mono text-slate-700">{{ $result['df'] }}</td></tr>
                <tr><td class="py-2 font-medium text-slate-600">p-value</td><td class="py-2 sw-mono text-slate-700">{{ $result['p_value'] }}</td></tr>
                <tr><td class="py-2 font-medium text-slate-600">Cramér's V (effect size)</td><td class="py-2 sw-mono text-slate-700">{{ $result['cramers_v'] }}</td></tr>
                <tr>
                    <td class="py-2 font-medium text-slate-600">Kesimpulan</td>
                    <td class="py-2">
                        @if ($result['significant'])
                            <span class="px-2 py-1 text-xs bg-red-50 text-red-700 rounded-lg font-medium">✗ Tolak H₀</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-emerald-50 text-emerald-700 rounded-lg font-medium">✓ Gagal Tolak H₀</span>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>

            <div class="p-4 bg-cyan-50/60 text-cyan-900 rounded-lg text-sm">{{ $result['conclusion'] }}</div>
        </div>
    </div>
</x-app-layout>
