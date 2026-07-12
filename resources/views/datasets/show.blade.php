<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $dataset->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex justify-between items-center">
                <a href="{{ route('datasets.index') }}" class="text-indigo-600 hover:underline">
                    &larr; Kembali ke Daftar Dataset
                </a>
                <a href="{{ route('analysis.create', $dataset->id) }}"
                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Analisis Statistik Deskriptif
                </a>
            </div>

            {{-- Info Kolom & Tipe Data --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">Informasi Kolom</h3>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Kolom</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipe Data</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                    @foreach ($columns as $column)
                        <tr>
                            <td class="px-4 py-2">{{ $column->column_name }}</td>
                            <td class="px-4 py-2">
                                @if ($column->data_type === 'numeric')
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">Numerik</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">Kategorik</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Preview Data --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Preview Data (50 baris pertama)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                        <tr>
                            @foreach ($headerRow as $head)
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                                    {{ $head }}
                                </th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($dataRows as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
