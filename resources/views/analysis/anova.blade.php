<x-app-layout>
    <x-slot name="header">One-Way ANOVA</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('analysis.create', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Pilih Analisis Lain
        </a>
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-400 hover:text-slate-600">
            Kembali ke Dataset
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">{{ $columnValue }} berdasarkan {{ $columnGroup }}</h3>
        <p class="text-xs text-slate-400 sw-mono mb-5">{{ $result['k'] }} kelompok · N total = {{ $result['N'] }} · Grand mean = {{ $result['grand_mean'] }}</p>

        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Statistik Per Kelompok</h4>
        <div class="overflow-x-auto mb-5">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                <tr class="bg-slate-50">
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Kelompok</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">N</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Mean</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Std Dev</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @foreach ($groupStats as $name => $stat)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-4 py-2 font-medium text-slate-700">{{ $name }}</td>
                        <td class="px-4 py-2 sw-mono text-slate-600">{{ $stat['n'] }}</td>
                        <td class="px-4 py-2 sw-mono text-slate-600">{{ $stat['mean'] }}</td>
                        <td class="px-4 py-2 sw-mono text-slate-600">{{ $stat['std'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <h4 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Tabel ANOVA</h4>
        <div class="overflow-x-auto mb-5">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead>
                <tr class="bg-slate-50">
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">Sumber</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">SS</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">df</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">MS</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">F</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-slate-400 uppercase">p-value</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-2 font-medium text-slate-700">Antar Kelompok</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['ssb'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['dfb'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['msb'] }}</td>
                    <td class="px-4 py-2 sw-mono font-semibold text-slate-700">{{ $result['f'] }}</td>
                    <td class="px-4 py-2 sw-mono font-semibold text-slate-700">{{ $result['p_value'] }}</td>
                </tr>
                <tr class="hover:bg-slate-50/60">
                    <td class="px-4 py-2 font-medium text-slate-700">Dalam Kelompok</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['ssw'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['dfw'] }}</td>
                    <td class="px-4 py-2 sw-mono text-slate-600">{{ $result['msw'] }}</td>
                    <td class="px-4 py-2 text-slate-300">—</td>
                    <td class="px-4 py-2 text-slate-300">—</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="flex items-center gap-3 mb-4">
            @if ($result['significant'])
                <span class="px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded-lg font-medium">✗ Tolak H₀</span>
            @else
                <span class="px-3 py-1.5 text-xs bg-emerald-50 text-emerald-700 rounded-lg font-medium">✓ Gagal Tolak H₀</span>
            @endif
        </div>

        <div class="p-4 bg-cyan-50/60 text-cyan-900 rounded-lg text-sm">{{ $result['conclusion'] }}</div>
    </div>
</x-app-layout>
