<x-app-layout>
    <x-slot name="header">Analisis Statistik</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('datasets.show', $dataset->id) }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-800 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dataset ({{ $dataset->name }})
        </a>
        <a href="{{ route('analysis.history', $dataset->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-sw-navy hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Riwayat Analisis
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 px-4 py-3 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @php
        $card = 'bg-white rounded-xl border border-slate-200 p-5 shadow-sm transition hover:border-slate-300 hover:shadow';
        $label = 'block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5';
        $select = 'w-full border-slate-300 rounded-lg text-sm focus:border-sw-gold focus:ring-sw-gold transition';
        $btn = 'bg-sw-navy hover:bg-sw-navy-light text-white px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm w-full sm:w-auto cursor-pointer';
    @endphp

    {{-- 1. DESKRIPTIF & VISUALISASI --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-sw-gold"></span>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-display">1. Statistik Deskriptif & Visualisasi</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Statistik Deskriptif --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Statistik Deskriptif</h3>
                <p class="text-xs text-slate-400 mb-4">Mean, median, modus, std dev, range, kuartil</p>
                @if ($numericColumns->isEmpty())
                    <p class="text-slate-400 text-xs italic">Dataset ini tidak memiliki kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.descriptive', $dataset->id) }}" method="POST">
                        @csrf
                        <select name="column" required class="{{ $select }} mb-3">
                            <option value="">-- Pilih Kolom Numerik --</option>
                            @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                        </select>
                        <button type="submit" class="{{ $btn }}">Hitung Deskriptif</button>
                    </form>
                @endif
            </div>

            {{-- Distribusi Frekuensi --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Distribusi Frekuensi</h3>
                <p class="text-xs text-slate-400 mb-4">Tabel frekuensi dan persentase kategori</p>
                @if ($categoricalColumns->isEmpty())
                    <p class="text-slate-400 text-xs italic">Dataset ini tidak memiliki kolom kategorik.</p>
                @else
                    <form action="{{ route('analysis.categorical', $dataset->id) }}" method="POST">
                        @csrf
                        <select name="column" required class="{{ $select }} mb-3">
                            <option value="">-- Pilih Kolom Kategorik --</option>
                            @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                        </select>
                        <button type="submit" class="{{ $btn }}">Hitung Frekuensi</button>
                    </form>
                @endif
            </div>

            {{-- Scatter Plot --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Scatter Plot</h3>
                <p class="text-xs text-slate-400 mb-4">Plot sebaran hubungan antara 2 kolom numerik</p>
                @if ($numericColumns->count() < 2)
                    <p class="text-slate-400 text-xs italic">Dibutuhkan minimal 2 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.scatter', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Sumbu X</label>
                                <select name="column_x" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Sumbu Y</label>
                                <select name="column_y" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Buat Scatter Plot</button>
                    </form>
                @endif
            </div>

        </div>
    </div>

    {{-- 2. UJI HIPOTESIS & KOMPARASI --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-sw-gold"></span>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-display">2. Uji Hipotesis & Komparasi Rata-rata</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Uji Normalitas --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Uji Normalitas</h3>
                <p class="text-xs text-slate-400 mb-4">Shapiro-Wilk (N &lt; 50) / Kolmogorov-Smirnov (N &ge; 50)</p>
                @if ($numericColumns->isEmpty())
                    <p class="text-slate-400 text-xs italic">Dataset ini tidak memiliki kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.normality', $dataset->id) }}" method="POST">
                        @csrf
                        <select name="column" required class="{{ $select }} mb-3">
                            <option value="">-- Pilih Kolom Numerik --</option>
                            @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                        </select>
                        <button type="submit" class="{{ $btn }}">Uji Normalitas</button>
                    </form>
                @endif
            </div>

            {{-- Uji-t Satu Sampel --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Uji-t Satu Sampel (One-Sample t-Test)</h3>
                <p class="text-xs text-slate-400 mb-4">Membandingkan rata-rata sampel dengan konstanta nilai uji</p>
                @if ($numericColumns->isEmpty())
                    <p class="text-slate-400 text-xs italic">Dataset ini tidak memiliki kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.ttest_one_sample', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Kolom Numerik</label>
                                <select name="column" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Nilai Uji (Test Value)</label>
                                <input type="number" name="test_value" step="any" required placeholder="75" class="{{ $select }}">
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Jalankan Uji-t</button>
                    </form>
                @endif
            </div>

            {{-- Uji-t Independen --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Uji-t Independen (Two-Sample t-Test)</h3>
                <p class="text-xs text-slate-400 mb-4">Membandingkan rata-rata antara 2 kelompok yang berbeda</p>
                @if ($numericColumns->isEmpty() || $categoricalColumns->isEmpty())
                    <p class="text-slate-400 text-xs italic">Butuh minimal 1 kolom numerik &amp; 1 kolom kategorik.</p>
                @else
                    <form action="{{ route('analysis.ttest_independent', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Kolom Nilai</label>
                                <select name="column_value" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Kolom Kelompok (2 Kategori)</label>
                                <select name="column_group" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Jalankan Uji-t Independen</button>
                    </form>
                @endif
            </div>

            {{-- Uji-t Berpasangan --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Uji-t Berpasangan (Paired t-Test)</h3>
                <p class="text-xs text-slate-400 mb-4">Membandingkan 2 pengukuran berpasangan (misal Pre vs Post)</p>
                @if ($numericColumns->count() < 2)
                    <p class="text-slate-400 text-xs italic">Dibutuhkan minimal 2 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.ttest_paired', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Sebelum (Pre)</label>
                                <select name="column_1" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Sesudah (Post)</label>
                                <select name="column_2" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Jalankan Uji-t Berpasangan</button>
                    </form>
                @endif
            </div>

            {{-- ANOVA --}}
            <div class="{{ $card }} lg:col-span-2">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">One-Way ANOVA (Analisis Variansi Satu Arah)</h3>
                <p class="text-xs text-slate-400 mb-4">Membandingkan rata-rata dari 3 kelompok perlakuan atau lebih secara simultan</p>
                @if ($numericColumns->isEmpty() || $categoricalColumns->isEmpty())
                    <p class="text-slate-400 text-xs italic">Butuh minimal 1 kolom numerik &amp; 1 kolom kategorik.</p>
                @else
                    <form action="{{ route('analysis.anova', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Variabel Dependen (Nilai)</label>
                                <select name="column_value" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Faktor / Kelompok (&ge; 2 Kategori)</label>
                                <select name="column_group" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Jalankan One-Way ANOVA</button>
                    </form>
                @endif
            </div>

        </div>
    </div>

    {{-- 3. KORELASI & PEMODELAN REGRESI --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-sw-gold"></span>
            <h2 class="text-xs font-semibold uppercase tracking-wider text-slate-500 font-display">3. Korelasi &amp; Pemodelan Regresi</h2>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- Korelasi --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Uji Korelasi</h3>
                <p class="text-xs text-slate-400 mb-4">Mengukur kekuatan dan arah hubungan linier antar 2 variabel</p>
                @if ($numericColumns->count() < 2)
                    <p class="text-slate-400 text-xs italic">Dibutuhkan minimal 2 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.correlation', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Variabel X</label>
                                <select name="column_x" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Variabel Y</label>
                                <select name="column_y" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex gap-5 mb-3 text-sm">
                            <label class="flex items-center gap-1.5 text-slate-600 text-xs font-medium cursor-pointer">
                                <input type="radio" name="method" value="pearson" checked class="text-sw-navy focus:ring-sw-gold"> Pearson (Parametrik)
                            </label>
                            <label class="flex items-center gap-1.5 text-slate-600 text-xs font-medium cursor-pointer">
                                <input type="radio" name="method" value="spearman" class="text-sw-navy focus:ring-sw-gold"> Spearman (Non-parametrik)
                            </label>
                        </div>
                        <button type="submit" class="{{ $btn }}">Hitung Korelasi</button>
                    </form>
                @endif
            </div>

            {{-- Chi-square --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Uji Independensi Chi-Square</h3>
                <p class="text-xs text-slate-400 mb-4">Uji hubungan antar 2 variabel kategorik + ukuran asosiasi Cram&eacute;r's V</p>
                @if ($categoricalColumns->count() < 2)
                    <p class="text-slate-400 text-xs italic">Dibutuhkan minimal 2 kolom kategorik.</p>
                @else
                    <form action="{{ route('analysis.chi_square', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Kategori Baris (X)</label>
                                <select name="column_1" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Kategori Kolom (Y)</label>
                                <select name="column_2" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Jalankan Chi-Square</button>
                    </form>
                @endif
            </div>

            {{-- Regresi Sederhana --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Regresi Linear Sederhana</h3>
                <p class="text-xs text-slate-400 mb-4">Memprediksi variabel dependen Y dari 1 variabel independen X</p>
                @if ($numericColumns->count() < 2)
                    <p class="text-slate-400 text-xs italic">Dibutuhkan minimal 2 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.regression_simple', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="{{ $label }}">Variabel Independen (X)</label>
                                <select name="column_x" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                            <div>
                                <label class="{{ $label }}">Variabel Dependen (Y)</label>
                                <select name="column_y" required class="{{ $select }}">
                                    <option value="">--</option>
                                    @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Estimasi Regresi Sederhana</button>
                    </form>
                @endif
            </div>

            {{-- Regresi Berganda --}}
            <div class="{{ $card }}">
                <h3 class="text-sm font-semibold text-slate-800 font-display mb-1">Regresi Linear Berganda</h3>
                <p class="text-xs text-slate-400 mb-4">Memprediksi variabel Y dari 2 atau lebih variabel independen X</p>
                @if ($numericColumns->count() < 3)
                    <p class="text-slate-400 text-xs italic">Dibutuhkan minimal 3 kolom numerik.</p>
                @else
                    <form action="{{ route('analysis.regression_multiple', $dataset->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="{{ $label }}">Variabel Dependen (Y)</label>
                            <select name="column_y" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="{{ $label }}">Variabel Independen (Pilih minimal 2 X)</label>
                            <div class="space-y-1.5 p-3 border border-slate-200 rounded-lg max-h-36 overflow-y-auto bg-slate-50/50">
                                @foreach ($numericColumns as $col)
                                    <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="columns_x[]" value="{{ $col->column_name }}" class="rounded text-sw-navy focus:ring-sw-gold">
                                        <span class="font-mono font-medium">{{ $col->column_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="{{ $btn }}">Estimasi Regresi Berganda</button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
