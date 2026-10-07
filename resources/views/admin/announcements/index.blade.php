<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 leading-tight flex items-center gap-2">
                    <span class="p-2 bg-red-100 text-red-600 rounded-xl">📄</span>
                    <span>Surat Edaran & Pengumuman Sekolah (PDF)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Upload dan publikasikan dokumen resmi PDF dari Admin langsung ke portal seluruh orang tua siswa.</p>
            </div>
            <a href="#form-upload" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold rounded-xl shadow-md shadow-red-600/30 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Upload Berkas PDF Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 space-y-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Flash Notification -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-500 text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl text-xs font-bold space-y-1 shadow-xs">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-red-500 text-base">⚠️</span>
                    <span>Terdapat kesalahan pada formulir upload:</span>
                </div>
                <ul class="list-disc pl-6 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM UPLOAD PDF -->
        <div id="form-upload" class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg">
                    📤
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">Upload Dokumen PDF ke Orang Tua</h3>
                    <p class="text-xs text-slate-500">Unggah berkas surat edaran, kalender pendidikan, atau panduan resmi untuk dapat diunduh orang tua.</p>
                </div>
            </div>

            <form action="{{ route('school-announcements.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Judul Surat / Pengumuman -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Judul Surat / Dokumen <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                               placeholder="Contoh: Surat Edaran Kegiatan Tengah Semester & Libur Hari Raya 2026"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                    </div>

                    <!-- Target Penerima (Kelas / Semua) -->
                    <div>
                        <label for="target_class" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Target Penerima Orang Tua <span class="text-red-500">*</span>
                        </label>
                        <select id="target_class" name="target_class" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <option value="Semua Kelas" {{ old('target_class') == 'Semua Kelas' ? 'selected' : '' }}>📢 Seluruh Orang Tua Siswa (Semua Kelas)</option>
                            @foreach($availableClasses as $cls)
                                <option value="{{ $cls }}" {{ old('target_class') == $cls ? 'selected' : '' }}>
                                    🏫 Khusus Kelas {{ $cls }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">Pilih "Semua Kelas" agar dapat dibaca oleh seluruh wali murid sekolah.</p>
                    </div>

                    <!-- Input File PDF -->
                    <div>
                        <label for="pdf_file" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Berkas PDF <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" id="pdf_file" name="pdf_file" accept=".pdf,application/pdf" required
                                   class="block w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer p-1.5 focus:outline-none">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Hanya format <strong class="text-red-600">.PDF</strong>, maksimal ukuran file 15 MB.</p>
                    </div>

                    <!-- Keterangan / Deskripsi Tambahan -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Keterangan / Ringkasan Isi Surat (Opsional)
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  placeholder="Tuliskan catatan singkat atau instruksi penting kepada orang tua perihal berkas ini..."
                                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="reset" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        Reset Form
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Publikasikan Dokumen PDF</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- DAFTAR PENGUMUMAN & BERKAS PDF -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span>📚</span>
                        <span>Daftar Dokumen PDF yang Telah Dipublikasikan</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Seluruh berkas di bawah ini dapat diakses dan diunduh oleh akun orang tua.</p>
                </div>

                <!-- Search & Filter Form -->
                <form method="GET" action="{{ route('school-announcements.index') }}" class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / file..."
                               class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 w-48">
                        <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                    </div>

                    <select name="target_class" onchange="this.form.submit()" class="py-1.5 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500">
                        <option value="all">Semua Target</option>
                        <option value="Semua Kelas" {{ request('target_class') == 'Semua Kelas' ? 'selected' : '' }}>Semua Kelas</option>
                        @foreach($availableClasses as $cls)
                            <option value="{{ $cls }}" {{ request('target_class') == $cls ? 'selected' : '' }}>Kelas {{ $cls }}</option>
                        @endforeach
                    </select>

                    @if(request('search') || (request('target_class') && request('target_class') !== 'all'))
                        <a href="{{ route('school-announcements.index') }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold" title="Reset filter">
                            ✕
                        </a>
                    @endif
                </form>
            </div>

            @if($announcements->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3">
                        📁
                    </div>
                    <h4 class="font-bold text-sm text-slate-800">Belum Ada Dokumen PDF</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Silakan gunakan formulir di atas untuk mengunggah surat edaran atau dokumen pengumuman sekolah pertama Anda.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($announcements as $item)
                        <div class="bg-gradient-to-b from-white to-slate-50/50 rounded-2xl border border-slate-200/90 p-5 flex flex-col justify-between hover:border-red-300 hover:shadow-md transition duration-200">
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-xl bg-red-100/70 text-red-600 flex items-center justify-center font-black text-sm shrink-0">
                                        PDF
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $item->target_class === 'Semua Kelas' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                        {{ $item->target_class === 'Semua Kelas' ? '📢 Semua Kelas' : '🏫 Kelas ' . $item->target_class }}
                                    </span>
                                </div>

                                <h4 class="font-bold text-sm text-slate-900 leading-snug line-clamp-2 mb-1.5">
                                    {{ $item->title }}
                                </h4>

                                @if($item->description)
                                    <p class="text-xs text-slate-600 line-clamp-3 mb-3 leading-relaxed">
                                        {{ $item->description }}
                                    </p>
                                @endif

                                <div class="p-2.5 bg-white rounded-xl border border-slate-100 space-y-1 mb-4 text-[11px] text-slate-500">
                                    <div class="flex items-center justify-between font-mono">
                                        <span class="truncate max-w-[180px] text-slate-700 font-semibold" title="{{ $item->file_name }}">📎 {{ $item->file_name }}</span>
                                        <span class="text-slate-400 font-bold">{{ $item->formatted_file_size }}</span>
                                    </div>
                                    <div class="flex items-center justify-between pt-1 border-t border-slate-50 text-[10px] text-slate-400">
                                        <span>Diupload: {{ $item->created_at->isoFormat('D MMM Y, HH:mm') }}</span>
                                        <span>Oleh: {{ $item->creator ? $item->creator->name : 'Admin' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                <a href="{{ route('school-announcements.download', $item) }}"
                                   class="flex-1 px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition text-center inline-flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh PDF</span>
                                </a>

                                <form action="{{ route('school-announcements.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat edaran ini? File PDF akan dihapus secara permanen.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition" title="Hapus Dokumen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
