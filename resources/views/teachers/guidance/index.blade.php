<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4" x-data>
            <div>
                <h2 class="text-xl font-bold text-slate-900 leading-tight flex items-center gap-2">
                    <span class="p-2 bg-red-100 text-red-600 rounded-xl">📋</span>
                    <span>Buku Bimbingan & Jurnal Observasi Siswa</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Pencatatan perilaku, bimbingan berkala, dan pembinaan siswa &bull; 
                    <strong class="text-red-700">{{ str_starts_with($selectedClass, 'Kelas') ? $selectedClass : 'Kelas ' . $selectedClass }}</strong>
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('guidance-journal.print', ['kelas' => $selectedClass, 'bulan' => $selectedMonth]) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Lembar A4</span>
                </a>
                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-create-modal'))"
                        @click="$dispatch('open-create-modal')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold rounded-xl shadow-md shadow-red-600/30 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Observasi Siswa</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="{
        createOpen: false,
        editOpen: false,
        editData: {
            id: null,
            student_id: '',
            tanggal: '',
            kategori: 'A',
            perilaku_kejadian: '',
            identifikasi_masalah: '',
            pendekatan_wali_kelas: '',
            komitmen_siswa: '',
            tindak_lanjut: ''
        },
        openEdit(item) {
            this.editData = {
                id: item.id,
                student_id: item.student_id,
                tanggal: item.tanggal,
                kategori: item.kategori,
                perilaku_kejadian: item.perilaku_kejadian,
                identifikasi_masalah: item.identifikasi_masalah || '',
                pendekatan_wali_kelas: item.pendekatan_wali_kelas || '',
                komitmen_siswa: item.komitmen_siswa || '',
                tindak_lanjut: item.tindak_lanjut || ''
            };
            this.editOpen = true;
        }
    }" @open-create-modal.window="createOpen = true">

        <!-- Flash Message -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between text-xs font-bold shadow-xs">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-500 text-base">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        <!-- Filter Periode & Pilihan Kelas (Jika Admin) -->
        <div class="bg-white rounded-3xl p-5 shadow-xs border border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form method="GET" action="{{ route('guidance-journal.index') }}" class="flex flex-wrap items-center gap-3">
                @if(auth()->user()->isAdmin())
                    <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                        <span>Kelas:</span>
                        <select name="kelas" onchange="this.form.submit()" class="py-1.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-red-500">
                            @foreach($availableClasses as $cls)
                                <option value="{{ $cls }}" {{ $selectedClass == $cls ? 'selected' : '' }}>{{ \App\Models\Student::formatClass($cls) }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="kelas" value="{{ $selectedClass }}">
                @endif

                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                    <span>Bulan:</span>
                    <input type="month" name="bulan" value="{{ $selectedMonth }}" onchange="this.form.submit()"
                           class="py-1.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-red-500">
                </div>

                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-700">
                    <span>Kategori:</span>
                    <select name="kategori" onchange="this.form.submit()" class="py-1.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:ring-red-500">
                        <option value="">Semua Kategori (A, S, D, P)</option>
                        <option value="A" {{ request('kategori') == 'A' ? 'selected' : '' }}>📚 Akademik (A)</option>
                        <option value="S" {{ request('kategori') == 'S' ? 'selected' : '' }}>🤝 Sosial-Emosional (S)</option>
                        <option value="D" {{ request('kategori') == 'D' ? 'selected' : '' }}>⏱️ Kedisiplinan (D)</option>
                        <option value="P" {{ request('kategori') == 'P' ? 'selected' : '' }}>🌟 Potensi / Prestasi (P)</option>
                    </select>
                </div>

                @if(request('kategori') || request('search'))
                    <a href="{{ route('guidance-journal.index', ['kelas' => $selectedClass, 'bulan' => $selectedMonth]) }}"
                       class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold" title="Reset filter">
                        ✕ Reset
                    </a>
                @endif
            </form>

            <form method="GET" action="{{ route('guidance-journal.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="kelas" value="{{ $selectedClass }}">
                <input type="hidden" name="bulan" value="{{ $selectedMonth }}">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari siswa / kejadian..."
                           class="pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 w-52">
                    <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                </div>
            </form>
        </div>

        <!-- 4 Kotak Ringkasan Kategori (A, S, D, P) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">Akademik (A)</span>
                    <span class="text-2xl font-black text-blue-900">{{ $countA }}</span>
                    <span class="text-[11px] text-blue-700 block">Kasus / Pendampingan</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                    📚
                </div>
            </div>

            <div class="bg-purple-50/70 border border-purple-200/80 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 block">Sosial-Emosional (S)</span>
                    <span class="text-2xl font-black text-purple-900">{{ $countS }}</span>
                    <span class="text-[11px] text-purple-700 block">Relasi & Psikologis</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-lg">
                    🤝
                </div>
            </div>

            <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Kedisiplinan (D)</span>
                    <span class="text-2xl font-black text-amber-900">{{ $countD }}</span>
                    <span class="text-[11px] text-amber-700 block">Tata Tertib & Waktu</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-lg">
                    ⏱️
                </div>
            </div>

            <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Potensi / Prestasi (P)</span>
                    <span class="text-2xl font-black text-emerald-900">{{ $countP }}</span>
                    <span class="text-[11px] text-emerald-700 block">Bakat & Capaian Positif</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg">
                    🌟
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- BAGIAN A: TABEL JURNAL OBSERVASI HARIAN                        -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                        <span>A.</span> Jurnal Observasi Harian
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catatan kejadian, akar masalah, pendekatan, komitmen siswa, dan tindak lanjut perorangan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-xl text-xs font-bold">
                        Total: {{ $journals->count() }} Kejadian
                    </span>
                    <button type="button" @click="createOpen = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>+ Tambah Data</span>
                    </button>
                </div>
            </div>

            @if($journals->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-3">
                        📝
                    </div>
                    <h4 class="font-bold text-sm text-slate-800">Belum Ada Catatan Observasi di Bulan Ini</h4>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Mulai mencatat jurnal observasi dan bimbingan untuk siswa di {{ str_starts_with($selectedClass, 'Kelas') ? $selectedClass : 'Kelas ' . $selectedClass }}.</p>
                    <button type="button" @click="createOpen = true"
                            class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-xs rounded-xl shadow-md shadow-red-600/30 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Observasi Siswa</span>
                    </button>
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-slate-200/80">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50 text-slate-700 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200">
                            <tr>
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3 w-24">Tanggal</th>
                                <th class="py-3 px-4 min-w-[140px]">Nama Siswa</th>
                                <th class="py-3 px-4 min-w-[180px]">Perilaku / Kejadian</th>
                                <th class="py-3 px-3 text-center w-20">Kategori</th>
                                <th class="py-3 px-4 min-w-[150px]">Identifikasi Masalah</th>
                                <th class="py-3 px-4 min-w-[150px]">Pendekatan Guru</th>
                                <th class="py-3 px-4 min-w-[140px]">Komitmen Siswa</th>
                                <th class="py-3 px-4 min-w-[140px]">Tindak Lanjut</th>
                                <th class="py-3 px-3 text-center w-16">Paraf</th>
                                <th class="py-3 px-3 text-center w-20">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            @foreach($journals as $idx => $item)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="py-3 px-3 text-center font-bold text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-3 font-mono font-bold text-slate-800 whitespace-nowrap">
                                        {{ $item->tanggal->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-bold text-slate-900">{{ $item->student?->nama ?? '-' }}</div>
                                        <div class="text-[10px] font-mono text-slate-400">NISN: {{ $item->student?->nisn ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 leading-relaxed font-semibold text-slate-800">
                                        {{ $item->perilaku_kejadian }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black border {{ $item->kategori_badge }}" title="{{ $item->kategori_nama }}">
                                            {{ $item->kategori }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 leading-relaxed">
                                        {{ $item->identifikasi_masalah ?: '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 leading-relaxed">
                                        {{ $item->pendekatan_wali_kelas ?: '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 leading-relaxed italic">
                                        {{ $item->komitmen_siswa ? '"' . $item->komitmen_siswa . '"' : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-600 leading-relaxed">
                                        {{ $item->tindak_lanjut ?: '-' }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs" title="Diparaf oleh {{ $item->teacher?->name }}">
                                            ✓
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openEdit({
                                                id: {{ $item->id }},
                                                student_id: {{ $item->student_id }},
                                                tanggal: '{{ $item->tanggal->format('Y-m-d') }}',
                                                kategori: '{{ $item->kategori }}',
                                                perilaku_kejadian: '{{ addslashes($item->perilaku_kejadian) }}',
                                                identifikasi_masalah: '{{ addslashes($item->identifikasi_masalah ?? '') }}',
                                                pendekatan_wali_kelas: '{{ addslashes($item->pendekatan_wali_kelas ?? '') }}',
                                                komitmen_siswa: '{{ addslashes($item->komitmen_siswa ?? '') }}',
                                                tindak_lanjut: '{{ addslashes($item->tindak_lanjut ?? '') }}'
                                            })" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Catatan">
                                                ✏️
                                            </button>
                                            <form action="{{ route('guidance-journal.destroy', $item) }}" method="POST"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan observasi siswa ini?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- BAGIAN B: STANDAR ACUAN KATEGORI PERILAKU                      -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80 space-y-4">
            <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                <span>B.</span> Keterangan Kategori Perilaku (Standar Pedoman Guru)
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <div class="p-3.5 bg-blue-50/50 rounded-2xl border border-blue-200/60 flex items-start gap-3">
                    <span class="w-7 h-7 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center shrink-0 text-sm">A</span>
                    <div>
                        <strong class="text-blue-900 block font-bold mb-0.5">Akademik</strong>
                        <p class="text-slate-600 leading-relaxed text-[11px]">Berkaitan dengan pemahaman materi, penyelesaian tugas, capaian pembelajaran, motivasi belajar, dan kebutuhan pendampingan akademik.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-purple-50/50 rounded-2xl border border-purple-200/60 flex items-start gap-3">
                    <span class="w-7 h-7 rounded-xl bg-purple-600 text-white font-black flex items-center justify-center shrink-0 text-sm">S</span>
                    <div>
                        <strong class="text-purple-900 block font-bold mb-0.5">Sosial-Emosional</strong>
                        <p class="text-slate-600 leading-relaxed text-[11px]">Berkaitan dengan relasi sosial, pengelolaan emosi, kepercayaan diri, empati, komunikasi, dan kondisi psikologis siswa.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-amber-50/50 rounded-2xl border border-amber-200/60 flex items-start gap-3">
                    <span class="w-7 h-7 rounded-xl bg-amber-600 text-white font-black flex items-center justify-center shrink-0 text-sm">D</span>
                    <div>
                        <strong class="text-amber-900 block font-bold mb-0.5">Kedisiplinan</strong>
                        <p class="text-slate-600 leading-relaxed text-[11px]">Berkaitan dengan kehadiran, ketepatan waktu, kerapian, kepatuhan terhadap tata tertib, tanggung jawab, dan pembiasaan positif.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-emerald-50/50 rounded-2xl border border-emerald-200/60 flex items-start gap-3">
                    <span class="w-7 h-7 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center shrink-0 text-sm">P</span>
                    <div>
                        <strong class="text-emerald-900 block font-bold mb-0.5">Potensi / Prestasi</strong>
                        <p class="text-slate-600 leading-relaxed text-[11px]">Berkaitan dengan bakat, minat, kepemimpinan, keaktifan menonjol, pencapaian akademik/nonakademik, dan perkembangan positif siswa.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- BAGIAN C & D: CATATAN UMUM WALI KELAS & REKAP SINGKAT BULANAN  -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80 space-y-6">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                    <span>C & D.</span> Catatan Umum Wali Kelas & Rekap Singkat Bulanan
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Uraian evaluasi bulanan untuk lembar pengesahan dan laporan ke Kepala Sekolah.</p>
            </div>

            <form action="{{ route('guidance-journal.recap') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="kelas" value="{{ $selectedClass }}">
                <input type="hidden" name="bulan" value="{{ $selectedMonth }}">

                <!-- BAGIAN C: URAIAN CATATAN UMUM -->
                <div class="space-y-4">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl inline-block">
                        Bagian C: Uraian Catatan Umum Kelas
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">1. Kondisi Umum Kelas</label>
                            <textarea name="kondisi_umum_kelas" rows="3" placeholder="Tuliskan suasana kelas, dinamika interaksi, dan keterlibatan siswa secara keseluruhan..."
                                      class="w-full text-xs rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">{{ old('kondisi_umum_kelas', $monthlyRecap->kondisi_umum_kelas) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">2. Siswa yang Memerlukan Perhatian Khusus</label>
                            <textarea name="siswa_perhatian_khusus" rows="3" placeholder="Nama-nama siswa yang butuh pendampingan khusus beserta catatan singkatnya..."
                                      class="w-full text-xs rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">{{ old('siswa_perhatian_khusus', $monthlyRecap->siswa_perhatian_khusus) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">3. Tindak Lanjut dengan Guru Mapel / Orang Tua</label>
                            <textarea name="tindak_lanjut_ortu_guru" rows="3" placeholder="Koordinasi yang telah dilakukan atau direncanakan bersama guru bidang studi / wali murid..."
                                      class="w-full text-xs rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">{{ old('tindak_lanjut_ortu_guru', $monthlyRecap->tindak_lanjut_ortu_guru) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">4. Rekomendasi Pembinaan Berikutnya</label>
                            <textarea name="rekomendasi_berikutnya" rows="3" placeholder="Langkah strategis pembinaan untuk bulan berikutnya..."
                                      class="w-full text-xs rounded-2xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">{{ old('rekomendasi_berikutnya', $monthlyRecap->rekomendasi_berikutnya) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN D: KETERANGAN SINGKAT PER KATEGORI -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl inline-block">
                        Bagian D: Keterangan Singkat Bulanan per Kategori
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-blue-900 mb-1">
                                Keterangan Singkat: Akademik (A) &bull; <span class="font-normal text-slate-500">{{ $countA }} Kasus</span>
                            </label>
                            <input type="text" name="ket_akademik" value="{{ old('ket_akademik', $monthlyRecap->ket_akademik) }}"
                                   placeholder="Contoh: Pendampingan materi numerasi bagi 2 siswa..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-purple-900 mb-1">
                                Keterangan Singkat: Sosial-Emosional (S) &bull; <span class="font-normal text-slate-500">{{ $countS }} Kasus</span>
                            </label>
                            <input type="text" name="ket_sosial_emosional" value="{{ old('ket_sosial_emosional', $monthlyRecap->ket_sosial_emosional) }}"
                                   placeholder="Contoh: Pembinaan resolusi konflik pertemanan..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-amber-900 mb-1">
                                Keterangan Singkat: Kedisiplinan (D) &bull; <span class="font-normal text-slate-500">{{ $countD }} Kasus</span>
                            </label>
                            <input type="text" name="ket_kedisiplinan" value="{{ old('ket_kedisiplinan', $monthlyRecap->ket_kedisiplinan) }}"
                                   placeholder="Contoh: Pembiasaan hadir tepat waktu sebelum bel masuk..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-emerald-900 mb-1">
                                Keterangan Singkat: Potensi / Prestasi (P) &bull; <span class="font-normal text-slate-500">{{ $countP }} Temuan</span>
                            </label>
                            <input type="text" name="ket_potensi_prestasi" value="{{ old('ket_potensi_prestasi', $monthlyRecap->ket_potensi_prestasi) }}"
                                   placeholder="Contoh: Peningkatan minat membaca dan keaktifan memimpin doa..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white rounded-xl font-bold text-xs shadow-md shadow-red-600/30 transition flex items-center gap-2">
                        <span>💾 Simpan Catatan & Rekap Bulanan</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL 1: TAMBAH CATATAN OBSERVASI SISWA (CREATE)              -->
        <!-- ============================================================== -->
        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="createOpen = false"></div>

                <div class="inline-block w-full max-w-xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-3xl shadow-2xl z-10 border border-slate-100 p-6 sm:p-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 bg-red-50 text-red-600 rounded-xl text-lg">📝</span>
                            <div>
                                <h3 class="font-extrabold text-base text-slate-900">Tambah Jurnal Observasi Siswa</h3>
                                <p class="text-xs text-slate-500">{{ \App\Models\Student::formatClass($selectedClass) }}</p>
                            </div>
                        </div>
                        <button type="button" @click="createOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <form action="{{ route('guidance-journal.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Siswa -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Siswa <span class="text-red-500">*</span></label>
                                <select name="student_id" required class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500 font-semibold">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach($students as $st)
                                        <option value="{{ $st->id }}">{{ $st->nama }} (NISN: {{ $st->nisn }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tanggal -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Observasi <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                                       class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500 font-semibold">
                            </div>
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kategori Perilaku <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50/50 cursor-pointer has-checked:border-blue-500 has-checked:bg-blue-50">
                                    <input type="radio" name="kategori" value="A" checked class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs font-bold text-blue-900">A (Akademik)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-purple-50/50 cursor-pointer has-checked:border-purple-500 has-checked:bg-purple-50">
                                    <input type="radio" name="kategori" value="S" class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-xs font-bold text-purple-900">S (Sosial)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50/50 cursor-pointer has-checked:border-amber-500 has-checked:bg-amber-50">
                                    <input type="radio" name="kategori" value="D" class="text-amber-600 focus:ring-amber-500">
                                    <span class="text-xs font-bold text-amber-900">D (Disiplin)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50/50 cursor-pointer has-checked:border-emerald-500 has-checked:bg-emerald-50">
                                    <input type="radio" name="kategori" value="P" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-bold text-emerald-900">P (Potensi)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Perilaku / Kejadian -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Perilaku / Kejadian yang Diamati <span class="text-red-500">*</span></label>
                            <textarea name="perilaku_kejadian" rows="2" required placeholder="Deskripsikan secara objektif perilaku atau kejadian yang muncul..."
                                      class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500"></textarea>
                        </div>

                        <!-- Akar Masalah -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Identifikasi Akar Masalah (Opsional)</label>
                            <input type="text" name="identifikasi_masalah" placeholder="Contoh: Kurang fokus karena mengantuk / belum sarapan..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <!-- Pendekatan Guru -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pendekatan Wali Kelas (Opsional)</label>
                            <input type="text" name="pendekatan_wali_kelas" placeholder="Contoh: Dialog empatik empat mata saat jam istirahat..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <!-- Komitmen Siswa -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Komitmen Siswa (Opsional)</label>
                            <input type="text" name="komitmen_siswa" placeholder="Contoh: Berjanji tidur lebih awal dan mengumpulkan PR tepat waktu..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <!-- Tindak Lanjut -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Rencana Tindak Lanjut (Opsional)</label>
                            <input type="text" name="tindak_lanjut" placeholder="Contoh: Pantau 3 hari ke depan dan beri apresiasi saat ada progres..."
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="createOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-bold text-xs rounded-xl shadow-md shadow-red-600/30 transition">
                                Simpan Entri Observasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- MODAL 2: EDIT CATATAN OBSERVASI SISWA (EDIT)                    -->
        <!-- ============================================================== -->
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" @click="editOpen = false"></div>

                <div class="inline-block w-full max-w-xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-3xl shadow-2xl z-10 border border-slate-100 p-6 sm:p-8">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="p-2 bg-blue-50 text-blue-600 rounded-xl text-lg">✏️</span>
                            <div>
                                <h3 class="font-extrabold text-base text-slate-900">Edit Jurnal Observasi Siswa</h3>
                                <p class="text-xs text-slate-500">Perbarui data hasil bimbingan</p>
                            </div>
                        </div>
                        <button type="button" @click="editOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                    </div>

                    <form :action="'{{ url('guidance-journal') }}/' + editData.id" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Siswa</label>
                                <select name="student_id" required x-model="editData.student_id" class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500 font-semibold">
                                    @foreach($students as $st)
                                        <option value="{{ $st->id }}">{{ $st->nama }} (NISN: {{ $st->nisn }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Observasi</label>
                                <input type="date" name="tanggal" required x-model="editData.tanggal"
                                       class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500 font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Kategori Perilaku</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50/50 cursor-pointer has-checked:border-blue-500 has-checked:bg-blue-50">
                                    <input type="radio" name="kategori" value="A" x-model="editData.kategori" class="text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs font-bold text-blue-900">A (Akademik)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-purple-50/50 cursor-pointer has-checked:border-purple-500 has-checked:bg-purple-50">
                                    <input type="radio" name="kategori" value="S" x-model="editData.kategori" class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-xs font-bold text-purple-900">S (Sosial)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50/50 cursor-pointer has-checked:border-amber-500 has-checked:bg-amber-50">
                                    <input type="radio" name="kategori" value="D" x-model="editData.kategori" class="text-amber-600 focus:ring-amber-500">
                                    <span class="text-xs font-bold text-amber-900">D (Disiplin)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50/50 cursor-pointer has-checked:border-emerald-500 has-checked:bg-emerald-50">
                                    <input type="radio" name="kategori" value="P" x-model="editData.kategori" class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-bold text-emerald-900">P (Potensi)</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Perilaku / Kejadian yang Diamati</label>
                            <textarea name="perilaku_kejadian" rows="2" required x-model="editData.perilaku_kejadian"
                                      class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Identifikasi Akar Masalah</label>
                            <input type="text" name="identifikasi_masalah" x-model="editData.identifikasi_masalah"
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pendekatan Wali Kelas</label>
                            <input type="text" name="pendekatan_wali_kelas" x-model="editData.pendekatan_wali_kelas"
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Komitmen Siswa</label>
                            <input type="text" name="komitmen_siswa" x-model="editData.komitmen_siswa"
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Rencana Tindak Lanjut</label>
                            <input type="text" name="tindak_lanjut" x-model="editData.tindak_lanjut"
                                   class="w-full text-xs rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-red-500">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="editOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
