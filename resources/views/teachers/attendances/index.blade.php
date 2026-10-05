<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Presensi Harian Guru & Staf NTO</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Kelola kehadiran guru harian, pantau scan mandiri, dan tetapkan Izin, Sakit, atau Alpa (Khusus Admin)</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher-attendances.rekap') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-2xl text-xs transition cursor-pointer shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Rekapitulasi Bulanan Guru</span>
                </a>
                <a href="{{ route('qr.scan') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl text-xs transition cursor-pointer shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <span>Scanner QR</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ 
        editModalOpen: false,
        selectedTeacher: null,
        selectedAtt: null,
        formStatus: 'hadir',
        formKeterangan: '',
        formJamMasuk: '',
        formJamPulang: '',
        openEdit(teacher, att) {
            this.selectedTeacher = teacher;
            this.selectedAtt = att;
            this.formStatus = att ? att.status : 'hadir';
            this.formKeterangan = att && att.keterangan ? att.keterangan : '';
            this.formJamMasuk = att && att.jam_masuk ? att.jam_masuk.substring(0, 5) : '';
            this.formJamPulang = att && att.jam_pulang ? att.jam_pulang.substring(0, 5) : '';
            this.editModalOpen = true;
        }
    }">

        <!-- Flash Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Summary Statistics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Guru</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $totalGuru }}</p>
                <p class="text-[10px] font-semibold text-slate-400 mt-0.5">Staf Pengajar</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-emerald-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Hadir</p>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ $totalHadir }}</p>
                <p class="text-[10px] font-semibold text-emerald-600/80 mt-0.5">{{ $totalTerlambat }} Terlambat</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-sky-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-sky-600">Izin</p>
                <p class="text-2xl font-black text-sky-700 mt-1">{{ $totalIzin }}</p>
                <p class="text-[10px] font-semibold text-sky-600/80 mt-0.5">Diset Admin</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-indigo-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-indigo-600">Sakit</p>
                <p class="text-2xl font-black text-indigo-700 mt-1">{{ $totalSakit }}</p>
                <p class="text-[10px] font-semibold text-indigo-600/80 mt-0.5">Surat / Dokter</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-rose-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-rose-600">Alpa</p>
                <p class="text-2xl font-black text-rose-700 mt-1">{{ $totalAlpa }}</p>
                <p class="text-[10px] font-semibold text-rose-600/80 mt-0.5">Tanpa Keterangan</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-amber-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-600">Belum Presensi</p>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ $totalBelumPresensi }}</p>
                <p class="text-[10px] font-semibold text-amber-600/80 mt-0.5">Menunggu Scan</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('teacher-attendances.index') }}" class="flex flex-wrap items-center gap-3 w-full">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Tanggal Presensi</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" 
                           class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition cursor-pointer">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Cari Guru</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau email guru..." 
                           class="w-full py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Status Kehadiran</label>
                    <select name="status" onchange="this.form.submit()" 
                            class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="hadir" {{ $statusFilter === 'hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="terlambat" {{ $statusFilter === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                        <option value="izin" {{ $statusFilter === 'izin' ? 'selected' : '' }}>Izin</option>
                        <option value="sakit" {{ $statusFilter === 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="alpa" {{ $statusFilter === 'alpa' ? 'selected' : '' }}>Alpa</option>
                        <option value="belum_presensi" {{ $statusFilter === 'belum_presensi' ? 'selected' : '' }}>Belum Presensi</option>
                    </select>
                </div>

                <div class="pt-4 flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-xs font-extrabold transition cursor-pointer">
                        Terapkan
                    </button>
                    @if($search || $statusFilter || $tanggal !== now()->format('Y-m-d'))
                        <a href="{{ route('teacher-attendances.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-bold transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Teachers Attendance List Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">
                        Presensi Tanggal: {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Batas Masuk: {{ $jamMasukConfig }} WITA &bull; Toleransi Terlambat: {{ $jamTerlambatConfig }} WITA &bull; Jam Pulang: {{ $jamPulangConfig }} WITA</p>
                </div>
                <span class="text-xs font-black px-3 py-1 bg-slate-100 text-slate-700 rounded-xl">
                    {{ count($teachers) }} Guru Terdaftar
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-4 w-12 text-center">#</th>
                            <th class="py-3.5 px-4">Nama Guru / Staf</th>
                            <th class="py-3.5 px-4">Penugasan / Kelas</th>
                            <th class="py-3.5 px-4 text-center">Jam Masuk</th>
                            <th class="py-3.5 px-4 text-center">Jam Pulang</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4">Catatan / Keterangan</th>
                            <th class="py-3.5 px-4 text-center">Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($teachers as $index => $teacher)
                            @php
                                $att = $teacher->teacherAttendances->first();
                                $isLate = $att && $att->status === 'hadir' && str_contains(strtolower($att->keterangan ?? ''), 'terlambat');
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 text-center font-bold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-2xl bg-slate-900 text-amber-400 font-black text-xs flex items-center justify-center shrink-0 shadow-xs">
                                            {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900">{{ $teacher->name }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $teacher->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-800">
                                        🏫 {{ $teacher->getAssignedClass() ?? 'Guru Pengajar' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-xs font-bold text-slate-800">
                                    @if($att && $att->jam_masuk)
                                        <span class="inline-flex items-center gap-1">
                                            <span>{{ substr($att->jam_masuk, 0, 5) }}</span>
                                            @if($isLate)
                                                <span class="w-2 h-2 rounded-full bg-amber-500" title="Terlambat"></span>
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-xs font-bold text-slate-800">
                                    @if($att && $att->jam_pulang)
                                        <span>{{ substr($att->jam_pulang, 0, 5) }}</span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if(!$att)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black bg-slate-100 text-slate-500 border border-slate-200">
                                            ⏳ BELUM ABSEN
                                        </span>
                                    @elseif($att->status === 'hadir')
                                        @if($isLate)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-300">
                                                ⚠️ TERLAMBAT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black bg-emerald-50 text-emerald-800 border border-emerald-300">
                                                ✓ HADIR
                                            </span>
                                        @endif
                                    @elseif($att->status === 'izin')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black bg-sky-50 text-sky-800 border border-sky-300">
                                            ℹ️ IZIN
                                        </span>
                                    @elseif($att->status === 'sakit')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-300">
                                            🏥 SAKIT
                                        </span>
                                    @elseif($att->status === 'alpa')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-black bg-rose-50 text-rose-800 border border-rose-300">
                                            ✕ ALPA
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 text-xs">
                                    <span class="line-clamp-2" title="{{ $att->keterangan ?? '-' }}">
                                        {{ $att->keterangan ?? '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <button type="button" 
                                            @click="openEdit({{ json_encode($teacher) }}, {{ json_encode($att) }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-900 hover:text-white text-slate-700 font-extrabold rounded-xl text-[11px] transition shadow-2xs cursor-pointer">
                                        <span>⚙️ Set Status</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400">
                                    <p class="font-extrabold text-sm">Tidak ada data guru ditemukan.</p>
                                    <p class="text-xs mt-1">Pastikan sudah menambahkan data guru di menu Data Guru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Admin Set Status Modal (Sakit, Izin, Alpa, Hadir) -->
        <div x-show="editModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="editModalOpen = false"></div>

                <div x-show="editModalOpen" x-transition.scale.origin.bottom class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100 sm:p-8">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center font-black text-sm">
                                👨‍🏫
                            </div>
                            <div>
                                <h3 class="font-black text-base text-slate-900" id="modal-title">Tetapkan Status Presensi Guru</h3>
                                <p class="text-xs text-slate-400" x-text="selectedTeacher ? selectedTeacher.name : ''"></p>
                            </div>
                        </div>
                        <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 p-2 rounded-xl">
                            ✕
                        </button>
                    </div>

                    <form method="POST" action="{{ route('teacher-attendances.update-status') }}" class="mt-5 space-y-4">
                        @csrf
                        <input type="hidden" name="teacher_id" :value="selectedTeacher ? selectedTeacher.id : ''">
                        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-2">Pilih Status Kehadiran</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition"
                                       :class="formStatus === 'hadir' ? 'border-emerald-500 bg-emerald-50/50 text-emerald-900 font-extrabold shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                                    <input type="radio" name="status" value="hadir" x-model="formStatus" class="sr-only">
                                    <span class="text-lg mb-1">✓</span>
                                    <span class="text-xs font-bold">Hadir</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition"
                                       :class="formStatus === 'izin' ? 'border-sky-500 bg-sky-50/50 text-sky-900 font-extrabold shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                                    <input type="radio" name="status" value="izin" x-model="formStatus" class="sr-only">
                                    <span class="text-lg mb-1">ℹ️</span>
                                    <span class="text-xs font-bold">Izin</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition"
                                       :class="formStatus === 'sakit' ? 'border-indigo-500 bg-indigo-50/50 text-indigo-900 font-extrabold shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                                    <input type="radio" name="status" value="sakit" x-model="formStatus" class="sr-only">
                                    <span class="text-lg mb-1">🏥</span>
                                    <span class="text-xs font-bold">Sakit</span>
                                </label>
                                <label class="flex flex-col items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition"
                                       :class="formStatus === 'alpa' ? 'border-rose-500 bg-rose-50/50 text-rose-900 font-extrabold shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-600'">
                                    <input type="radio" name="status" value="alpa" x-model="formStatus" class="sr-only">
                                    <span class="text-lg mb-1">✕</span>
                                    <span class="text-xs font-bold">Alpa</span>
                                </label>
                            </div>
                        </div>

                        <!-- Optional Times for Hadir -->
                        <div x-show="formStatus === 'hadir'" x-transition class="grid grid-cols-2 gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Jam Masuk (Opsional)</label>
                                <input type="time" name="jam_masuk" x-model="formJamMasuk" class="w-full py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Jam Pulang (Opsional)</label>
                                <input type="time" name="jam_pulang" x-model="formJamPulang" class="w-full py-1.5 px-3 bg-white border border-slate-200 rounded-xl text-xs font-mono font-bold">
                            </div>
                        </div>

                        <!-- Catatan / Keterangan -->
                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-500 mb-1">Catatan / Alasan Keterangan</label>
                            <textarea name="keterangan" rows="3" x-model="formKeterangan"
                                      placeholder="Contoh: Izin urusan keluarga, Sakit demam surat dokter terlampir, dll."
                                      class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition"></textarea>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                            <button type="button" 
                                    x-show="selectedAtt"
                                    @click="if(confirm('Yakin ingin mereset/menghapus presensi guru ini pada tanggal {{ $tanggal }}?')) { formStatus = 'hapus'; $el.closest('form').submit(); }"
                                    class="px-4 py-2.5 text-rose-600 hover:bg-rose-50 rounded-2xl text-xs font-extrabold transition">
                                🗑️ Hapus Presensi
                            </button>
                            <span x-show="!selectedAtt"></span>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="editModalOpen = false" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-2xl text-xs font-bold transition">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-xs font-black transition shadow-sm">
                                    Simpan Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
