<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Pengaturan Jam Presensi Sekolah</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Atur jadwal jam masuk, batas tepat waktu, dan jam pulang siswa Regular & ABK NTO National Plus</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl space-y-6">
        
        <!-- Flash Alert -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-extrabold rounded-2xl shadow-2xs flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden p-6 sm:p-8">
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-8">
                @csrf

                <!-- SECTION 1: SISWA REGULAR -->
                <div>
                    <div class="flex items-center gap-2 pb-3 mb-5 border-b border-slate-100">
                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                        <h3 class="font-extrabold text-sm text-slate-900 uppercase tracking-wider">1. Jam Presensi Siswa Regular (Umum)</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Jam Masuk -->
                        <div>
                            <label for="jam_masuk" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Jam Masuk</label>
                            <input type="time" id="jam_masuk" name="jam_masuk" value="{{ old('jam_masuk', $jamMasuk) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-extrabold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                            <p class="text-[11px] text-slate-400 font-medium mt-1">Waktu gerbang dibuka (misal: 07:00).</p>
                        </div>

                        <!-- Batas Jam Tepat Waktu -->
                        <div>
                            <label for="jam_terlambat" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Batas Tepat Waktu</label>
                            <input type="time" id="jam_terlambat" name="jam_terlambat" value="{{ old('jam_terlambat', $jamTerlambat) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-extrabold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                            <p class="text-[11px] text-slate-400 font-medium mt-1">Lewat dari ini dicatat <span class="font-bold text-rose-600">Terlambat</span>.</p>
                        </div>

                        <!-- Jam Pulang -->
                        <div>
                            <label for="jam_pulang" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-2">Jam Pulang</label>
                            <input type="time" id="jam_pulang" name="jam_pulang" value="{{ old('jam_pulang', $jamPulang) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-extrabold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                            <p class="text-[11px] text-slate-400 font-medium mt-1">Waktu selesai KBM (misal: 14:00).</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: SISWA ABK -->
                <div class="p-6 bg-indigo-50/50 border border-indigo-200/80 rounded-3xl">
                    <div class="flex items-center justify-between pb-3 mb-5 border-b border-indigo-200/60">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                            <h3 class="font-extrabold text-sm text-indigo-950 uppercase tracking-wider">2. Jam Presensi Siswa Berkebutuhan Khusus (ABK)</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-100 text-indigo-900 border border-indigo-300">♿ Jadwal Khusus ABK</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Jam Masuk ABK -->
                        <div>
                            <label for="jam_masuk_abk" class="block text-xs font-extrabold text-indigo-900 uppercase tracking-wider mb-2">Jam Masuk ABK</label>
                            <input type="time" id="jam_masuk_abk" name="jam_masuk_abk" value="{{ old('jam_masuk_abk', $jamMasukAbk) }}" required class="w-full px-4 py-2.5 bg-white border border-indigo-200 rounded-2xl text-sm font-extrabold text-indigo-950 focus:ring-2 focus:ring-indigo-600 transition cursor-pointer">
                            <p class="text-[11px] text-indigo-700 font-medium mt-1">Waktu gerbang dibuka ABK (misal: 08:00).</p>
                        </div>

                        <!-- Batas Jam Tepat Waktu ABK -->
                        <div>
                            <label for="jam_terlambat_abk" class="block text-xs font-extrabold text-indigo-900 uppercase tracking-wider mb-2">Batas Tepat Waktu ABK</label>
                            <input type="time" id="jam_terlambat_abk" name="jam_terlambat_abk" value="{{ old('jam_terlambat_abk', $jamTerlambatAbk) }}" required class="w-full px-4 py-2.5 bg-white border border-indigo-200 rounded-2xl text-sm font-extrabold text-indigo-950 focus:ring-2 focus:ring-indigo-600 transition cursor-pointer">
                            <p class="text-[11px] text-indigo-700 font-medium mt-1">Toleransi keterlambatan ABK (misal: 08:30).</p>
                        </div>

                        <!-- Jam Pulang ABK -->
                        <div>
                            <label for="jam_pulang_abk" class="block text-xs font-extrabold text-indigo-900 uppercase tracking-wider mb-2">Jam Pulang ABK</label>
                            <input type="time" id="jam_pulang_abk" name="jam_pulang_abk" value="{{ old('jam_pulang_abk', $jamPulangAbk) }}" required class="w-full px-4 py-2.5 bg-white border border-indigo-200 rounded-2xl text-sm font-extrabold text-indigo-950 focus:ring-2 focus:ring-indigo-600 transition cursor-pointer">
                            <p class="text-[11px] text-indigo-700 font-medium mt-1">Waktu kepulangan ABK (misal: 13:00).</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs shadow-lg shadow-blue-600/30 transition cursor-pointer flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simpan Semua Pengaturan Jam</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
