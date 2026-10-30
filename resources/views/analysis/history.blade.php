<x-app-layout>
    <x-slot name="header">Riwayat Analisis</x-slot>

    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Dataset
        </a>
        <a href="{{ route('analysis.create', $dataset->id) }}"
           class="bg-cyan-700 text-white px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-cyan-800 transition">
            + Analisis Baru
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-sm font-semibold text-slate-700 sw-display">Output Viewer</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $dataset->name }}</p>
            </div>
            <a href="{{ route('export.history_pdf', $dataset->id) }}"
               class="bg-slate-100 text-slate-700 px-3 py-1.5 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                Export Semua PDF
            </a>
        </div>

        @if ($histories->isEmpty())
            <div class="px-6 py-12 text-center text-slate-400 text-sm">
                Belum ada analisis yang dijalankan.
                <div class="mt-2">
                    <a href="{{ route('analysis.create', $dataset->id) }}"
                       class="text-cyan-700 hover:underline font-medium">
                        Jalankan analisis pertama →
                    </a>
                </div>
            </div>
        @else
            {{-- Desktop --}}
            <div class="hidden sm:block divide-y divide-slate-100">
                @foreach ($histories as $history)
                    <div class="flex items-center justify-between px-6 py-3.5 hover:bg-slate-50/60 transition">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $history->analysis_label }}</p>
                            <p class="text-xs text-slate-400 sw-mono mt-0.5">
                                {{ $history->created_at->format('d M Y, H:i') }} ·
                                {{ $history->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                            <span class="px-2 py-0.5 text-xs rounded-full
                                @if (str_contains($history->analysis_type, 'regression')) bg-emerald-100 text-emerald-700
                                @elseif (str_contains($history->analysis_type, 'ttest')) bg-orange-100 text-orange-700
                                @elseif ($history->analysis_type === 'anova') bg-cyan-100 text-cyan-700
                                @elseif ($history->analysis_type === 'correlation') bg-blue-100 text-blue-700
                                @elseif ($history->analysis_type === 'normality') bg-yellow-100 text-yellow-700
                                @elseif ($history->analysis_type === 'chi_square') bg-pink-100 text-pink-700
                                @elseif ($history->analysis_type === 'descriptive') bg-indigo-100 text-indigo-700
                                @else bg-slate-100 text-slate-600
                                @endif">
                                {{ str_replace('_', ' ', ucfirst($history->analysis_type)) }}
                            </span>
                            <a href="{{ route('analysis.history_show', [$dataset->id, $history->id]) }}"
                               class="text-xs text-cyan-700 hover:text-cyan-800 font-medium">
                                Lihat →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Mobile --}}
            <div class="sm:hidden divide-y divide-slate-100">
                @foreach ($histories as $history)
                    <div class="px-4 py-3.5">
                        <p class="text-sm font-medium text-slate-800">{{ $history->analysis_label }}</p>
                        <p class="text-xs text-slate-400 sw-mono mt-0.5 mb-2">{{ $history->created_at->format('d M Y, H:i') }}</p>
                        <div class="flex gap-3 items-center">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-slate-100 text-slate-600">
                                {{ str_replace('_', ' ', ucfirst($history->analysis_type)) }}
                            </span>
                            <a href="{{ route('analysis.history_show', [$dataset->id, $history->id]) }}"
                               class="text-xs text-cyan-700 font-medium">Lihat →</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-slate-100">
                {{ $histories->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
