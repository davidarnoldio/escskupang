<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight flex items-center gap-2">
                    <span class="p-2 bg-red-100 text-red-600 rounded-2xl text-xl">📄</span>
                    <span>Surat Edaran & Pengumuman Sekolah</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Dokumen resmi, surat pemberitahuan, dan berkas PDF dari pihak sekolah untuk orang tua / wali murid.</p>
            </div>
            @if($student)
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-red-50 text-red-900 font-bold text-xs rounded-xl border border-red-200">
                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                    <span>Anak: {{ $student->nama }} ({{ $student->formatted_kelas }})</span>
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-2 shadow-xs">
                    <span>✅</span>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Search and Filter Bar -->
            <div class="bg-white rounded-3xl p-5 shadow-xs border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold text-slate-700">Arsip Surat Edaran & Dokumen Resmi PDF</span>
                </div>

                <form method="GET" action="{{ route('parent.announcements') }}" class="flex items-center gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari surat / judul..."
                               class="pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 w-60">
                        <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
                    </div>
                    <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('parent.announcements') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Documents Grid -->
            @if($announcements->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center shadow-xs border border-slate-200/80">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3">
                        📭
                    </div>
                    <h3 class="font-bold text-base text-slate-800">Belum Ada Surat Edaran / Pengumuman</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        Saat ini belum ada dokumen PDF surat edaran atau pengumuman yang diterbitkan oleh pihak sekolah untuk kelas anak Anda.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($announcements as $doc)
                        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between hover:shadow-md hover:border-red-300 transition duration-200">
                            <div>
                                <!-- Top Badges -->
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-black text-sm shrink-0 border border-red-100 shadow-xs">
                                        PDF
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $doc->target_class === 'Semua Kelas' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                            {{ $doc->target_class === 'Semua Kelas' ? '📢 Seluruh Wali Murid' : '🏫 Khusus Kelas ' . $doc->target_class }}
                                        </span>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $doc->created_at->isoFormat('D MMMM Y') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Title & Description -->
                                <h3 class="font-bold text-base text-slate-900 leading-snug mb-2 line-clamp-2">
                                    {{ $doc->title }}
                                </h3>

                                @if($doc->description)
                                    <p class="text-xs text-slate-600 leading-relaxed mb-4 line-clamp-3 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                        {{ $doc->description }}
                                    </p>
                                @endif

                                <!-- File Metadata -->
                                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-1 mb-5 text-[11px] text-slate-500">
                                    <div class="flex items-center justify-between font-mono">
                                        <span class="truncate max-w-[190px] text-slate-700 font-semibold" title="{{ $doc->file_name }}">📎 {{ $doc->file_name }}</span>
                                        <span class="text-slate-500 font-bold">{{ $doc->formatted_file_size }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                                <a href="{{ route('school-announcements.download', $doc) }}"
                                   class="flex-1 py-2.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold rounded-2xl shadow-md shadow-red-600/30 text-center transition inline-flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh Surat PDF</span>
                                </a>

                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                   class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl transition" title="Buka di Tab Baru">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
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
