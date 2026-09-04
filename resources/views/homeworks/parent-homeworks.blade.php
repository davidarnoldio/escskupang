<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tugas Sekolah & Pekerjaan Rumah (PR)') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

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

        <!-- Student Info Banner -->
        <div class="bg-gradient-to-r from-blue-900 to-indigo-950 p-6 rounded-3xl text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-600/30 border border-blue-400/30 flex items-center justify-center font-black text-lg text-blue-300 shadow-inner">
                    {{ strtoupper(substr($student->nama, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight">{{ $student->nama }}</h3>
                    <p class="text-xs text-slate-300 font-medium">Kelas: <span class="font-bold text-blue-300">{{ $student->kelas }}</span> | NIS: {{ $student->nis }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3 bg-white/10 px-5 py-3 rounded-2xl border border-white/10 backdrop-blur-sm">
                <div class="text-center px-3">
                    <div class="text-[10px] font-bold text-slate-300 uppercase">Total PR</div>
                    <div class="text-lg font-black text-white">{{ $stats['total_pr'] }}</div>
                </div>
                <div class="w-px h-8 bg-white/20"></div>
                <div class="text-center px-3">
                    <div class="text-[10px] font-bold text-emerald-300 uppercase">Sudah Kumpul</div>
                    <div class="text-lg font-black text-emerald-400">{{ $stats['sudah_kumpul'] }}</div>
                </div>
            </div>
        </div>

        <!-- Homework List -->
        <div class="space-y-4" x-data="{ uploadModal: null, photoModal: null }">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Daftar Tugas & PR Kelas {{ $student->kelas }}</span>
            </h3>

            @forelse($homeworks as $hw)
                @php
                    $sub = $hw->submissions->first();
                @endphp

                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    {{ $hw->mata_pelajaran }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">Oleh: {{ $hw->teacher->name ?? 'Guru Kelas' }}</span>
                            </div>
                            <h4 class="text-lg font-black text-slate-900">{{ $hw->judul }}</h4>
                        </div>

                        <div class="text-right">
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Deadline</div>
                            <div class="text-xs font-bold text-rose-600">{{ $hw->deadline ? $hw->deadline->format('d M Y H:i') : '-' }}</div>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 font-medium bg-slate-50 p-4 rounded-2xl border border-slate-100 whitespace-pre-line">{{ $hw->deskripsi }}</p>

                    @if($hw->lampiran_guru)
                        <div class="pt-1">
                            <a href="{{ asset('storage/' . $hw->lampiran_guru) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-xs font-bold border border-blue-200 hover:bg-blue-100 transition">
                                📎 Unduh / Lihat Lampiran Soal dari Guru
                            </a>
                        </div>
                    @endif

                    <!-- Submission Status & Grade Section -->
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Pengumpulan Anda</div>
                            @if($sub)
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 text-[10px] font-black rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        ✅ Sudah Mengumpulkan Foto PR ({{ $sub->submitted_at->format('d/m/Y H:i') }})
                                    </span>
                                </div>
                                @if($sub->catatan_siswa)
                                    <p class="text-xs text-slate-500 mt-1">Catatan Anda: <em>"{{ $sub->catatan_siswa }}"</em></p>
                                @endif
                            @else
                                <span class="px-2.5 py-1 text-[10px] font-black rounded-full bg-rose-100 text-rose-800 border border-rose-300">
                                    ❌ Belum Mengumpulkan Foto PR
                                </span>
                            @endif
                        </div>

                        <!-- Grade Display -->
                        <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                            @if($sub)
                                <div class="text-right">
                                    <div class="text-[10px] font-bold text-slate-400 uppercase">Nilai Guru</div>
                                    @if($sub->nilai !== null)
                                        <span class="text-lg font-black px-3 py-0.5 rounded-full inline-block border {{ $sub->getGradeBadgeClass() }}">
                                            {{ $sub->nilai }} / 100
                                        </span>
                                    @else
                                        <span class="text-xs font-bold text-amber-600">Sedang Diperiksa</span>
                                    @endif
                                </div>

                                <button @click="photoModal = '{{ asset('storage/' . $sub->foto_pr) }}'" class="px-3 py-2 bg-white text-blue-600 hover:bg-blue-50 rounded-xl text-xs font-bold border border-slate-200 shadow-sm transition">
                                    🖼️ Lihat Foto PR
                                </button>
                            @endif

                            <button @click="uploadModal = {{ $hw->id }}" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 transition">
                                {{ $sub ? '🔄 Upload Ulang Foto PR' : '📤 Upload Foto PR' }}
                            </button>
                        </div>
                    </div>

                    @if($sub && $sub->catatan_guru)
                        <div class="bg-indigo-50/70 p-4 rounded-2xl border border-indigo-100 text-xs font-medium text-indigo-900 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-indigo-800">
                                <span>💬 Feedback & Catatan dari Guru:</span>
                            </div>
                            <p class="text-slate-700 italic">"{{ $sub->catatan_guru }}"</p>
                        </div>
                    @endif

                    <!-- Modal Upload Foto PR -->
                    <div x-show="uploadModal === {{ $hw->id }}" @click.away="uploadModal = null" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                            <h4 class="text-base font-bold text-slate-900">Upload Foto Hasil Pekerjaan Rumah (PR)</h4>
                            <p class="text-xs text-slate-500">{{ $hw->judul }} ({{ $hw->mata_pelajaran }})</p>

                            <form action="{{ route('parent.submit-homework', $hw) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ambil / Pilih Foto PR (JPG/PNG max 3MB)</label>
                                    <input type="file" name="foto_pr" required accept="image/jpeg,image/png,image/jpg,application/pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Tambahan Siswa (Opsional)</label>
                                    <textarea name="catatan_siswa" rows="2" placeholder="Contoh: Sudah selesai dikerjakan halaman 45 no 1-10..." class="w-full rounded-xl border-slate-200 text-xs font-medium"></textarea>
                                </div>

                                <div class="flex gap-2 pt-2">
                                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition">
                                        Kirim Foto PR
                                    </button>
                                    <button type="button" @click="uploadModal = null" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl border border-slate-100 text-center text-slate-400 text-xs font-medium">
                    Belum ada PR yang ditugaskan untuk kelas {{ $student->kelas }}.
                </div>
            @endforelse

            <!-- Photo View Modal -->
            <div x-show="photoModal" @click.away="photoModal = null" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-2xl max-w-xl w-full p-4 shadow-2xl relative">
                    <button @click="photoModal = null" class="absolute top-3 right-3 text-slate-400 hover:text-slate-700 text-sm font-bold">✕ Close</button>
                    <h4 class="text-sm font-bold text-slate-900 mb-3">Foto Hasil Pekerjaan Rumah (PR)</h4>
                    <div class="rounded-xl overflow-hidden border border-slate-200 max-h-[70vh] flex items-center justify-center bg-slate-950 p-2">
                        <img :src="photoModal" class="max-h-[65vh] object-contain">
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
