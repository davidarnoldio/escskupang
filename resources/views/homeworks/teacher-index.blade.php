<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Pekerjaan Rumah (PR) & Tugas Sekolah') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3 text-emerald-800 font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3 text-rose-800 font-semibold text-sm">
                    <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Form Buat PR Baru -->
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm space-y-4" x-data="{ targetType: 'all' }">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Buat PR / Tugas Baru</span>
                </h3>

                <form action="{{ route('homeworks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilihan Target Penerima PR</label>
                        <select name="target_type" x-model="targetType" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="all">Bisa Semua (Seluruh Siswa dalam Kelas)</option>
                            <option value="student">Bisa Pilih Per Siswa (Siswa Spesifik)</option>
                        </select>
                    </div>

                    <div x-show="targetType === 'student'" style="display: none;">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pilih Siswa Spesifik (Cari Nama / NIS)</label>
                        
                        <div x-data="{
                            open: false,
                            search: '',
                            selectedId: '',
                            selectedName: '',
                            students: {{ json_encode($studentsInClass->map(fn($s) => ['id' => $s->id, 'nama' => $s->nama, 'nis' => $s->nis])) }},
                            get filteredStudents() {
                                if (!this.search) return this.students;
                                return this.students.filter(s => 
                                    s.nama.toLowerCase().includes(this.search.toLowerCase()) || 
                                    s.nis.toLowerCase().includes(this.search.toLowerCase())
                                );
                            },
                            selectStudent(s) {
                                this.selectedId = s.id;
                                this.selectedName = s.nama + ' (NIS: ' + s.nis + ')';
                                this.open = false;
                                this.search = '';
                            }
                        }" class="relative">

                            <input type="hidden" name="student_id" :value="selectedId">

                            <button type="button" @click="open = !open" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-left text-xs font-medium flex items-center justify-between focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-2xs">
                                <span x-text="selectedName || '-- Cari & Pilih Siswa --'" :class="{ 'text-slate-400': !selectedName, 'text-slate-900 font-bold': selectedName }"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div x-show="open" @click.away="open = false" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-2xl shadow-2xl z-50 p-2.5 flex flex-col space-y-2 max-h-60" style="display: none;">
                                <input type="text" x-model="search" placeholder="🔍 Ketik nama atau NIS siswa untuk mencari..." class="w-full text-xs px-3 py-2 rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" autofocus>
                                
                                <div class="overflow-y-auto divide-y divide-slate-100 flex-1">
                                    <template x-for="s in filteredStudents" :key="s.id">
                                        <button type="button" @click="selectStudent(s)" class="w-full text-left px-3 py-2 hover:bg-emerald-50 text-xs font-semibold text-slate-700 hover:text-emerald-900 rounded-lg flex justify-between items-center transition">
                                            <span x-text="s.nama"></span>
                                            <span class="text-[10px] text-slate-400 font-normal" x-text="'NIS: ' + s.nis"></span>
                                        </button>
                                    </template>
                                    <div x-show="filteredStudents.length === 0" class="py-4 text-center text-xs text-slate-400 italic">
                                        Siswa tidak ditemukan...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kelas Target</label>
                        @if($assignedClass)
                            <input type="text" readonly value="{{ $assignedClass }}" class="w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                            <input type="hidden" name="kelas" value="{{ $assignedClass }}">
                        @else
                            <select name="kelas" required class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-emerald-500 focus:border-emerald-500">
                                @foreach($classes as $cls)
                                    <option value="{{ $cls }}">{{ $cls }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mata Pelajaran</label>
                        <input type="text" name="mata_pelajaran" required placeholder="Contoh: Matematika / Bahasa Indonesia" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Judul PR / Tugas</label>
                        <input type="text" name="judul" required placeholder="Contoh: Latihan Perkalian Bab 3 Halaman 45" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Batas Waktu Pengumpulan (Deadline)</label>
                        <input type="datetime-local" name="deadline" required value="{{ date('Y-m-d\TH:i', strtotime('+2 days 23:59')) }}" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-emerald-500 focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Deskripsi PR & Instruksi Pengerjaan</label>
                        <textarea name="deskripsi" rows="3" required placeholder="Kerjakan soal no 1-10 di buku tulis, lalu foto hasil pengerjaan secara jelas..." class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">File Lampiran Guru (Opsional)</label>
                        <input type="file" name="lampiran_guru" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-500/20 hover:from-emerald-700 hover:to-teal-800 transition">
                        Posting Pemberitahuan PR
                    </button>
                </form>
            </div>

            <!-- List PR yang sudah dibuat -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">Daftar PR / Tugas {{ $assignedClass ? "Kelas {$assignedClass}" : 'Semua Kelas' }}</h3>
                </div>

                <div class="space-y-4">
                    @forelse($homeworks as $hw)
                        <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        {{ $hw->kelas }}
                                    </span>
                                    <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ $hw->mata_pelajaran }}
                                    </span>
                                    @if($hw->student)
                                        <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-amber-100 text-amber-800 border border-amber-300">
                                            👤 Khusus: {{ $hw->student->nama }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                            👥 Semua Siswa
                                        </span>
                                    @endif
                                </div>
                                <h4 class="text-base font-black text-slate-900">{{ $hw->judul }}</h4>
                                <p class="text-xs text-slate-500 font-normal line-clamp-2">{{ $hw->deskripsi }}</p>

                                <div class="flex items-center gap-4 text-[11px] text-slate-400 font-semibold pt-1">
                                    <span>⏰ Deadline: <strong class="text-rose-600">{{ $hw->deadline ? $hw->deadline->format('d M Y H:i') : '-' }}</strong></span>
                                    <span>📬 Pengumpulan: <strong class="text-emerald-600">{{ $hw->submissions->count() }} Siswa</strong></span>
                                </div>
                            </div>

                            @php
                                $ungradedCount = $hw->submissions->whereNull('nilai')->count();
                            @endphp
                            <div class="shrink-0 flex items-center gap-2 w-full md:w-auto">
                                <a href="{{ route('homeworks.submissions', $hw) }}" class="flex-1 md:flex-initial px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white hover:from-emerald-700 hover:to-teal-800 rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                                    <span>📊 Rekap Nilai & Pengumpulan</span>
                                    @if($ungradedCount > 0)
                                        <span class="px-2 py-0.5 text-[9px] font-black bg-amber-400 text-amber-950 rounded-full animate-pulse shadow-sm">
                                            {{ $ungradedCount }} Perlu Dinilai
                                        </span>
                                    @endif
                                </a>

                                <form action="{{ route('homeworks.destroy', $hw) }}" method="POST" onsubmit="return confirm('Hapus PR ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 text-slate-400 hover:text-rose-600 transition">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-2xl border border-slate-100 text-center text-slate-400 text-xs font-medium">
                            Belum ada pemberitahuan PR yang dibuat.
                        </div>
                    @endforelse

                    @if($homeworks->hasPages())
                        <div class="pt-2">
                            {{ $homeworks->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
