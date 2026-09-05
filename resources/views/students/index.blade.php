<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Data Siswa NTO National Plus</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Kelola data seluruh siswa terdaftar di sekolah</p>
            </div>
            @if(Auth::user() && Auth::user()->isAdmin())
                <a href="{{ route('students.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl text-xs shadow-lg shadow-red-600/30 transition duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Tambah Siswa Baru</span>
                </a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ qrModalOpen: false, selectedStudent: null, openQrModal(student) { this.selectedStudent = student; this.qrModalOpen = true; } }">
        
        <!-- Flash Alert -->
        @if(session('success'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                    <span>{{ session('success') }}</span>
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa atau NIS..."
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

        <!-- Students Data Table Container -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                    Daftar Siswa Terdaftar (Total: {{ $students->total() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase font-black tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">NIS</th>
                            <th class="px-6 py-3.5">Siswa</th>
                            <th class="px-6 py-3.5">Kelas</th>
                            <th class="px-6 py-3.5">L/P</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            <tr class="hover:bg-red-50/40 transition duration-150">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">
                                    {{ $student->nis }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Avatar -->
                                        @if($student->foto)
                                            <img src="{{ asset($student->foto) }}" alt="{{ $student->nama }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-2xs">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-red-600 text-white flex items-center justify-center font-black text-xs shadow-2xs">
                                                {{ strtoupper(substr($student->nama, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-bold text-slate-900 flex items-center gap-2">
                                                <span>{{ $student->nama }}</span>
                                                @if($student->is_abk)
                                                    <span class="px-1.5 py-0.5 text-[9px] font-black rounded bg-amber-100 text-amber-900 border border-amber-300">ABK</span>
                                                @endif
                                            </div>
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
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button type="button" @click="openQrModal({{ json_encode($student) }})" class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 rounded-xl text-xs font-bold transition cursor-pointer">
                                        📷 QR Code
                                    </button>
                                    @if(Auth::user() && Auth::user()->isAdmin())
                                        <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition">
                                            ✏️ Edit
                                        </a>
                                        <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data siswa ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition cursor-pointer">
                                                🗑️ Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                    Tidak ada data siswa yang ditemukan.
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
                        <p class="text-xs text-slate-500 font-semibold mt-0.5" x-text="'NIS: ' + selectedStudent.nis + ' | Kelas ' + selectedStudent.kelas"></p>
                        
                        <div class="my-5 p-4 bg-slate-50 rounded-2xl border border-slate-200 inline-block shadow-xs">
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' + encodeURIComponent(selectedStudent.nis)" alt="QR Code" class="w-40 h-40 mx-auto rounded-xl">
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

    </div>
</x-app-layout>
