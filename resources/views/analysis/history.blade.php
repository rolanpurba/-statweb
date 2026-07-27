<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Analisis — {{ $dataset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('datasets.show', $dataset->id) }}" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Dataset
                </a>
                <a href="{{ route('analysis.create', $dataset->id) }}"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                    + Analisis Baru
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        Output Viewer
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">
                        Semua hasil analisis yang pernah dijalankan pada dataset ini
                    </p>
                </div>

                @if ($histories->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-400 text-sm">
                        Belum ada analisis yang dijalankan pada dataset ini.
                        <div class="mt-2">
                            <a href="{{ route('analysis.create', $dataset->id) }}"
                               class="text-indigo-600 hover:underline">
                                Jalankan analisis pertama →
                            </a>
                        </div>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Analisis</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Tipe</th>
                            <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Dijalankan</th>
                            <th class="px-6 py-2"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @foreach ($histories as $history)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <p class="text-sm font-medium text-gray-800">{{ $history->analysis_label }}</p>
                                </td>
                                <td class="px-6 py-3">
                                        <span class="px-2 py-0.5 text-xs rounded-full
                                            @if (str_contains($history->analysis_type, 'regression')) bg-emerald-100 text-emerald-700
                                            @elseif (str_contains($history->analysis_type, 'ttest')) bg-orange-100 text-orange-700
                                            @elseif ($history->analysis_type === 'anova') bg-cyan-100 text-cyan-700
                                            @elseif ($history->analysis_type === 'correlation') bg-blue-100 text-blue-700
                                            @elseif ($history->analysis_type === 'normality') bg-yellow-100 text-yellow-700
                                            @elseif ($history->analysis_type === 'chi_square') bg-pink-100 text-pink-700
                                            @elseif ($history->analysis_type === 'descriptive') bg-indigo-100 text-indigo-700
                                            @else bg-gray-100 text-gray-600
                                            @endif
                                        ">
                                            {{ str_replace('_', ' ', ucfirst($history->analysis_type)) }}
                                        </span>
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-400">
                                    {{ $history->created_at->format('d M Y, H:i') }}
                                    <span class="text-gray-300 ml-1">({{ $history->created_at->diffForHumans() }})</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('analysis.history_show', [$dataset->id, $history->id]) }}"
                                       class="text-xs text-indigo-600 hover:underline">
                                        Lihat →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="px-6 py-4">
                        {{ $histories->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
