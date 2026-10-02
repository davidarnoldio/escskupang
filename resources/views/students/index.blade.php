<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Data Siswa NTO National Plus</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Kelola data seluruh siswa aktif terdaftar di sekolah</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('alumni.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-2xl text-xs shadow-md transition duration-200 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                    <span>Direktori Alumni</span>
                </a>
                @if(Auth::user() && Auth::user()->isAdmin())
                    <a href="{{ route('students.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl text-xs shadow-lg shadow-red-600/30 transition duration-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>Tambah Siswa Baru</span>
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        qrModalOpen: false,
        selectedStudent: null,
        openQrModal(student) { this.selectedStudent = student; this.qrModalOpen = true; },
        graduateModalOpen: false,
        openGraduateModal(student) { this.selectedStudent = student; this.graduateModalOpen = true; },
        bulkGraduateModalOpen: false,
        selectedIds: [],
        selectAll: false,
        toggleSelectAll(ids) {
            if (this.selectAll) {
                this.selectedIds = [...ids];
            } else {
                this.selectedIds = [];
            }
        },
        openBulkModal() {
            if (this.selectedIds.length === 0) {
                alert('Pilih minimal satu siswa untuk diluluskan secara massal.');
                return;
            }
            this.bulkGraduateModalOpen = true;
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
        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Class Category Filter Bar / Tabs -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Kategori Tingkat Kelas</span>
                @if($assignedClass)
                    <span class="px-3 py-1 bg-red-50 text-red-800 border border-red-200 font-extrabold text-xs rounded-full">
                        Wali Kelas {{ $assignedClass }}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-2 overflow-x-auto pb-1">
                @if(!$assignedClass)
                    <a href="{{ route('students.index', ['search' => request('search')]) }}" 
                       class="px-4 py-2 rounded-2xl text-xs font-bold transition duration-200 shrink-0 {{ !request('kelas') ? 'bg-red-600 text-white font-black shadow-md shadow-red-600/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Kelas
                    </a>
                @endif
                @foreach($classList as $c)
                    @if(!$assignedClass || $assignedClass == $c)
                        <a href="{{ route('students.index', ['kelas' => $c, 'search' => request('search')]) }}" 
                           class="px-4 py-2 rounded-2xl text-xs font-bold transition duration-200 shrink-0 {{ request('kelas') == $c || $assignedClass == $c ? 'bg-red-600 text-white font-black shadow-md shadow-red-600/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            {{ $c }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100">
            <form method="GET" action="{{ route('students.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa, NISN, atau NIK..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-red-600 focus:bg-white transition">
                </div>

                @if(request('kelas'))
                    <input type="hidden" name="kelas" value="{{ request('kelas') }}">
                @endif

                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl text-xs shadow-sm transition cursor-pointer flex items-center justify-center gap-1.5">
                    <span>Cari</span>
                </button>
            </form>
        </div>

        <!-- Floating Bulk Action Bar (Visible when checkboxes selected) -->
        <div x-show="selectedIds.length > 0" x-transition.duration.200ms class="p-4 bg-slate-900 text-white rounded-3xl shadow-xl border border-slate-800 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-red-600 text-white font-black text-xs flex items-center justify-center shadow-md shadow-red-600/50" x-text="selectedIds.length"></span>
                <div>
                    <h4 class="font-extrabold text-xs text-white">Siswa Terpilih</h4>
                    <p class="text-[11px] text-slate-400 font-medium">Siap dipindahkan ke Data Alumni</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="openBulkModal()" class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-black rounded-xl text-xs shadow-lg transition flex items-center gap-1.5 cursor-pointer">
                    <span>🎓 Luluskan Siswa Terpilih (Massal)</span>
                </button>
                <button type="button" @click="selectedIds = []; selectAll = false;" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition">
                    Batal
                </button>
            </div>
        </div>

        <!-- Students Data Table Container -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                    Daftar Siswa Aktif (Total: {{ $students->total() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase font-black tracking-wider border-b border-slate-100">
                        <tr>
                            @if(Auth::user() && Auth::user()->isAdmin())
                                <th class="px-4 py-3.5 text-center w-10">
                                    <input type="checkbox" x-model="selectAll" @change="toggleSelectAll({{ json_encode($students->pluck('id')) }})" class="rounded border-slate-300 text-red-600 focus:ring-red-500 cursor-pointer">
                                </th>
                            @endif
                            <th class="px-6 py-3.5">NISN</th>
                            <th class="px-6 py-3.5">Siswa</th>
                            <th class="px-6 py-3.5">Kelas</th>
                            <th class="px-6 py-3.5">L/P</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            <tr class="hover:bg-red-50/40 transition duration-150" :class="selectedIds.includes({{ $student->id }}) ? 'bg-red-50/60' : ''">
                                @if(Auth::user() && Auth::user()->isAdmin())
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" value="{{ $student->id }}" x-model="selectedIds" class="rounded border-slate-300 text-red-600 focus:ring-red-500 cursor-pointer">
                                    </td>
                                @endif
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">
                                    {{ $student->nisn ?? $student->nis }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar -->
                                        @if($student->foto_url)
                                            <img src="{{ $student->foto_url }}" alt="{{ $student->nama }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center font-black text-xs shadow-2xs">
                                                {{ strtoupper(substr($student->nama, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('students.show', $student) }}" class="font-bold text-slate-900 hover:text-red-600 transition flex items-center gap-2">
                                                <span>{{ $student->nama }}</span>
                                                @if($student->is_abk)
                                                    <span class="px-1.5 py-0.5 text-[9px] font-black rounded bg-amber-100 text-amber-900 border border-amber-300">ABK</span>
                                                @endif
                                            </a>
                                            <div class="text-[11px] text-slate-400 font-medium">Ortu: {{ $student->user ? $student->user->name : '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-extrabold rounded-full bg-red-50 text-red-700 border border-red-200/80">
                                        Kelas {{ $student->kelas }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-600">
                                    {{ $student->jenis_kelamin }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('students.show', $student) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                                        👁️ Detail
                                    </a>
                                    <button type="button" @click="openQrModal({{ json_encode($student) }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl text-xs font-bold transition cursor-pointer">
                                        📷 QR
                                    </button>

                                    @if(Auth::user() && Auth::user()->isAdmin())
                                        <button type="button" @click="openGraduateModal({{ json_encode($student) }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition cursor-pointer" title="Luluskan siswa dan pindahkan ke data alumni">
                                            🎓 Luluskan
                                        </button>

                                        <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold transition">
                                            ✏️ Edit
                                        </a>

                                        <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data siswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user() && Auth::user()->isAdmin() ? '6' : '5' }}" class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                    Tidak ada data siswa aktif yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links -->
            @if($students->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $students->links() }}
                </div>
            @endif
        </div>

        <!-- QR Code Modal -->
        <div x-show="qrModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="qrModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-sm w-full shadow-2xl border border-slate-100 text-center space-y-4">
                <template x-if="selectedStudent">
                    <div>
                        <h3 class="font-extrabold text-lg text-slate-900" x-text="selectedStudent.nama"></h3>
                        <p class="text-xs text-slate-500 font-semibold mt-0.5" x-text="'NISN: ' + (selectedStudent.nisn || selectedStudent.nis) + ' | Kelas ' + selectedStudent.kelas"></p>
                        
                        <div class="my-5 p-4 bg-slate-50 rounded-2xl border border-slate-200 inline-block shadow-xs">
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(selectedStudent.nisn || selectedStudent.nis)" alt="QR Code" class="w-40 h-40 mx-auto rounded-xl">
                        </div>

                        <div class="flex items-center justify-center gap-3">
                            <a :href="'/students/' + selectedStudent.id + '/qr-card'" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl transition shadow-md">
                                Cetak Kartu QR
                            </a>
                            <button type="button" @click="qrModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal Kelulusan 1 Siswa (Single Graduate) -->
        <div x-show="graduateModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="graduateModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4">
                <template x-if="selectedStudent">
                    <div>
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                                    <span>🎓 Luluskan Siswa ke Alumni</span>
                                </h3>
                                <p class="text-xs text-slate-500 font-semibold" x-text="selectedStudent.nama + ' &bull; Kelas ' + selectedStudent.kelas"></p>
                            </div>
                            <button type="button" @click="graduateModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                        </div>

                        <form :action="'/students/' + selectedStudent.id + '/graduate'" method="POST" class="space-y-4 mt-4">
                            @csrf

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Tahun Kelulusan / Angkatan *</label>
                                    <input type="text" name="tahun_lulus" value="{{ date('Y') . '/' . (date('Y') + 1) }}" required placeholder="Contoh: 2024/2025"
                                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kelulusan</label>
                                    <input type="date" name="tanggal_lulus" value="{{ date('Y-m-d') }}"
                                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Ijazah / SKL (Opsional)</label>
                                <input type="text" name="no_ijazah" placeholder="Contoh: DN-24/D-SD/13/0012345"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Sekolah Lanjutan (Opsional)</label>
                                <input type="text" name="sekolah_lanjutan" placeholder="Contoh: SMP Kristen Petra Kupang"
                                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Kelulusan</label>
                                <textarea name="catatan_kelulusan" rows="2" placeholder="Keterangan predikat kelulusan atau catatan..."
                                          class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600"></textarea>
                            </div>

                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-900 font-medium">
                                ℹ️ Siswa yang diluluskan akan dipindahkan ke menu <strong>Data Alumni</strong> dan tidak akan muncul di daftar presensi harian kelas berjalan.
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="graduateModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow-md transition">
                                    Konfirmasi Luluskan Siswa
                                </button>
                            </div>
                        </form>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal Kelulusan Massal (Bulk Graduate) -->
        <div x-show="bulkGraduateModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="bulkGraduateModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                            <span>🎓 Kelulusan Massal Siswa Terpilih</span>
                        </h3>
                        <p class="text-xs text-slate-500 font-semibold">
                            Total <strong class="text-red-600" x-text="selectedIds.length"></strong> siswa akan dipindahkan serentak ke Data Alumni
                        </p>
                    </div>
                    <button type="button" @click="bulkGraduateModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form action="{{ route('students.bulk-graduate') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Hidden Inputs for Selected Student IDs -->
                    <template x-for="id in selectedIds" :key="id">
                        <input type="hidden" name="student_ids[]" :value="id">
                    </template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tahun Kelulusan / Angkatan *</label>
                            <input type="text" name="tahun_lulus" value="{{ date('Y') . '/' . (date('Y') + 1) }}" required placeholder="Contoh: 2024/2025"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kelulusan</label>
                            <input type="date" name="tanggal_lulus" value="{{ date('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Kelulusan (Opsional)</label>
                        <textarea name="catatan_kelulusan" rows="2" placeholder="Catatan angkatan kelulusan ini..."
                                  class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600"></textarea>
                    </div>

                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-[11px] text-amber-900 font-medium">
                        ⚠️ Seluruh siswa terpilih akan berstatus Alumni dan dipindahkan dari daftar presensi harian. Nomor ijazah perorangan dapat dilengkapi nanti di menu Data Alumni.
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="bulkGraduateModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-extrabold shadow-md transition">
                            Luluskan Siswa Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
