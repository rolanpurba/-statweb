<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Analisis Statistik — {{ $dataset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-4">
                <a href="{{ route('datasets.show', $dataset->id) }}" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Dataset
                </a>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Statistik Deskriptif --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Statistik Deskriptif</h3>
                <p class="text-sm text-gray-500 mb-4">Mean, median, std dev, dll — untuk kolom numerik</p>
                @if ($numericColumns->isEmpty())
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.descriptive', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                <option value="">-- Pilih Kolom Numerik --</option>
                                @foreach ($numericColumns as $col)
                                    <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Hitung Statistik Deskriptif
                        </button>
                    </form>
                @endif
            </div>

            {{-- Distribusi Frekuensi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Distribusi Frekuensi</h3>
                <p class="text-sm text-gray-500 mb-4">Jumlah kemunculan tiap kategori — untuk kolom kategorik</p>
                @if ($categoricalColumns->isEmpty())
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom kategorik.</p>
                @else
                    <form action="{{ route('analysis.categorical', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                <option value="">-- Pilih Kolom Kategorik --</option>
                                @foreach ($categoricalColumns as $col)
                                    <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Hitung Distribusi Frekuensi
                        </button>
                    </form>
                @endif
            </div>

            {{-- Scatter Plot --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Scatter Plot</h3>
                <p class="text-sm text-gray-500 mb-4">Lihat hubungan antara 2 kolom numerik</p>
                @if ($numericColumns->count() < 2)
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.scatter', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sumbu X</label>
                                <select name="column_x" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sumbu Y</label>
                                <select name="column_y" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                            Buat Scatter Plot
                        </button>
                    </form>
                @endif
            </div>

            {{-- Uji Normalitas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Uji Normalitas</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Shapiro-Wilk (n &lt; 50) atau Kolmogorov-Smirnov (n ≥ 50) — prasyarat sebelum uji parametrik
                </p>
                @if ($numericColumns->isEmpty())
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.normality', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                <option value="">-- Pilih Kolom Numerik --</option>
                                @foreach ($numericColumns as $col)
                                    <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">
                            Uji Normalitas
                        </button>
                    </form>
                @endif
            </div>

            {{-- Uji-t Satu Sampel --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Uji-t Satu Sampel</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bandingkan rata-rata sampel dengan nilai tertentu (misal: apakah rata-rata = 75?)
                </p>
                @if ($numericColumns->isEmpty())
                    <p class="text-gray-500 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.ttest_one_sample', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Numerik</label>
                                <select name="column" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Uji (Test Value)</label>
                                <input type="number" name="test_value" step="any" required
                                       placeholder="contoh: 75"
                                       class="w-full border-gray-300 rounded shadow-sm">
                            </div>
                        </div>
                        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                            Jalankan Uji-t Satu Sampel
                        </button>
                    </form>
                @endif
            </div>

            {{-- Uji-t Independen --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Uji-t Independen</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bandingkan rata-rata 2 kelompok berbeda (kolom kelompok harus memiliki tepat 2 kategori)
                </p>
                @if ($numericColumns->isEmpty() || $categoricalColumns->isEmpty())
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 1 kolom numerik dan 1 kolom kategorik (2 kategori).</p>
                @else
                    <form action="{{ route('analysis.ttest_independent', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Nilai (Numerik)</label>
                                <select name="column_value" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Kelompok (Kategorik)</label>
                                <select name="column_group" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($categoricalColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-rose-500 text-white px-4 py-2 rounded hover:bg-rose-600">
                            Jalankan Uji-t Independen
                        </button>
                    </form>
                @endif
            </div>

            {{-- Uji-t Berpasangan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Uji-t Berpasangan</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bandingkan 2 pengukuran pada subjek yang sama (misal: nilai sebelum vs sesudah treatment)
                </p>
                @if ($numericColumns->count() < 2)
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.ttest_paired', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom 1 (Sebelum)</label>
                                <select name="column_1" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom 2 (Sesudah)</label>
                                <select name="column_2" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-teal-500 text-white px-4 py-2 rounded hover:bg-teal-600">
                            Jalankan Uji-t Berpasangan
                        </button>
                    </form>
                @endif
            </div>

            {{-- ANOVA --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">One-Way ANOVA</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Bandingkan rata-rata 3+ kelompok sekaligus (kolom kelompok minimal 2 kategori)
                </p>
                @if ($numericColumns->isEmpty() || $categoricalColumns->isEmpty())
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 1 kolom numerik dan 1 kolom kategorik.</p>
                @else
                    <form action="{{ route('analysis.anova', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Nilai (Numerik)</label>
                                <select name="column_value" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Kelompok (Kategorik)</label>
                                <select name="column_group" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($categoricalColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-cyan-600 text-white px-4 py-2 rounded hover:bg-cyan-700">
                            Jalankan ANOVA
                        </button>
                    </form>
                @endif
            </div>

            {{-- Korelasi --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-1">Korelasi</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Ukur kekuatan hubungan antara 2 kolom numerik (Pearson atau Spearman)
                </p>
                @if ($numericColumns->count() < 2)
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.correlation', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom X</label>
                                <select name="column_x" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kolom Y</label>
                                <select name="column_y" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($numericColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode</label>
                            <div class="flex gap-6">
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="method" value="pearson" checked> Pearson (data normal)
                                </label>
                                <label class="flex items-center gap-2 text-sm">
                                    <input type="radio" name="method" value="spearman"> Spearman (data tidak normal / ordinal)
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Hitung Korelasi
                        </button>
                    </form>
                @endif
            </div>

            {{-- Chi-square --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-1">Chi-square Test</h3>
                <p class="text-sm text-gray-500 mb-4">
                    Uji hubungan antara 2 variabel kategorik
                </p>
                @if ($categoricalColumns->count() < 2)
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 2 kolom kategorik.</p>
                @else
                    <form action="{{ route('analysis.chi_square', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Variabel 1</label>
                                <select name="column_1" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($categoricalColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Variabel 2</label>
                                <select name="column_2" required class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="">-- Pilih Kolom --</option>
                                    @foreach ($categoricalColumns as $col)
                                        <option value="{{ $col->column_name }}">{{ $col->column_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="bg-pink-600 text-white px-4 py-2 rounded hover:bg-pink-700">
                            Jalankan Chi-square Test
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
