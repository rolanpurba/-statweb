<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 transition hover:border-[#F6C90E]/50 hover:shadow-sm">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Dataset Tersimpan</p>
            <p class="sw-mono text-3xl font-semibold text-slate-800 counter" data-target="{{ $totalDatasets }}">0</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 transition hover:border-[#F6C90E]/50 hover:shadow-sm">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Total Baris Data</p>
            <p class="sw-mono text-3xl font-semibold text-slate-800 counter" data-target="{{ $totalRows }}">0</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 transition hover:border-[#F6C90E]/50 hover:shadow-sm">
            <p class="text-xs text-slate-400 uppercase tracking-wide mb-2">Upload Terakhir</p>
            <p class="text-sm font-medium text-slate-700 mt-2.5">
                {{ $latestDataset ? $latestDataset->created_at->diffForHumans() : '—' }}
            </p>
            @if ($latestDataset)
                <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $latestDataset->name }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Dataset Terbaru --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="text-sm font-semibold text-slate-700 sw-display">Dataset Terbaru</h3>
                <a href="{{ route('datasets.index') }}" class="text-xs font-medium hover:underline" style="color: #0F2C59;">
                    Lihat semua
                </a>
            </div>

            @if ($recentDatasets->isEmpty())
                <div class="px-5 py-12 text-center text-slate-400 text-sm">
                    Belum ada dataset. Upload file CSV atau Excel untuk memulai.
                    <div class="mt-3">
                        <a href="{{ route('datasets.index') }}"
                           class="inline-block text-white px-4 py-2 rounded-lg text-sm font-medium transition" style="background: #0F2C59;">
                            Upload dataset
                        </a>
                    </div>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($recentDatasets as $dataset)
                        <div class="flex items-center justify-between px-5 py-3.5 transition hover:bg-slate-50 group">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate group-hover:text-[#0F2C59] transition">{{ $dataset->name }}</p>
                                <p class="text-xs text-slate-400 sw-mono mt-0.5">
                                    {{ $dataset->columns->count() }} kolom · {{ $dataset->rows()->count() }} baris · {{ $dataset->created_at->format('d M Y') }}
                                </p>
                            </div>
                            <div class="flex gap-3 flex-shrink-0 ml-4">
                                <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs text-slate-500 hover:text-slate-700 font-medium">Data</a>
                                <a href="{{ route('analysis.create', $dataset->id) }}" class="text-xs font-medium" style="color: #0F2C59;">Analisis</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Panel Kanan --}}
        <div class="space-y-4">

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-700 sw-display">Aksi Cepat</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    <a href="{{ route('datasets.index') }}" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition group">
                        <span class="text-sm text-slate-700">Upload Dataset Baru</span>
                        <span class="text-sm transition group-hover:translate-x-0.5 inline-block" style="color: #F6C90E;">→</span>
                    </a>
                    @if ($latestDataset)
                        <a href="{{ route('analysis.create', $latestDataset->id) }}" class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition group">
                            <span class="text-sm text-slate-700">Analisis Dataset Terakhir</span>
                            <span class="text-sm transition group-hover:translate-x-0.5 inline-block" style="color: #F6C90E;">→</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="rounded-xl overflow-hidden" style="background: #0F2C59;">
                <div class="px-5 py-4 border-b border-white/10">
                    <h3 class="text-sm font-semibold text-white sw-display">Fitur Analisis</h3>
                </div>
                <div class="px-5 py-4 space-y-2">
                    @foreach ([
                        'Statistik Deskriptif',
                        'Distribusi Frekuensi',
                        'Uji Normalitas',
                        'Uji-t (3 jenis)',
                        'One-Way ANOVA',
                        'Korelasi Pearson & Spearman',
                        'Chi-square Test',
                        'Regresi Linear',
                    ] as $fitur)
                        <div class="flex items-center gap-2 text-xs text-slate-300 hover:text-white transition">
                            <span class="w-1 h-1 rounded-full flex-shrink-0" style="background: #F6C90E;"></span>
                            {{ $fitur }}
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <script>
        document.querySelectorAll('.counter').forEach(el => {
            const target = parseInt(el.dataset.target, 10);
            if (isNaN(target) || target === 0) { el.textContent = '0'; return; }
            const duration = 700;
            const start = performance.now();
            function tick(now) {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target).toLocaleString('id-ID');
                if (progress < 1) requestAnimationFrame(tick);
                else el.textContent = target.toLocaleString('id-ID');
            }
            requestAnimationFrame(tick);
        });
    </script>
</x-app-layout>

