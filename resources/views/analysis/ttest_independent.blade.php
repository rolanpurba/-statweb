<x-app-layout>
    <x-slot name="header">Uji-t Independen</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">{{ $columnValue }}</h3>
        <p class="text-xs text-slate-400 sw-mono mb-5">Kelompok: {{ $columnGroup }} · Metode: Welch's t-test</p>

        <div class="grid grid-cols-2 gap-3 mb-5">
            <div class="bg-slate-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-slate-600 mb-2">{{ $result['group1'] }}</p>
                <div class="space-y-1">
                    <p class="text-xs text-slate-400">N = <span class="sw-mono text-slate-700">{{ $result['n1'] }}</span></p>
                    <p class="text-xs text-slate-400">Mean = <span class="sw-mono text-slate-700">{{ $result['mean1'] }}</span></p>
                    <p class="text-xs text-slate-400">Std Dev = <span class="sw-mono text-slate-700">{{ $result['std1'] }}</span></p>
                </div>
            </div>
            <div class="bg-slate-50 rounded-lg p-4">
                <p class="text-xs font-semibold text-slate-600 mb-2">{{ $result['group2'] }}</p>
                <div class="space-y-1">
                    <p class="text-xs text-slate-400">N = <span class="sw-mono text-slate-700">{{ $result['n2'] }}</span></p>
                    <p class="text-xs text-slate-400">Mean = <span class="sw-mono text-slate-700">{{ $result['mean2'] }}</span></p>
                    <p class="text-xs text-slate-400">Std Dev = <span class="sw-mono text-slate-700">{{ $result['std2'] }}</span></p>
                </div>
            </div>
        </div>

        <table class="min-w-full divide-y divide-slate-100 text-sm mb-5">
            <tbody class="divide-y divide-slate-100">
            <tr><td class="py-2 font-medium text-slate-600 w-1/2">t hitung</td><td class="py-2 sw-mono text-slate-700">{{ $result['t'] }}</td></tr>
            <tr><td class="py-2 font-medium text-slate-600">df (Welch-Satterthwaite)</td><td class="py-2 sw-mono text-slate-700">{{ $result['df'] }}</td></tr>
            <tr><td class="py-2 font-medium text-slate-600">p-value (2-tailed)</td><td class="py-2 sw-mono text-slate-700">{{ $result['p_value'] }}</td></tr>
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
</x-app-layout>
