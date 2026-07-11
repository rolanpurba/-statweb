<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dataset Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Form Upload --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium mb-4">Upload Dataset Baru</h3>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Dataset</label>
                        <input type="text" name="name" required
                               class="w-full border-gray-300 rounded shadow-sm"
                               placeholder="Contoh: Data Nilai Ujian Siswa">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">File (CSV / Excel)</label>
                        <input type="file" name="file" required accept=".csv,.xlsx,.xls"
                               class="w-full border-gray-300 rounded shadow-sm">
                        <p class="text-xs text-gray-500 mt-1">Format: CSV, XLSX, XLS. Maksimal 10MB.</p>
                    </div>

                    <button type="submit"
                            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Upload
                    </button>
                </form>
            </div>

            {{-- List Dataset --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium mb-4">Dataset Tersimpan</h3>

                @if ($datasets->isEmpty())
                    <p class="text-gray-500">Belum ada dataset yang diupload.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Diupload</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                        @foreach ($datasets as $dataset)
                            <tr>
                                <td class="px-4 py-2">{{ $dataset->name }}</td>
                                <td class="px-4 py-2">{{ $dataset->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-2 space-x-2">
                                    <a href="{{ route('datasets.show', $dataset->id) }}"
                                       class="text-indigo-600 hover:underline">Lihat</a>
                                    <form action="{{ route('datasets.destroy', $dataset->id) }}"
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
