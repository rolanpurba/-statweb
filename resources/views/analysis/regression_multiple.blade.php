<x-app-layout>
    <x-slot name="header">Regresi Linear Berganda</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Variabel Dependen: {{ $columnY }}</h3>
        <p class="text-xs text-slate-400 sw-mono mb-5">Prediktor: {{ implode(', ', $columnsX) }} · N = {{ $result['n'] }}</p>

        <div class="p-4 bg-[#0F172A] rounded-xl text-center mb-5">
            <p class="text-xs text-slate-400 mb-1">Persamaan Regresi</p>
            <p class="sw-mono text-sm font-bold text-cyan-300 break-words">{{ $result['equation'] }}</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">R²</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['r2'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">R² Adj</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['r2_adj'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">F hitung</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['f'] }}</p>
            </div>
            <div class="bg-slate-50 rounded-lg p-3">
                <p class="text-xs text-slate-400 uppercase tracking-wide">p-value</p>
                <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result['f_pvalue'] }}</p>
            </div>
        </div>

        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Koefisien</h4>
        <div class="overflow-x-auto mb-5">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                <tr class="bg-slate-50">
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Variabel</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Koefisien</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Std Error</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">t</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">p-value</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Sig.</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach ($coefficients as $coef)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-2 font-medium text-slate-700">{{ $coef['label'] }}</td>
                        <td class="px-4 py-2 sw-mono text-slate-600">{{ $coef['coefficient'] }}</td>
                        <td class="px-4 py-2 sw-mono text-slate-600">{{ $coef['se'] }}</td>
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

        <div class="p-4 bg-cyan-50/60 text-cyan-900 rounded-lg text-sm mb-4">
            <strong>R² = {{ $result['r2'] }}</strong> — model menjelaskan {{ round($result['r2'] * 100, 1) }}% variasi pada {{ $columnY }}
        </div>

        @if ($result['significant'])
            <span class="px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded-lg font-medium">✗ {{ $result['conclusion'] }}</span>
        @else
            <span class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 rounded-lg font-medium">✓ {{ $result['conclusion'] }}</span>
        @endif
    </div>
</x-app-layout>
