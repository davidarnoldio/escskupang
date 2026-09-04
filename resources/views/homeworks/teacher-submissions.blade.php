<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight pr-2">
                {{ __('Penilaian & Rekap Nilai PR: ') . $homework->judul }}
            </h2>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('homeworks.print-recap', $homework) }}?autoprint=1" target="_blank" class="px-3.5 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                    <span>🖨️</span> Cetak Rekap Nilai A4
                </a>
                <a href="{{ route('homeworks.index') }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                    ← Kembali ke Daftar PR
                </a>
            </div>
        </div>
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

        <!-- PR Header Details Card -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 text-xs font-black rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Kelas {{ $homework->kelas }}
                </span>
                <span class="px-3 py-1 text-xs font-black rounded-full bg-teal-50 text-teal-700 border border-teal-200">
                    Mata Pelajaran: {{ $homework->mata_pelajaran }}
                </span>
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700">
                    Deadline: {{ $homework->deadline ? $homework->deadline->format('d M Y H:i') : '-' }}
                </span>
            </div>
            <h3 class="text-xl font-black text-slate-900">{{ $homework->judul }}</h3>
            <p class="text-xs text-slate-600 font-medium">{{ $homework->deskripsi }}</p>
        </div>

        <!-- Grade Recap Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm text-center">
                <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">Total Siswa</p>
                <p class="text-xl font-black text-slate-900 mt-1">{{ $recap['total_siswa'] }}</p>
            </div>
            <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 shadow-sm text-center">
                <p class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider">Sudah Kumpul</p>
                <p class="text-xl font-black text-emerald-700 mt-1">{{ $recap['sudah_kumpul'] }}</p>
            </div>
            <div class="bg-rose-50/50 p-4 rounded-2xl border border-rose-100 shadow-sm text-center">
                <p class="text-[10px] font-extrabold text-rose-500 uppercase tracking-wider">Belum Kumpul</p>
                <p class="text-xl font-black text-rose-600 mt-1">{{ $recap['belum_kumpul'] }}</p>
            </div>
            <div class="bg-teal-50/50 p-4 rounded-2xl border border-teal-100 shadow-sm text-center">
                <p class="text-[10px] font-extrabold text-teal-600 uppercase tracking-wider">Sudah Dinilai</p>
                <p class="text-xl font-black text-teal-700 mt-1">{{ $recap['sudah_dinilai'] }}</p>
            </div>
            <div class="bg-emerald-50/50 p-4 rounded-2xl border border-emerald-100 shadow-sm text-center">
                <p class="text-[10px] font-extrabold text-emerald-600 uppercase tracking-wider">Rata-Rata Kelas</p>
                <p class="text-xl font-black text-emerald-700 mt-1">{{ $recap['rata_rata'] }}</p>
            </div>
            <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100 shadow-sm text-center">
                <p class="text-[10px] font-extrabold text-amber-600 uppercase tracking-wider">Nilai Max</p>
                <p class="text-xl font-black text-amber-700 mt-1">{{ $recap['nilai_tertinggi'] }}</p>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 shadow-sm text-center">
                <p class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">Nilai Min</p>
                <p class="text-xl font-black text-slate-700 mt-1">{{ $recap['nilai_terendah'] }}</p>
            </div>
        </div>

        <!-- Grade Recap & Submissions Table -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ photoModal: null, gradeModal: null }">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Rekapan Nilai & Pengumpulan Siswa Kelas {{ $homework->kelas }}</h3>
                <a href="{{ route('homeworks.print-recap', $homework) }}?autoprint=1" target="_blank" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5">
                    <span>🖨️</span> Cetak Rekap A4
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 uppercase tracking-wider font-extrabold">
                            <th class="py-3.5 px-4">Nama Siswa</th>
                            <th class="py-3.5 px-4">Status Kumpul</th>
                            <th class="py-3.5 px-4">Waktu Kumpul</th>
                            <th class="py-3.5 px-4 text-center">Foto PR</th>
                            <th class="py-3.5 px-4 text-center">Nilai (0-100)</th>
                            <th class="py-3.5 px-4">Catatan Guru</th>
                            <th class="py-3.5 px-4 text-center">Aksi Input Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($students as $st)
                            @php
                                $sub = $submissions->get($st->id);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    <div>{{ $st->nama }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">NIS: {{ $st->nis }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($sub)
                                        <span class="px-2.5 py-1 text-[10px] font-black rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            ✅ Sudah Kumpul
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-black rounded-full bg-rose-100 text-rose-800 border border-rose-300">
                                            ❌ Belum Kumpul
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 font-medium">
                                    {{ $sub ? $sub->submitted_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($sub && $sub->foto_pr)
                                        <button @click="photoModal = '{{ asset('storage/' . $sub->foto_pr) }}'" class="px-2.5 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg text-[10px] font-bold border border-emerald-200 transition inline-flex items-center gap-1">
                                            <span>🖼️ Lihat Foto</span>
                                        </button>
                                    @else
                                        <span class="text-slate-300 text-[10px]">-</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-black">
                                    @if($sub && $sub->nilai !== null)
                                        <span class="px-3 py-1 rounded-full text-xs font-black border {{ $sub->getGradeBadgeClass() }}">
                                            {{ $sub->nilai }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 font-normal text-[11px]">Belum Ada</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $sub && $sub->catatan_guru ? $sub->catatan_guru : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($sub)
                                        <button @click="gradeModal = { id: {{ $sub->id }}, name: '{{ addslashes($st->nama) }}', grade: '{{ $sub->nilai ?? '' }}', notes: '{{ addslashes($sub->catatan_guru ?? '') }}' }" class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white hover:from-emerald-700 hover:to-teal-800 rounded-xl text-xs font-bold shadow-sm transition">
                                            ✏️ Input / Edit Nilai
                                        </button>
                                    @else
                                        <span class="text-[10px] text-slate-400 font-medium">Menunggu Kumpul</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal View Foto PR -->
            <div x-show="photoModal" @click.away="photoModal = null" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-2xl max-w-2xl w-full p-4 shadow-2xl relative">
                    <button @click="photoModal = null" class="absolute top-3 right-3 text-slate-400 hover:text-slate-700 text-sm font-bold">✕ Close</button>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Foto Jawaban PR Siswa</h4>
                    <div class="rounded-xl overflow-hidden border border-slate-200 max-h-[70vh] flex items-center justify-center bg-slate-950 p-2">
                        <img :src="photoModal" class="max-h-[65vh] object-contain">
                    </div>
                </div>
            </div>

            <!-- Modal Input Nilai -->
            <div x-show="gradeModal" @click.away="gradeModal = null" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                    <h4 class="text-base font-bold text-slate-900">Input Nilai PR</h4>
                    <p class="text-xs text-slate-500">Siswa: <strong x-text="gradeModal ? gradeModal.name : ''"></strong></p>

                    <form :action="'/homework-submissions/' + (gradeModal ? gradeModal.id : '') + '/grade'" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nilai (0 - 100)</label>
                            <input type="number" name="nilai" min="0" max="100" required :value="gradeModal ? gradeModal.grade : ''" placeholder="Contoh: 95" class="w-full rounded-xl border-slate-200 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan / Feedback Guru (Opsional)</label>
                            <textarea name="catatan_guru" rows="3" :value="gradeModal ? gradeModal.notes : ''" placeholder="Contoh: Bagus sekali, tulisan rapi dan jawaban tepat!" class="w-full rounded-xl border-slate-200 text-xs font-medium focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20">
                                Simpan Nilai
                            </button>
                            <button type="button" @click="gradeModal = null" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
