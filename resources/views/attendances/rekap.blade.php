<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Rekap Presensi Real-Time - NTO National Plus</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Laporan rekapitulasi presensi bulanan & grafik statistik indikator</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('attendances.index') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl text-xs shadow-lg shadow-red-600/30 transition duration-200 cursor-pointer">
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
                    <input type="month" name="bulan" value="{{ $bulan }}" onchange="this.form.submit()" class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition cursor-pointer">
                </div>

                @if(Auth::user()->isAdmin())
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Filter Kelas</label>
                        <select name="kelas" onchange="this.form.submit()" class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach($classList as $k)
                                <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="pt-4">
                        <span class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-800 font-extrabold text-xs rounded-full border border-red-200">
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
        <div class="bg-gradient-to-r from-red-700 to-red-900 rounded-3xl overflow-hidden shadow-lg border border-red-500/30 text-white p-6 relative flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl z-10">
                <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-black uppercase tracking-wider text-red-100 border border-white/20">Laporan Rekapitulasi Presensi Resmi</span>
                <h3 class="text-xl font-black">NTO National Plus Primary School</h3>
                <p class="text-xs text-red-100/90 font-medium">
                    Data presensi bulan <span class="font-bold text-white">{{ \Carbon\Carbon::parse($bulan . '-01')->locale('id')->isoFormat('MMMM Y') }}</span> tercatat secara transparan dan akurat.
                </p>
            </div>
            <div class="w-24 h-24 rounded-2xl overflow-hidden border-2 border-white/20 shadow-2xl shrink-0 z-10 bg-white p-2 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus" class="w-full h-full object-contain" onerror="this.src='{{ asset('logo.png') }}'">
            </div>
        </div>

        <!-- 5 Summary Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Total Hadir (Siswa)</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalHadir }} <span class="text-xs font-semibold text-slate-400">/ {{ $students->count() }} siswa</span></div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-emerald-600/10 text-emerald-600 flex items-center justify-center font-bold text-lg">✓</div>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-amber-200 bg-amber-50/20 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-amber-700">Total Terlambat (Siswa)</div>
                    <div class="text-2xl font-black text-amber-900 mt-1">{{ $totalTerlambat }} <span class="text-xs font-semibold text-amber-700/70">siswa</span></div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-700 flex items-center justify-center font-bold text-lg">⏰</div>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-700">Total Izin (Siswa)</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalIzin }} <span class="text-xs font-semibold text-slate-400">/ {{ $students->count() }} siswa</span></div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-slate-700/10 text-slate-700 flex items-center justify-center font-bold text-lg">ⓘ</div>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-slate-700">Total Sakit (Siswa)</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalSakit }} <span class="text-xs font-semibold text-slate-400">/ {{ $students->count() }} siswa</span></div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-slate-700/10 text-slate-700 flex items-center justify-center font-bold text-lg">🏥</div>
            </div>

            <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-100 flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider text-red-600">Total Alpa (Siswa)</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalAlpa }} <span class="text-xs font-semibold text-slate-400">/ {{ $students->count() }} siswa</span></div>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-red-600/10 text-red-600 flex items-center justify-center font-bold text-lg">✕</div>
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
                            <th class="px-3 py-3 text-center bg-red-50 text-red-800">H</th>
                            <th class="px-3 py-3 text-center bg-teal-50 text-red-900">I</th>
                            <th class="px-3 py-3 text-center bg-red-100/60 text-red-900">S</th>
                            <th class="px-3 py-3 text-center bg-rose-50 text-rose-800">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            @php
                                $hCount = 0; $iCount = 0; $sCount = 0; $aCount = 0;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 font-mono font-bold text-slate-600 sticky left-0 bg-white z-10">{{ $student->nis }}</td>
                                <td class="px-4 py-3 font-bold text-slate-900 sticky left-16 bg-white z-10 whitespace-nowrap">{{ $student->nama }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-500 whitespace-nowrap">{{ $student->kelas }}</td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dateKey = $bulan . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                                        $attRecord = $matrix[$student->id][$dateKey] ?? null;
                                        $st = $attRecord ? $attRecord->status : '-';
                                        
                                        if ($st === 'hadir') $hCount++;
                                        elseif ($st === 'izin') $iCount++;
                                        elseif ($st === 'sakit') $sCount++;
                                        elseif ($st === 'alpa') $aCount++;

                                        $isLate = ($st === 'hadir') && str_contains(strtolower($attRecord->keterangan ?? ''), 'terlambat');

                                        $badgeBg = match(true) {
                                            $isLate => 'bg-amber-400 text-amber-950 font-black border border-amber-500 shadow-2xs',
                                            $st === 'hadir' => 'bg-emerald-600 text-white font-bold',
                                            $st === 'izin' => 'bg-slate-700 text-white font-bold',
                                            $st === 'sakit' => 'bg-slate-600 text-white font-bold',
                                            $st === 'alpa' => 'bg-red-600 text-white font-bold',
                                            $st === 'libur' => 'bg-slate-200 text-slate-600 font-medium',
                                            default => 'text-slate-300',
                                        };
                                    @endphp
                                    <td class="px-1 py-3 text-center text-[10px]">
                                        <span class="inline-block w-5 h-5 leading-5 rounded-md text-center {{ $badgeBg }}" title="{{ $isLate ? ($attRecord->keterangan ?? 'Hadir Terlambat') : strtoupper($st) }}">
                                            {{ strtoupper(substr($st, 0, 1)) }}
                                        </span>
                                    </td>
                                @endfor
                                <td class="px-3 py-3 text-center font-extrabold text-emerald-700 bg-emerald-50/50">{{ $hCount }}</td>
                                <td class="px-3 py-3 text-center font-extrabold text-slate-800 bg-slate-100/50">{{ $iCount }}</td>
                                <td class="px-3 py-3 text-center font-extrabold text-slate-800 bg-slate-100/50">{{ $sCount }}</td>
                                <td class="px-3 py-3 text-center font-extrabold text-red-700 bg-red-50/50">{{ $aCount }}</td>
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

        <!-- Dedicated Late Students Recap Table Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-amber-500/10 border-b border-amber-200/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">⏰</span>
                    <h3 class="font-black text-amber-950 text-xs uppercase tracking-wider">
                        Daftar Rekapitulasi Siswa Terlambat (Bulan: {{ \Carbon\Carbon::parse($bulan . '-01')->locale('id')->isoFormat('MMMM Y') }})
                    </h3>
                </div>
                <span class="px-3 py-1 bg-amber-200 text-amber-950 text-[11px] font-black rounded-full border border-amber-300">
                    Total: {{ $lateStudentsList->count() }} Kali Keterlambatan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-black tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">No</th>
                            <th class="px-6 py-3">NIS</th>
                            <th class="px-6 py-3">Nama Siswa</th>
                            <th class="px-6 py-3">Kelas</th>
                            <th class="px-6 py-3">Tanggal Scan</th>
                            <th class="px-6 py-3">Jam Scan</th>
                            <th class="px-6 py-3">Batas Jam</th>
                            <th class="px-6 py-3">Durasi Terlambat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($lateStudentsList as $idx => $item)
                            <tr class="hover:bg-amber-50/30 transition duration-150">
                                <td class="px-6 py-4 font-bold text-slate-400 text-xs">{{ $idx + 1 }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">{{ $item->student->nis }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900 flex items-center gap-2">
                                    <span>{{ $item->student->nama }}</span>
                                    @if($item->student->is_abk)
                                        <span class="px-1.5 py-0.5 text-[9px] font-black rounded bg-amber-100 text-amber-900 border border-amber-300">ABK</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-600">Kelas {{ $item->student->kelas }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->isoFormat('D MMMM Y') }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-amber-900 bg-amber-50 px-2 py-1 rounded-lg inline-block border border-amber-200">{{ $item->waktu_scan }} WITA</td>
                                <td class="px-6 py-4 font-mono font-semibold text-slate-500">{{ $item->jam_terlambat }} WITA</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 text-xs font-black text-amber-950 bg-amber-200 rounded-lg border border-amber-300 shadow-2xs inline-flex items-center gap-1">
                                        <span>Terlambat {{ $item->late_formatted }}</span>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                    Tidak ada data keterlambatan siswa untuk bulan ini. Semua siswa hadir tepat waktu!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
