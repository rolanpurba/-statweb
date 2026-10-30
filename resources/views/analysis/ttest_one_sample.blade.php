<x-app-layout>
    <x-slot name="header">Uji-t Satu Sampel</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">{{ $columnName }}</h3>
        <p class="text-xs text-slate-400 sw-mono mb-5">Nilai Uji: {{ $result['test_value'] }} · N = {{ $result['n'] }}</p>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            @foreach (['mean' => 'Mean', 'std_dev' => 'Std Dev', 'se' => 'Std Error', 't' => 't Hitung'] as $key => $lbl)
                <div class="bg-slate-50 rounded-lg p-3">
                    <p class="text-xs text-slate-400 uppercase tracking-wide">{{ $lbl }}</p>
                    <p class="sw-mono text-sm font-semibold text-slate-800 mt-1">{{ $result[$key] }}</p>
                </div>
            @endforeach
        </div>

        <table class="min-w-full divide-y divide-slate-100 text-sm mb-5">
            <tbody class="divide-y divide-slate-100">
            <tr><td class="py-2 font-medium text-slate-600 w-1/2">df</td><td class="py-2 sw-mono text-slate-700">{{ $result['df'] }}</td></tr>
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

        <div class="p-4 bg-cyan-50/60 text-cyan-900 rounded-lg text-sm">
            <strong>H₀:</strong> Rata-rata populasi = {{ $result['test_value'] }} |
            <strong>H₁:</strong> Rata-rata populasi ≠ {{ $result['test_value'] }}<br>
            <span class="mt-1 block">{{ $result['conclusion'] }}</span>
        </div>
    </div>
</x-app-layout>
