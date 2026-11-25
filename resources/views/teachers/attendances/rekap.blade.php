<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Rekapitulasi Presensi Guru NTO</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">Laporan rekap bulanan kehadiran seluruh dewan guru dan staf (Periode: {{ $carbonMonth->locale('id')->isoFormat('MMMM YYYY') }})</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher-attendances.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-2xl text-xs transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Presensi Harian</span>
                </a>
                <a href="{{ route('teacher-attendances.print-rekap', ['bulan' => $bulan]) }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl text-xs transition cursor-pointer shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span>Cetak Rekap (A4 PDF)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Summary Statistics Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Guru</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ count($teachers) }}</p>
                <p class="text-[10px] font-semibold text-slate-400 mt-0.5">Staf Pengajar</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-emerald-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Total Hadir</p>
                <p class="text-2xl font-black text-emerald-700 mt-1">{{ $grandHadir }}</p>
                <p class="text-[10px] font-semibold text-emerald-600/80 mt-0.5">Kumulatif Bulan Ini</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-amber-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-amber-600">Terlambat</p>
                <p class="text-2xl font-black text-amber-700 mt-1">{{ $grandTerlambat }}</p>
                <p class="text-[10px] font-semibold text-amber-600/80 mt-0.5">Masuk Lewat Jam</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-sky-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-sky-600">Total Izin</p>
                <p class="text-2xl font-black text-sky-700 mt-1">{{ $grandIzin }}</p>
                <p class="text-[10px] font-semibold text-sky-600/80 mt-0.5">Disetujui Admin</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-indigo-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-indigo-600">Total Sakit</p>
                <p class="text-2xl font-black text-indigo-700 mt-1">{{ $grandSakit }}</p>
                <p class="text-[10px] font-semibold text-indigo-600/80 mt-0.5">Keterangan Dokter</p>
            </div>
            <div class="bg-white p-4 rounded-3xl border border-rose-100 shadow-xs">
                <p class="text-[10px] font-black uppercase tracking-wider text-rose-600">Total Alpa</p>
                <p class="text-2xl font-black text-rose-700 mt-1">{{ $grandAlpa }}</p>
                <p class="text-[10px] font-semibold text-rose-600/80 mt-0.5">Tanpa Keterangan</p>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('teacher-attendances.rekap') }}" class="flex flex-wrap items-center gap-3 w-full">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Pilih Bulan & Tahun</label>
                    <input type="month" name="bulan" value="{{ $bulan }}" onchange="this.form.submit()" 
                           class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition cursor-pointer">
                </div>

                <div class="flex-1 min-w-[200px]">
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Cari Guru</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Filter nama guru..." 
                           class="w-full py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-medium text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition">
                </div>

                <div class="pt-4 flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-xs font-extrabold transition cursor-pointer">
                        Filter
                    </button>
                    @if($search || $bulan !== now()->format('Y-m'))
                        <a href="{{ route('teacher-attendances.rekap') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-2xl text-xs font-bold transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Monthly Matrix Table (7 Hari Penuh: Senin - Minggu) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="font-extrabold text-sm text-slate-900">
                        Matriks Presensi Guru (7 Hari Aktif: Senin–Minggu): {{ $carbonMonth->locale('id')->isoFormat('MMMM YYYY') }}
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Keterangan: <span class="font-bold text-emerald-600">H = Hadir</span>, <span class="font-bold text-amber-600">T = Terlambat</span>, <span class="font-bold text-sky-600">I = Izin</span>, <span class="font-bold text-indigo-600">S = Sakit</span>, <span class="font-bold text-rose-600">A = Alpa</span> &bull; Disertai jam masuk &amp; pulang</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-black px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-lg">H</span>
                    <span class="text-[11px] font-black px-2.5 py-1 bg-amber-50 text-amber-800 rounded-lg">T</span>
                    <span class="text-[11px] font-black px-2.5 py-1 bg-sky-50 text-sky-800 rounded-lg">I</span>
                    <span class="text-[11px] font-black px-2.5 py-1 bg-indigo-50 text-indigo-800 rounded-lg">S</span>
                    <span class="text-[11px] font-black px-2.5 py-1 bg-rose-50 text-rose-800 rounded-lg">A</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-center text-xs border-collapse">
                    <thead class="bg-slate-50 text-[10px] font-black uppercase text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 text-left w-10 sticky left-0 bg-slate-50 z-10 border-r border-slate-200">#</th>
                            <th class="py-3 px-4 text-left min-w-[200px] sticky left-10 bg-slate-50 z-10 border-r border-slate-200 shadow-xs">Nama Guru</th>
                            @for($d = 1; $d <= $daysInMonth; $d++)
                                @php
                                    $curDate = \Carbon\Carbon::parse(sprintf('%s-%02d', $bulan, $d));
                                @endphp
                                <th class="py-2.5 px-1.5 min-w-[44px] border-r border-slate-100">
                                    <span>{{ $d }}</span>
                                    <span class="block text-[8px] font-bold text-slate-400 uppercase">{{ substr($curDate->locale('id')->minDayName, 0, 2) }}</span>
                                </th>
                            @endfor
                            <th class="py-3 px-2 bg-emerald-50 text-emerald-900 border-l-2 border-slate-300 w-12 font-black">H</th>
                            <th class="py-3 px-2 bg-amber-50 text-amber-900 border-r border-slate-100 w-12 font-black">T</th>
                            <th class="py-3 px-2 bg-sky-50 text-sky-900 border-r border-slate-100 w-12 font-black">I</th>
                            <th class="py-3 px-2 bg-indigo-50 text-indigo-900 border-r border-slate-100 w-12 font-black">S</th>
                            <th class="py-3 px-2 bg-rose-50 text-rose-900 border-r border-slate-100 w-12 font-black">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($teachers as $idx => $teacher)
                            @php
                                $totals = $teacherTotals[$teacher->id] ?? ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-2.5 px-3 text-left font-bold text-slate-400 sticky left-0 bg-white border-r border-slate-200 z-10">
                                    {{ $idx + 1 }}
                                </td>
                                <td class="py-2.5 px-4 text-left sticky left-10 bg-white border-r border-slate-200 shadow-xs z-10">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-xl bg-slate-900 text-amber-400 font-black text-[10px] flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 leading-tight">{{ $teacher->name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $teacher->getAssignedClass() ?? 'Guru Pengajar' }}</p>
                                        </div>
                                    </div>
                                </td>
                                @for($d = 1; $d <= $daysInMonth; $d++)
                                    @php
                                        $dStr = sprintf('%s-%02d', $bulan, $d);
                                        $att = $matrix[$teacher->id][$dStr] ?? null;
                                        $jamIn = ($att && $att->jam_masuk) ? substr($att->jam_masuk, 0, 5) : null;
                                        $jamOut = ($att && $att->jam_pulang) ? substr($att->jam_pulang, 0, 5) : null;
                                        $isLate = $att && $att->status === 'hadir' && (($jamIn && $jamIn > $jamTerlambatConfig) || str_contains(strtolower($att->keterangan ?? ''), 'terlambat'));
                                        $isEarlyOut = $att && $att->status === 'hadir' && $jamOut && ($jamOut < $jamPulangConfig || str_contains(strtolower($att->keterangan ?? ''), 'pulang awal'));
                                    @endphp
                                    <td class="py-2 px-1 border-r border-slate-100 align-top">
                                        @if(!$att)
                                            <span class="text-slate-200">-</span>
                                        @else
                                            <div class="inline-flex flex-col items-center gap-0.5">
                                                @if($att->status === 'hadir')
                                                    @if($isLate)
                                                        <span class="inline-block w-5 h-5 leading-5 text-[10px] font-black rounded bg-amber-100 text-amber-900" title="{{ $att->keterangan ?? 'Hadir Terlambat' }}">T</span>
                                                    @else
                                                        <span class="inline-block w-5 h-5 leading-5 text-[10px] font-black rounded bg-emerald-100 text-emerald-900" title="{{ $att->keterangan ?? 'Hadir Tepat Waktu' }}">H</span>
                                                    @endif
                                                @elseif($att->status === 'izin')
                                                    <span class="inline-block w-5 h-5 leading-5 text-[10px] font-black rounded bg-sky-100 text-sky-900" title="Izin: {{ $att->keterangan }}">I</span>
                                                @elseif($att->status === 'sakit')
                                                    <span class="inline-block w-5 h-5 leading-5 text-[10px] font-black rounded bg-indigo-100 text-indigo-900" title="Sakit: {{ $att->keterangan }}">S</span>
                                                @elseif($att->status === 'alpa')
                                                    <span class="inline-block w-5 h-5 leading-5 text-[10px] font-black rounded bg-rose-100 text-rose-900" title="Alpa (Tanpa Keterangan)">A</span>
                                                @endif
                                                @if($jamIn || $jamOut)
                                                    <span class="font-mono text-[8px] leading-tight text-slate-600 font-bold whitespace-nowrap">{{ $jamIn ?? '--:--' }}</span>
                                                    <span class="font-mono text-[8px] leading-tight whitespace-nowrap {{ $isEarlyOut ? 'text-orange-600 font-black' : 'text-indigo-600 font-semibold' }}">{{ $jamOut ?? '--:--' }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                @endfor
                                <td class="py-2 px-2 bg-emerald-50/50 text-emerald-900 font-extrabold border-l-2 border-slate-300">
                                    {{ $totals['hadir'] }}
                                </td>
                                <td class="py-2 px-2 bg-amber-50/50 text-amber-900 font-extrabold border-r border-slate-100">
                                    {{ $totals['terlambat'] }}
                                </td>
                                <td class="py-2 px-2 bg-sky-50/50 text-sky-900 font-extrabold border-r border-slate-100">
                                    {{ $totals['izin'] }}
                                </td>
                                <td class="py-2 px-2 bg-indigo-50/50 text-indigo-900 font-extrabold border-r border-slate-100">
                                    {{ $totals['sakit'] }}
                                </td>
                                <td class="py-2 px-2 bg-rose-50/50 text-rose-900 font-extrabold border-r border-slate-100">
                                    {{ $totals['alpa'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $daysInMonth + 7 }}" class="py-12 text-center text-slate-400">
                                    <p class="font-extrabold text-sm">Tidak ada data dewan guru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detailed Teacher Check-In & Check-Out Log Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-base">🕒</span>
                    <div>
                        <h3 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                            Detail Jam Masuk &amp; Jam Pulang Guru ({{ $carbonMonth->locale('id')->isoFormat('MMMM YYYY') }})
                        </h3>
                        <p class="text-[11px] text-slate-500 font-medium">Rincian waktu masuk, waktu pulang, status (termasuk Terlambat &amp; Pulang Awal), serta catatan harian guru</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-slate-200/70 text-slate-800 text-[11px] font-black rounded-full">
                    {{ $detailLogs->count() }} Catatan
                </span>
            </div>

            <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase font-black tracking-wider border-b border-slate-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-5 py-3">No</th>
                            <th class="px-5 py-3">Tanggal &amp; Hari</th>
                            <th class="px-5 py-3">Nama Guru</th>
                            <th class="px-5 py-3">Penugasan</th>
                            <th class="px-5 py-3 text-center">Jam Masuk</th>
                            <th class="px-5 py-3 text-center">Jam Pulang</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($detailLogs as $idx => $log)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-5 py-3 font-bold text-slate-400">{{ $idx + 1 }}</td>
                                <td class="px-5 py-3 font-semibold text-slate-800 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($log->tanggal)->locale('id')->isoFormat('dddd, D MMM Y') }}
                                </td>
                                <td class="px-5 py-3 font-bold text-slate-900 whitespace-nowrap">{{ $log->teacher->name }}</td>
                                <td class="px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">{{ $log->teacher->getAssignedClass() ?? 'Guru Pengajar' }}</td>
                                <td class="px-5 py-3 text-center font-mono font-extrabold text-emerald-700">
                                    {{ $log->jam_masuk ? $log->jam_masuk . ' WITA' : '-' }}
                                </td>
                                <td class="px-5 py-3 text-center font-mono font-extrabold {{ $log->is_early_out ? 'text-orange-700' : 'text-indigo-700' }}">
                                    {{ $log->jam_pulang ? $log->jam_pulang . ' WITA' : '-' }}
                                </td>
                                <td class="px-5 py-3 text-center whitespace-nowrap">
                                    @if($log->status === 'hadir')
                                        <div class="inline-flex items-center gap-1">
                                            @if($log->is_late)
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">TERLAMBAT</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-emerald-100 text-emerald-900 border border-emerald-300">HADIR</span>
                                            @endif
                                            @if($log->is_early_out)
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-orange-100 text-orange-900 border border-orange-300">PULANG AWAL</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-slate-100 text-slate-800 border border-slate-300 uppercase">{{ $log->status }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-600">{{ $log->keterangan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                    Belum ada riwayat presensi guru pada bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
