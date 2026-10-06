<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Catatan Harian Guru</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Buku penghubung perkembangan & pembelajaran harian Ananda di sekolah</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('parent.dashboard') }}"
                   class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                    <span>← Kembali ke Portal Utama</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Child Summary Header Card -->
            <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 text-white rounded-3xl p-6 shadow-xl border border-red-500/30 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-red-600/10 rounded-full blur-xl pointer-events-none"></div>

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-800 text-red-400 font-black text-2xl flex items-center justify-center border-2 border-red-400/40 shadow-md shrink-0 overflow-hidden">
                            @if($student->foto_url)
                                <img src="{{ $student->foto_url }}" alt="{{ $student->nama }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($student->nama, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-red-400">BUKU PENGHUBUNG DIGITAL</span>
                            <h3 class="text-xl font-extrabold text-white leading-tight mt-0.5">{{ $student->nama }}</h3>
                            <div class="flex items-center gap-2 mt-1 flex-wrap text-xs text-slate-300">
                                <span class="px-2.5 py-0.5 bg-white/10 rounded-full font-semibold">Kelas {{ $student->kelas }}</span>
                                <span>&bull;</span>
                                <span class="font-mono text-red-300">NISN: {{ $student->nisn ?? $student->nis }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats Badges -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="bg-white/5 backdrop-blur-xs border border-white/10 rounded-2xl p-3 text-center">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Catatan</span>
                            <span class="text-xl font-black text-white mt-0.5">{{ $totalNotes }}</span>
                        </div>
                        <div class="bg-white/5 backdrop-blur-xs border border-white/10 rounded-2xl p-3 text-center">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Catatan Hari Ini</span>
                            <span class="text-xl font-black {{ $todayNote ? 'text-emerald-400' : 'text-slate-400' }} mt-0.5">
                                {{ $todayNote ? '1 Ada' : '0' }}
                            </span>
                        </div>
                        <div class="bg-white/5 backdrop-blur-xs border border-white/10 rounded-2xl p-3 text-center col-span-2 sm:col-span-1">
                            <span class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Status Buku</span>
                            <span class="text-xs font-bold text-emerald-300 mt-1 inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Aktif Sinkron</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Bar -->
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-xs">
                <form method="GET" action="{{ route('parent.daily-notes') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Kategori</label>
                        <select name="kategori" onchange="this.form.submit()"
                                class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Tanggal Spesifik</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" onchange="this.form.submit()"
                               class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Bulan</label>
                        <input type="month" name="bulan" value="{{ request('bulan') }}" onchange="this.form.submit()"
                               class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold text-slate-500 uppercase tracking-wider mb-1">Cari Kata Kunci</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari isi catatan..."
                                   class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                            <button type="submit" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition">
                                🔍
                            </button>
                        </div>
                    </div>
                </form>

                @if(request()->anyFilled(['kategori', 'tanggal', 'bulan', 'search']))
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan hasil filter catatan</span>
                        <a href="{{ route('parent.daily-notes') }}" class="text-red-600 hover:underline font-bold">Reset Filter</a>
                    </div>
                @endif
            </div>

            <!-- Notes List -->
            @if($notes->isEmpty())
                <div class="bg-white p-12 rounded-3xl border border-slate-100 shadow-xs text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-3xl mx-auto">
                        📖
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Catatan dari Guru</h3>
                    <p class="text-xs text-slate-500 max-w-md mx-auto">
                        Saat ini Bapak/Ibu guru belum menuliskan catatan harian khusus untuk Ananda {{ $student->nama }}. Setiap ada catatan perkembangan atau apresiasi baru dari sekolah, Anda dapat melihatnya langsung di halaman ini.
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($notes as $note)
                        <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-xs hover:shadow-md transition duration-200 relative overflow-hidden">
                            <!-- Left Accent Color Bar -->
                            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-red-600 to-red-800"></div>

                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 pb-4 border-b border-slate-100 pl-2">
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold border {{ $note->getKategoriBadgeClass() }}">
                                            <span>{{ $note->getKategoriIcon() }}</span>
                                            <span>{{ $note->kategori }}</span>
                                        </span>
                                        <span class="text-xs text-slate-400">&bull;</span>
                                        <span class="text-xs font-extrabold text-slate-700">
                                            📅 {{ \Carbon\Carbon::parse($note->tanggal)->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>
                                    <h4 class="text-base font-black text-slate-900 mt-2">
                                        {{ $note->judul }}
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Ditulis oleh: <strong class="text-slate-800">{{ $note->teacher?->name ?? 'Guru / Wali Kelas' }}</strong>
                                    </p>
                                </div>

                                <div class="text-right">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-[11px] font-extrabold">
                                        <span>✓ Telah Dilihat</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Note Content Body -->
                            <div class="pt-4 pl-2 space-y-3">
                                <div class="text-xs sm:text-sm text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                                    {{ $note->catatan }}
                                </div>

                                @if($note->pesan_untuk_orangtua)
                                    <div class="p-4 bg-gradient-to-r from-red-50 to-amber-50/50 rounded-2xl border border-red-200/80 space-y-1.5">
                                        <div class="font-extrabold text-red-800 text-xs flex items-center gap-2 uppercase tracking-wider">
                                            <span>🏡 Pesan & Pendampingan di Rumah:</span>
                                        </div>
                                        <p class="text-xs sm:text-sm text-slate-800 font-medium leading-relaxed pl-3 border-l-2 border-red-500">
                                            "{{ $note->pesan_untuk_orangtua }}"
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer note metadata -->
                            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 pl-2">
                                <span>Waktu publikasi: {{ $note->created_at->translatedFormat('d M Y, H:i') }} WITA</span>
                                <span>Portal NTO National Plus Primary School</span>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $notes->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
