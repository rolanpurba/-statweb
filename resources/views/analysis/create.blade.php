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

            {{-- Statistik Deskriptif (Kolom Numerik) --}}
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
                        <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Hitung Statistik Deskriptif
                        </button>
                    </form>
                @endif
            </div>

            {{-- Frekuensi Kategori (Kolom Kategorik) --}}
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
                        <button type="submit"
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                            Hitung Distribusi Frekuensi
                        </button>
                    </form>
                @endif
            </div>

            {{-- Scatter Plot (2 Kolom Numerik) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-1">Scatter Plot</h3>
                <p class="text-sm text-gray-500 mb-4">Lihat hubungan antara 2 kolom numerik</p>

                @if ($numericColumns->count() < 2)
                    <p class="text-gray-500 text-sm">Dibutuhkan minimal 2 kolom numerik untuk membuat scatter plot.</p>
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
                        <button type="submit"
                                class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                            Buat Scatter Plot
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
