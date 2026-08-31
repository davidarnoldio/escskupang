<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Rekap Presensi Real-Time - ESCS Kupang</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Laporan rekapitulasi presensi bulanan & grafik statistik indikator</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('attendances.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-extrabold rounded-2xl text-xs shadow-lg shadow-blue-600/30 transition duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span>Input Presensi Harian</span>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Include Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6" x-data="{ init() { setInterval(() => window.location.reload(), 12000); } }">
        
        <!-- Filter & Live Sync Status Bar Card -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('attendances.rekap') }}" class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Bulan & Tahun</label>
                    <input type="month" name="bulan" value="{{ $bulan }}" onchange="this.form.submit()" class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                </div>

                @if(Auth::user()->isAdmin())
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Filter Kelas</label>
                        <select name="kelas" onchange="this.form.submit()" class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-600 focus:bg-white transition cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach($classList as $k)
                                <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="pt-4">
                        <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 text-blue-800 font-extrabold text-xs rounded-full border border-blue-200">
                            🏫 Kelas {{ $assignedClass }} (Wali Kelas)
                        </span>
                    </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-xs font-extrabold transition cursor-pointer">
                        Filter Rekap
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-3">
                <!-- Print Action Button -->
                <a href="{{ route('attendances.print-rekap', ['bulan' => $bulan, 'kelas' => $kelas]) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-extrabold rounded-2xl text-xs shadow-md transition cursor-pointer flex items-center gap-1.5">
                    <span>🖨️ Cetak A4</span>
                </a>
            </div>
        </div>

        <!-- School Building Photo Banner Card -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl overflow-hidden shadow-lg border border-blue-500/30 text-white p-6 relative flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl z-10">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-wider text-blue-100 border border-white/20">Laporan Rekapitulasi Presensi Resmi</span>
                <h3 class="text-xl font-black">Excellent Spirit Christian School Kupang</h3>
                <p class="text-xs text-blue-100/90 font-medium">
                    Data presensi bulan <span class="font-bold text-white">{{ \Carbon\Carbon::parse($bulan . '-01')->locale('id')->isoFormat('MMMM Y') }}</span> tercatat secara transparan dan akurat.
                </p>
            </div>
            <div class="w-full md:w-72 h-32 rounded-2xl overflow-hidden border-2 border-white/20 shadow-2xl shrink-0 z-10">
                <img src="{{ asset('images/escs-kupang-school.png') }}" alt="ESCS Kupang School" class="w-full h-full object-cover object-center" onerror="this.src='{{ asset('escs-kupang-school.png') }}'">
            </div>
        </div>

        <!-- 4 Summary Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Total Hadir</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalHadir }}</div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-lg">✓</div>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-sky-600">Total Izin</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalIzin }}</div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-sky-500/10 text-sky-600 flex items-center justify-center font-bold text-lg">ⓘ</div>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-blue-600">Total Sakit</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalSakit }}</div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold text-lg">🏥</div>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-rose-600">Total Alpa</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalAlpa }}</div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center font-bold text-lg">✕</div>
            </div>
        </div>

        <!-- Monthly Attendance Matrix Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                    Matriks Presensi Siswa (Bulan: {{ \Carbon\Carbon::parse($bulan . '-01')->locale('id')->isoFormat('MMMM Y') }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-black tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 sticky left-0 bg-slate-50 z-10">NIS</th>
                            <th class="px-4 py-3 sticky left-16 bg-slate-50 z-10">Nama Siswa</th>
                            <th class="px-4 py-3">Kelas</th>
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                <th class="px-2 py-3 text-center min-w-[28px]">{{ $d }}</th>
                            @endfor
                            <th class="px-3 py-3 text-center bg-emerald-50 text-emerald-800">H</th>
                            <th class="px-3 py-3 text-center bg-sky-50 text-sky-800">I</th>
                            <th class="px-3 py-3 text-center bg-blue-50 text-blue-800">S</th>
                            <th class="px-3 py-3 text-center bg-rose-50 text-rose-800">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            @php
                                $hCount = 0; $iCount = 0; $sCount = 0; $aCount = 0;
                            @endphp
                            <tr class="hover:bg-blue-50/30 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-600 sticky left-0 bg-white z-10">
                                    {{ $student->nis }}
                                </td>
                                <td class="px-4 py-3 font-bold text-slate-900 sticky left-16 bg-white z-10 whitespace-nowrap">
                                    {{ $student->nama }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $student->kelas }}
                                    </span>
                                </td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dateStr = sprintf('%s-%02d', $bulan, $d);
                                        $att = isset($matrix[$student->id][$dateStr]) ? $matrix[$student->id][$dateStr] : null;
                                        $st = $att ? $att->status : '-';
                                        
                                        if ($st == 'hadir') $hCount++;
                                        elseif ($st == 'izin') $iCount++;
                                        elseif ($st == 'sakit') $sCount++;
                                        elseif ($st == 'alpa') $aCount++;

                                        $badgeBg = match($st) {
                                            'hadir' => 'bg-emerald-500 text-white font-bold',
                                            'izin' => 'bg-sky-500 text-white font-bold',
                                            'sakit' => 'bg-blue-600 text-white font-bold',
                                            'alpa' => 'bg-rose-500 text-white font-bold',
                                            'libur' => 'bg-slate-300 text-slate-700 font-bold',
                                            default => 'text-slate-300',
                                        };
                                    @endphp
                                    <td class="px-1 py-3 text-center text-[10px]">
                                        <span class="inline-block w-5 h-5 leading-5 rounded-md text-center {{ $badgeBg }}">
                                            {{ strtoupper(substr($st, 0, 1)) }}
                                        </span>
                                    </td>
                                @endfor
                                <td class="px-3 py-3 text-center font-extrabold text-emerald-700 bg-emerald-50/50">{{ $hCount }}</td>
                                <td class="px-3 py-3 text-center font-extrabold text-sky-700 bg-sky-50/50">{{ $iCount }}</td>
                                <td class="px-3 py-3 text-center font-extrabold text-blue-700 bg-blue-50/50">{{ $sCount }}</td>
                                <td class="px-3 py-3 text-center font-extrabold text-rose-700 bg-rose-50/50">{{ $aCount }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $daysInMonth + 8 }}" class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                    Tidak ada data siswa untuk ditampilkan pada rekap ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
