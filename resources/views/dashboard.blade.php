<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <p class="text-gray-500 text-sm mb-6">
                Selamat datang, <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
            </p>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-indigo-500">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Dataset Tersimpan</p>
                    <p class="text-3xl font-semibold text-gray-800">{{ $totalDatasets }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-green-500">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Total Baris Data</p>
                    <p class="text-3xl font-semibold text-gray-800">{{ number_format($totalRows) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-gray-300">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Upload Terakhir</p>
                    <p class="text-sm font-medium text-gray-700 mt-2">
                        {{ $latestDataset ? $latestDataset->created_at->diffForHumans() : '—' }}
                    </p>
                    @if ($latestDataset)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $latestDataset->name }}</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Dataset Terbaru --}}
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Dataset Terbaru</h3>
                        <a href="{{ route('datasets.index') }}" class="text-xs text-indigo-600 hover:underline">
                            Lihat semua
                        </a>
                    </div>

                    @if ($recentDatasets->isEmpty())
                        <div class="px-6 py-10 text-center text-gray-400 text-sm">
                            Belum ada dataset. Upload file CSV atau Excel untuk memulai.
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Nama</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Kolom</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Baris</th>
                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-400 uppercase">Diupload</th>
                                <th class="px-6 py-2"></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach ($recentDatasets as $dataset)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-800">
                                        {{ $dataset->name }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-500">
                                        {{ $dataset->columns->count() }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-500">
                                        {{ $dataset->rows()->count() }}
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-400">
                                        {{ $dataset->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-3 text-right">
                                        <a href="{{ route('datasets.show', $dataset->id) }}"
                                           class="text-xs text-indigo-600 hover:underline mr-3">Data</a>
                                        <a href="{{ route('analysis.create', $dataset->id) }}"
                                           class="text-xs text-green-600 hover:underline">Analisis</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Panel Kanan --}}
                <div class="space-y-4">

                    {{-- Quick Actions --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Aksi Cepat</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <a href="{{ route('datasets.index') }}"
                               class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                                <span class="text-sm text-gray-700">Upload Dataset Baru</span>
                                <span class="text-gray-300 text-sm">→</span>
                            </a>
                            @if ($latestDataset)
                                <a href="{{ route('analysis.create', $latestDataset->id) }}"
                                   class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                                    <span class="text-sm text-gray-700">Analisis Dataset Terakhir</span>
                                    <span class="text-gray-300 text-sm">→</span>
                                </a>
                            @endif
                            <a href="{{ route('datasets.index') }}"
                               class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
                                <span class="text-sm text-gray-700">Kelola Dataset</span>
                                <span class="text-gray-300 text-sm">→</span>
                            </a>
                        </div>
                    </div>

                    {{-- Fitur Tersedia --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Fitur Analisis</h3>
                        </div>
                        <div class="px-6 py-4">
                            <div class="space-y-1.5">
                                @foreach ([
                                    'Statistik Deskriptif',
                                    'Distribusi Frekuensi',
                                    'Uji Normalitas (Shapiro-Wilk / KS)',
                                    'Uji-t (Satu Sampel, Independen, Berpasangan)',
                                    'One-Way ANOVA',
                                    'Korelasi Pearson & Spearman',
                                    'Chi-square Test',
                                    'Regresi Linear Sederhana & Berganda',
                                ] as $fitur)
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></span>
                                        {{ $fitur }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
