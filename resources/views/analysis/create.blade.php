<x-app-layout>
    <x-slot name="header">Analisis Statistik</x-slot>

    <div class="mb-4">
        <a href="{{ route('datasets.show', $dataset->id) }}" class="text-sm text-slate-500 hover:text-slate-700 font-medium">
            &larr; Kembali ke Dataset
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @php
        $card = 'bg-white rounded-xl border border-slate-200 p-5 mb-4';
        $label = 'block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5';
        $select = 'w-full border-slate-300 rounded-lg text-sm focus:border-[#F6C90E] focus:ring-[#F6C90E]';
        $btn = 'text-white px-4 py-2 rounded-lg text-sm font-medium transition w-full sm:w-auto';
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Statistik Deskriptif --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Statistik Deskriptif</h3>
            <p class="text-xs text-slate-400 mb-4">Mean, median, std dev, dll — untuk kolom numerik</p>
            @if ($numericColumns->isEmpty())
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
            @else
                <form action="{{ route('analysis.descriptive', $dataset->id) }}" method="POST">
                    @csrf
                    <select name="column" required class="{{ $select }} mb-3">
                        <option value="">-- Pilih Kolom Numerik --</option>
                        @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                    </select>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Hitung</button>
                </form>
            @endif
        </div>

        {{-- Distribusi Frekuensi --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Distribusi Frekuensi</h3>
            <p class="text-xs text-slate-400 mb-4">Frekuensi tiap kategori — untuk kolom kategorik</p>
            @if ($categoricalColumns->isEmpty())
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom kategorik.</p>
            @else
                <form action="{{ route('analysis.categorical', $dataset->id) }}" method="POST">
                    @csrf
                    <select name="column" required class="{{ $select }} mb-3">
                        <option value="">-- Pilih Kolom Kategorik --</option>
                        @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                    </select>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Hitung</button>
                </form>
            @endif
        </div>

        {{-- Scatter Plot --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Scatter Plot</h3>
            <p class="text-xs text-slate-400 mb-4">Hubungan antara 2 kolom numerik</p>
            @if ($numericColumns->count() < 2)
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
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
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Buat Scatter Plot</button>
                </form>
            @endif
        </div>

        {{-- Uji Normalitas --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji Normalitas</h3>
            <p class="text-xs text-slate-400 mb-4">Shapiro-Wilk (n&lt;50) / Kolmogorov-Smirnov (n≥50)</p>
            @if ($numericColumns->isEmpty())
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
            @else
                <form action="{{ route('analysis.normality', $dataset->id) }}" method="POST">
                    @csrf
                    <select name="column" required class="{{ $select }} mb-3">
                        <option value="">-- Pilih Kolom Numerik --</option>
                        @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                    </select>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Uji Normalitas</button>
                </form>
            @endif
        </div>

        {{-- Uji-t Satu Sampel --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji-t Satu Sampel</h3>
            <p class="text-xs text-slate-400 mb-4">Bandingkan rata-rata sampel dengan nilai tertentu</p>
            @if ($numericColumns->isEmpty())
                <p class="text-slate-400 text-sm">Dataset ini tidak memiliki kolom numerik.</p>
            @else
                <form action="{{ route('analysis.ttest_one_sample', $dataset->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="{{ $label }}">Kolom</label>
                            <select name="column" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">Nilai Uji</label>
                            <input type="number" name="test_value" step="any" required placeholder="75" class="{{ $select }}">
                        </div>
                    </div>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Jalankan</button>
                </form>
            @endif
        </div>

        {{-- Uji-t Independen --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji-t Independen</h3>
            <p class="text-xs text-slate-400 mb-4">Bandingkan 2 kelompok berbeda (tepat 2 kategori)</p>
            @if ($numericColumns->isEmpty() || $categoricalColumns->isEmpty())
                <p class="text-slate-400 text-sm">Butuh minimal 1 kolom numerik & 1 kategorik.</p>
            @else
                <form action="{{ route('analysis.ttest_independent', $dataset->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="{{ $label }}">Nilai</label>
                            <select name="column_value" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">Kelompok</label>
                            <select name="column_group" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Jalankan</button>
                </form>
            @endif
        </div>

        {{-- Uji-t Berpasangan --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Uji-t Berpasangan</h3>
            <p class="text-xs text-slate-400 mb-4">2 pengukuran pada subjek yang sama</p>
            @if ($numericColumns->count() < 2)
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
            @else
                <form action="{{ route('analysis.ttest_paired', $dataset->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="{{ $label }}">Sebelum</label>
                            <select name="column_1" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">Sesudah</label>
                            <select name="column_2" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Jalankan</button>
                </form>
            @endif
        </div>

        {{-- ANOVA --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">One-Way ANOVA</h3>
            <p class="text-xs text-slate-400 mb-4">Bandingkan rata-rata 3+ kelompok sekaligus</p>
            @if ($numericColumns->isEmpty() || $categoricalColumns->isEmpty())
                <p class="text-slate-400 text-sm">Butuh minimal 1 kolom numerik & 1 kategorik.</p>
            @else
                <form action="{{ route('analysis.anova', $dataset->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="{{ $label }}">Nilai</label>
                            <select name="column_value" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">Kelompok</label>
                            <select name="column_group" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Jalankan ANOVA</button>
                </form>
            @endif
        </div>

        {{-- Korelasi --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Korelasi</h3>
            <p class="text-xs text-slate-400 mb-4">Pearson atau Spearman antara 2 kolom numerik</p>
            @if ($numericColumns->count() < 2)
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
            @else
                <form action="{{ route('analysis.correlation', $dataset->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="{{ $label }}">Kolom X</label>
                            <select name="column_x" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">Kolom Y</label>
                            <select name="column_y" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-5 mb-3 text-sm">
                        <label class="flex items-center gap-1.5 text-slate-600">
                            <input type="radio" name="method" value="pearson" checked class="focus:ring-[#F6C90E]"> Pearson
                        </label>
                        <label class="flex items-center gap-1.5 text-slate-600">
                            <input type="radio" name="method" value="spearman" class="focus:ring-[#F6C90E]"> Spearman
                        </label>
                    </div>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Hitung Korelasi</button>
                </form>
            @endif
        </div>

        {{-- Chi-square --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Chi-square Test</h3>
            <p class="text-xs text-slate-400 mb-4">Hubungan antara 2 variabel kategorik</p>
            @if ($categoricalColumns->count() < 2)
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom kategorik.</p>
            @else
                <form action="{{ route('analysis.chi_square', $dataset->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="{{ $label }}">Variabel 1</label>
                            <select name="column_1" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="{{ $label }}">Variabel 2</label>
                            <select name="column_2" required class="{{ $select }}">
                                <option value="">--</option>
                                @foreach ($categoricalColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Jalankan</button>
                </form>
            @endif
        </div>

        {{-- Regresi Sederhana --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Regresi Linear Sederhana</h3>
            <p class="text-xs text-slate-400 mb-4">1 variabel X memprediksi 1 variabel Y</p>
            @if ($numericColumns->count() < 2)
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 2 kolom numerik.</p>
            @else
                <form action="{{ route('analysis.regression_simple', $dataset->id) }}" method="POST">
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
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Jalankan</button>
                </form>
            @endif
        </div>

        {{-- Regresi Berganda --}}
        <div class="{{ $card }}">
            <h3 class="text-sm font-semibold text-slate-800 sw-display mb-1">Regresi Linear Berganda</h3>
            <p class="text-xs text-slate-400 mb-4">2+ variabel X memprediksi 1 variabel Y</p>
            @if ($numericColumns->count() < 3)
                <p class="text-slate-400 text-sm">Dibutuhkan minimal 3 kolom numerik.</p>
            @else
                <form action="{{ route('analysis.regression_multiple', $dataset->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="{{ $label }}">Variabel Y</label>
                        <select name="column_y" required class="{{ $select }}">
                            <option value="">--</option>
                            @foreach ($numericColumns as $col)<option value="{{ $col->column_name }}">{{ $col->column_name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="{{ $label }}">Variabel X (pilih 2+)</label>
                        <div class="space-y-1.5 p-3 border border-slate-200 rounded-lg max-h-32 overflow-y-auto">
                            @foreach ($numericColumns as $col)
                                <label class="flex items-center gap-2 text-sm text-slate-600">
                                    <input type="checkbox" name="columns_x[]" value="{{ $col->column_name }}" class="rounded focus:ring-[#F6C90E]">
                                    {{ $col->column_name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="{{ $btn }}" style="background: #0F2C59;">Jalankan</button>
                </form>
            @endif
        </div>

    </div>
</x-app-layout>
