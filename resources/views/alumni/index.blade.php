<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Data Alumni NTO National Plus</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Arsip dan direktori kelulusan siswa sekolah</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('alumni.print-rekap', ['tahun_lulus' => request('tahun_lulus'), 'kelas' => request('kelas')]) }}" 
                   target="_blank" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-2xl text-xs shadow-md transition duration-200 cursor-pointer">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Rekapan Alumni (PDF)</span>
                </a>
                <a href="{{ route('students.index') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-2xl text-xs transition duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Ke Siswa Aktif</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        editModalOpen: false,
        selectedAlumni: {},
        openEditModal(item) {
            this.selectedAlumni = item;
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
        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Alumni Quick Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-red-600 to-red-800 text-white flex items-center justify-center shadow-lg shadow-red-600/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Seluruh Alumni</p>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalAlumni) }}</h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Alumni Laki-Laki</p>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalLaki) }}</h3>
                </div>
            </div>

            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 border border-pink-100 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Alumni Perempuan</p>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalPerempuan) }}</h3>
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 space-y-4">
            <!-- Filter Tabs Angkatan -->
            <div class="space-y-2">
                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tahun Kelulusan / Angkatan</span>
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <a href="{{ route('alumni.index', ['search' => request('search'), 'kelas' => request('kelas')]) }}" 
                       class="px-4 py-2 rounded-2xl text-xs font-bold transition duration-200 shrink-0 {{ !request('tahun_lulus') ? 'bg-red-600 text-white font-black shadow-md shadow-red-600/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Angkatan
                    </a>
                    @foreach($angkatanList as $th)
                        <a href="{{ route('alumni.index', ['tahun_lulus' => $th, 'search' => request('search'), 'kelas' => request('kelas')]) }}" 
                           class="px-4 py-2 rounded-2xl text-xs font-bold transition duration-200 shrink-0 {{ request('tahun_lulus') == $th ? 'bg-red-600 text-white font-black shadow-md shadow-red-600/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Angkatan {{ $th }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('alumni.index') }}" class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-slate-100">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NISN, No Ijazah, sekolah lanjutan..."
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-red-600 focus:bg-white transition">
                </div>

                @if(request('tahun_lulus'))
                    <input type="hidden" name="tahun_lulus" value="{{ request('tahun_lulus') }}">
                @endif

                <select name="kelas" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                    <option value="">-- Semua Kelas Terakhir --</option>
                    @foreach($classList as $c)
                        <option value="{{ $c }}" {{ request('kelas') == $c ? 'selected' : '' }}>{{ \App\Models\Student::formatClass($c) }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-2xl text-xs shadow-sm transition cursor-pointer flex items-center justify-center gap-1.5">
                    <span>Filter & Cari</span>
                </button>
                @if(request('search') || request('tahun_lulus') || request('kelas'))
                    <a href="{{ route('alumni.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-2xl text-xs transition flex items-center justify-center">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Alumni Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                    Daftar Siswa Alumni (Total: {{ $alumni->total() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase font-black tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">NISN</th>
                            <th class="px-6 py-3.5">Nama Alumni</th>
                            <th class="px-6 py-3.5">Kelas Terakhir</th>
                            <th class="px-6 py-3.5">Tahun Lulus</th>
                            <th class="px-6 py-3.5">No. Ijazah / SKL</th>
                            <th class="px-6 py-3.5">Sekolah Lanjutan</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($alumni as $item)
                            <tr class="hover:bg-red-50/30 transition duration-150">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">
                                    {{ $item->nisn ?? $item->nis }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($item->foto_url)
                                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-slate-700 text-white flex items-center justify-center font-black text-xs shadow-2xs">
                                                {{ strtoupper(substr($item->nama, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('students.show', $item) }}" class="font-bold text-slate-900 hover:text-red-600 transition flex items-center gap-2">
                                                <span>{{ $item->nama }}</span>
                                                <span class="px-1.5 py-0.5 text-[9px] font-black rounded bg-emerald-100 text-emerald-800 border border-emerald-300">Alumni</span>
                                            </a>
                                            <div class="text-[11px] text-slate-400 font-medium">
                                                {{ in_array($item->jenis_kelamin, ['L', 'Laki-laki']) ? 'Laki-laki' : (in_array($item->jenis_kelamin, ['P', 'Perempuan']) ? 'Perempuan' : '-') }} &bull; Lahir: {{ $item->tanggal_lahir ? $item->tanggal_lahir->format('d/m/Y') : '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-extrabold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->kelas }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-black rounded-full bg-amber-50 text-amber-900 border border-amber-200">
                                        🎓 {{ $item->tahun_lulus ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    {{ $item->no_ijazah ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    {{ $item->sekolah_lanjutan ?: '-' }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                    <a href="{{ route('students.show', $item) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                                        👁️ Detail
                                    </a>

                                    <a href="{{ route('alumni.print-skl', $item) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-xl text-xs font-bold transition" title="Cetak Surat Keterangan Lulus">
                                        📜 SKL
                                    </a>

                                    @if(Auth::user() && Auth::user()->isAdmin())
                                        <button type="button" @click="openEditModal({{ json_encode($item) }})" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold transition cursor-pointer">
                                            ✏️ Edit
                                        </button>

                                        <form method="POST" action="{{ route('alumni.revert', $item) }}" class="inline-block" onsubmit="return confirm('Batalkan kelulusan {{ $item->nama }} dan kembalikan ke Data Siswa aktif?');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition cursor-pointer" title="Kembalikan status siswa aktif">
                                                ↩️ Batal Lulus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-xs font-semibold text-slate-400 italic">
                                    Belum ada data alumni yang tercatat. Luluskan siswa dari halaman <a href="{{ route('students.index') }}" class="text-red-600 font-bold underline">Data Siswa</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($alumni->hasPages())
                <div class="px-6 py-4 border-t border-slate-100">
                    {{ $alumni->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Edit Kelulusan Alumni -->
        <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
            <div @click.away="editModalOpen = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-100 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="font-black text-base text-slate-900">Perbarui Data Kelulusan Alumni</h3>
                        <p class="text-xs text-slate-500 font-semibold" x-text="selectedAlumni.nama + ' (' + (selectedAlumni.nisn || selectedAlumni.nis) + ')'"></p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
                </div>

                <form :action="'/alumni/' + selectedAlumni.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tahun Lulus / Angkatan *</label>
                            <input type="text" name="tahun_lulus" :value="selectedAlumni.tahun_lulus" required placeholder="Contoh: 2024/2025"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Kelulusan</label>
                            <input type="date" name="tanggal_lulus" :value="selectedAlumni.tanggal_lulus ? selectedAlumni.tanggal_lulus.substring(0, 10) : ''"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Ijazah / SKL</label>
                        <input type="text" name="no_ijazah" :value="selectedAlumni.no_ijazah" placeholder="Contoh: DN-24/D-SD/13/0012345"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sekolah / Perguruan Tinggi Lanjutan</label>
                        <input type="text" name="sekolah_lanjutan" :value="selectedAlumni.sekolah_lanjutan" placeholder="Contoh: SMP Kristen Petra Kupang"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Kelulusan</label>
                        <textarea name="catatan_kelulusan" :value="selectedAlumni.catatan_kelulusan" rows="2" placeholder="Catatan prestasi kelulusan atau keterangan lain..."
                                  class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-red-600"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-extrabold shadow-md transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
