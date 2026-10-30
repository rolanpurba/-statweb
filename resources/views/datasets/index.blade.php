<x-app-layout>
    <x-slot name="header">Dataset</x-slot>

    <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
        <h3 class="text-sm font-semibold text-slate-700 sw-display mb-4">Upload Dataset Baru</h3>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-50 text-red-700 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">Nama Dataset</label>
                    <input type="text" name="name" required
                           placeholder="Contoh: Data Nilai Ujian Siswa"
                           class="w-full border-slate-300 rounded-lg text-sm focus:border-[#F6C90E] focus:ring-[#F6C90E]">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 uppercase tracking-wide mb-1.5">File (CSV / Excel)</label>
                    <input type="file" name="file" required accept=".csv,.xlsx,.xls"
                           class="w-full text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 file:cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">Format: CSV, XLSX, XLS. Maksimal 50MB.</p>
                </div>
            </div>
            <button type="submit"
                    class="text-white px-5 py-2 rounded-lg text-sm font-medium transition" style="background: #0F2C59;">
                Upload
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="text-sm font-semibold text-slate-700 sw-display">Dataset Tersimpan</h3>
        </div>

        @if ($datasets->isEmpty())
            <div class="px-6 py-12 text-center text-slate-400 text-sm">
                Belum ada dataset yang diupload.
            </div>
        @else
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead>
                    <tr class="bg-slate-50">
                        <th class="px-6 py-2 text-left text-xs font-medium text-slate-400 uppercase">Nama</th>
                        <th class="px-6 py-2 text-left text-xs font-medium text-slate-400 uppercase">Diupload</th>
                        <th class="px-6 py-2"></th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach ($datasets as $dataset)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-3.5 text-sm font-medium text-slate-800">{{ $dataset->name }}</td>
                            <td class="px-6 py-3.5 text-xs text-slate-400 sw-mono">{{ $dataset->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-3.5 text-right">
                                <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs font-medium mr-4" style="color: #0F2C59;">Lihat</a>
                                <form action="{{ route('datasets.destroy', $dataset->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-600 font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sm:hidden divide-y divide-slate-100">
                @foreach ($datasets as $dataset)
                    <div class="px-4 py-3.5">
                        <p class="text-sm font-medium text-slate-800">{{ $dataset->name }}</p>
                        <p class="text-xs text-slate-400 sw-mono mt-0.5 mb-2">{{ $dataset->created_at->format('d M Y, H:i') }}</p>
                        <div class="flex gap-4">
                            <a href="{{ route('datasets.show', $dataset->id) }}" class="text-xs font-medium" style="color: #0F2C59;">Lihat</a>
                            <form action="{{ route('datasets.destroy', $dataset->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 font-medium">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
