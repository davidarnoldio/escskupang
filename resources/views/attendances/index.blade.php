<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Presensi Siswa Harian NTO National Plus</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                    @if(Auth::user()->isAdmin())
                        Catat dan perbarui kehadiran siswa harian secara manual atau kelompok
                    @else
                        Pantau kehadiran siswa harian kelas Anda (Mode Lihat Saja)
                    @endif
                </p>
            </div>
            <a href="{{ route('attendances.rekap') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold rounded-2xl text-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>Lihat Rekap Presensi</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6"
        x-data="{ setAllHadir() { document.querySelectorAll('.radio-hadir').forEach(el => el.checked = true); }, setAllLibur() { document.querySelectorAll('.radio-libur').forEach(el => el.checked = true); } }">

        <!-- Flash Alert -->
        @if(session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div
                class="p-4 bg-rose-50 border border-rose-200 text-rose-900 rounded-2xl text-xs font-extrabold flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Filter & Batch Actions Header Card -->
        <div
            class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Tanggal
                        Presensi</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                        class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition cursor-pointer">
                </div>

                @if(Auth::user()->isAdmin())
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Pilih
                            Kelas</label>
                        <select name="kelas" onchange="this.form.submit()"
                            class="py-2 px-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-red-600 focus:bg-white transition cursor-pointer">
                            <option value="">Semua Kelas</option>
                            @foreach($classList as $k)
                                <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>
                                    {{ \App\Models\Student::formatClass($k) }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="pt-4">
                        <span
                            class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-800 font-extrabold text-xs rounded-full border border-red-200">
                            🏫 {{ \App\Models\Student::formatWaliClass($assignedClass) }}
                        </span>
                    </div>
                @endif

                <div class="pt-4">
                    <button type="submit"
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-xs font-extrabold transition cursor-pointer">
                        Filter
                    </button>
                </div>
            </form>

            @if(Auth::user()->isAdmin())
                <!-- Quick Batch Buttons (Khusus Administrator) -->
                <div class="flex items-center gap-2 pt-2 md:pt-0">
                    <button type="button" @click="setAllHadir()"
                        class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-800 border border-red-200 rounded-2xl text-xs font-extrabold transition cursor-pointer shadow-2xs">
                        ✓ Set Semua Hadir
                    </button>
                    <button type="button" @click="setAllLibur()"
                        class="px-4 py-2 bg-teal-50 hover:bg-teal-100 text-teal-900 border border-teal-200 rounded-2xl text-xs font-extrabold transition cursor-pointer shadow-2xs">
                        🏖️ Set Semua Libur
                    </button>
                </div>
            @else
                <div class="pt-2 md:pt-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-2xl text-xs font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                        <span>ℹ️</span>
                        <span>Mode Pratinjau / Hanya Lihat Saja</span>
                    </span>
                </div>
            @endif
        </div>

        <!-- Attendance Form & Table Container -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            @if(Auth::user()->isAdmin())
                <form method="POST" action="{{ route('attendances.store') }}">
                    @csrf
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            @endif

                <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <h3 class="font-black text-slate-900 text-xs uppercase tracking-wider">
                            Daftar Presensi Siswa (Tanggal:
                            {{ \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM Y') }})
                        </h3>
                        @if(!Auth::user()->isAdmin())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-800 border border-amber-200">
                                <span>🔒</span>
                                <span>Khusus Guru: Hanya Melihat</span>
                            </span>
                        @endif
                    </div>

                    @if(Auth::user()->isAdmin())
                        <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl text-xs shadow-lg shadow-red-600/30 transition cursor-pointer flex items-center gap-1.5 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span>Simpan Presensi</span>
                        </button>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-700">
                        <thead
                            class="bg-slate-50 text-slate-500 text-[11px] uppercase font-black tracking-wider border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">NIS</th>
                                <th class="px-6 py-3.5">Siswa</th>
                                <th class="px-6 py-3.5">Kelas</th>
                                <th class="px-6 py-3.5">Status Presensi</th>
                                <th class="px-6 py-3.5">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($students as $student)
                                @php
                                    $att = $attendances->get($student->id);
                                    $currentStatus = $att ? $att->status : 'hadir';

                                    $isLateStudent = false;
                                    $lateMinutesDisplay = 0;
                                    $lateFormatted = '';

                                    if ($att && $currentStatus === 'hadir') {
                                        $ket = $att->keterangan ?? '';
                                        if (str_contains(strtolower($ket), 'terlambat')) {
                                            $isLateStudent = true;

                                            $jamTerlambatConfig = $student->is_abk
                                                ? \App\Models\Setting::get('jam_terlambat_abk', '08:30')
                                                : \App\Models\Setting::get('jam_terlambat', '07:30');

                                            // 1. Priority: Extract exact scan timestamp [HH:MM:SS] from keterangan
                                            if (preg_match('/\[(\d{2}:\d{2}:\d{2})\]/', $ket, $timeMatches)) {
                                                $scanCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $timeMatches[1], 'Asia/Makassar');
                                                $thresholdCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
                                                if ($scanCarbon->greaterThan($thresholdCarbon)) {
                                                    $lateMinutesDisplay = abs((int) $scanCarbon->diffInMinutes($thresholdCarbon));
                                                }
                                            }
                                            // 2. Priority: Extract explicit "Terlambat X menit" string
                                            elseif (preg_match('/Terlambat\s+(\d+)\s+menit/i', $ket, $m)) {
                                                $lateMinutesDisplay = (int) $m[1];
                                            }
                                            // 3. Priority: Fallback to updated_at timestamp in WITA
                                            else {
                                                $scanTime = \Carbon\Carbon::parse($att->updated_at)->timezone('Asia/Makassar');
                                                $threshold = \Carbon\Carbon::parse($att->tanggal . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');
                                                if ($scanTime->greaterThan($threshold)) {
                                                    $lateMinutesDisplay = abs((int) $scanTime->diffInMinutes($threshold));
                                                }
                                            }

                                            if ($lateMinutesDisplay >= 60) {
                                                $hrs = floor($lateMinutesDisplay / 60);
                                                $mins = $lateMinutesDisplay % 60;
                                                $lateFormatted = $mins > 0 ? "{$hrs}j {$mins}m" : "{$hrs}j";
                                            } else {
                                                $lateFormatted = $lateMinutesDisplay > 0 ? "{$lateMinutesDisplay}m" : '';
                                            }
                                        }
                                    }
                                @endphp
                                <tr class="hover:bg-red-50/30 transition duration-150">
                                    <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">
                                        {{ $student->nis }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($student->foto_url)
                                                <img src="{{ $student->foto_url }}" alt="{{ $student->nama }}"
                                                    class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0">
                                            @else
                                                <div
                                                    class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-xs flex items-center justify-center border border-slate-200 shrink-0">
                                                    {{ strtoupper(substr($student->nama, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                                    <span>{{ $student->nama }}</span>
                                                    @if($student->is_abk)
                                                        <span
                                                            class="px-1.5 py-0.5 text-[9px] font-black rounded bg-amber-100 text-amber-900 border border-amber-300">ABK</span>
                                                    @endif
                                                </div>
                                                @if($att && ($att->jam_masuk || $att->jam_pulang))
                                                    <div
                                                        class="text-[10px] font-mono text-slate-500 font-bold mt-0.5 flex items-center gap-2">
                                                        @if($att->jam_masuk) <span
                                                            class="bg-slate-100 px-1.5 py-0.5 rounded">In:
                                                        {{ substr($att->jam_masuk, 0, 5) }}</span> @endif
                                                        @if($att->jam_pulang) <span
                                                            class="bg-indigo-50 text-indigo-700 px-1.5 py-0.5 rounded">Out:
                                                        {{ substr($att->jam_pulang, 0, 5) }}</span> @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-block px-3 py-1 text-xs font-extrabold rounded-full bg-red-50 text-red-700 border border-red-200/80">
                                            {{ $student->formatted_kelas }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(Auth::user()->isAdmin())
                                            <!-- Mode Admin: Formulir Radio Interaktif -->
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $student->id }}][status]"
                                                        value="hadir"
                                                        class="radio-hadir text-red-600 focus:ring-red-500 w-4 h-4 cursor-pointer"
                                                        {{ $currentStatus == 'hadir' ? 'checked' : '' }}>
                                                    @if($isLateStudent)
                                                        <span
                                                            class="text-xs font-black text-amber-950 bg-amber-100 px-2 py-0.5 rounded border border-amber-300 flex items-center gap-1 shadow-2xs">
                                                            <span>Hadir</span>
                                                            <span
                                                                class="text-[10px] bg-amber-200 text-amber-950 px-1 py-0.2 rounded font-extrabold">(Terlambat
                                                                {{ $lateFormatted ?: ($lateMinutesDisplay . 'm') }})</span>
                                                        </span>
                                                    @else
                                                        <span
                                                            class="text-xs font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-200">Hadir</span>
                                                    @endif
                                                </label>

                                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $student->id }}][status]"
                                                        value="izin"
                                                        class="text-teal-700 focus:ring-teal-600 w-4 h-4 cursor-pointer" {{ $currentStatus == 'izin' ? 'checked' : '' }}>
                                                    <span
                                                        class="text-xs font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded border border-teal-200">Izin</span>
                                                </label>

                                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $student->id }}][status]"
                                                        value="sakit"
                                                        class="text-amber-700 focus:ring-amber-500 w-4 h-4 cursor-pointer" {{ $currentStatus == 'sakit' ? 'checked' : '' }}>
                                                    <span
                                                        class="text-xs font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-300">Sakit</span>
                                                </label>

                                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $student->id }}][status]"
                                                        value="alpa"
                                                        class="text-rose-600 focus:ring-rose-500 w-4 h-4 cursor-pointer" {{ $currentStatus == 'alpa' ? 'checked' : '' }}>
                                                    <span
                                                        class="text-xs font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">Alpa</span>
                                                </label>

                                                <label class="inline-flex items-center gap-1 cursor-pointer">
                                                    <input type="radio" name="attendances[{{ $student->id }}][status]"
                                                        value="libur"
                                                        class="radio-libur text-slate-600 focus:ring-slate-500 w-4 h-4 cursor-pointer"
                                                        {{ $currentStatus == 'libur' ? 'checked' : '' }}>
                                                    <span
                                                        class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-300">Libur</span>
                                                </label>
                                            </div>
                                        @else
                                            <!-- Mode Guru: Hanya Melihat Status Badge (Read-Only) -->
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if(!$att)
                                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                        <span>⏳</span>
                                                        <span>Belum Presensi</span>
                                                    </span>
                                                @elseif($currentStatus === 'hadir')
                                                    @if($isLateStudent)
                                                        <span class="inline-flex items-center gap-1.5 text-xs font-black text-amber-950 bg-amber-100 px-3 py-1 rounded-xl border border-amber-300 shadow-2xs">
                                                            <span>✔ Hadir</span>
                                                            <span class="text-[10px] bg-amber-200 text-amber-950 px-1.5 py-0.5 rounded-md font-extrabold">
                                                                (Terlambat {{ $lateFormatted ?: ($lateMinutesDisplay . 'm') }})
                                                            </span>
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 text-xs font-extrabold text-emerald-800 bg-emerald-50 px-3 py-1 rounded-xl border border-emerald-200 shadow-2xs">
                                                            <span>✔</span>
                                                            <span>Hadir</span>
                                                        </span>
                                                    @endif
                                                @elseif($currentStatus === 'izin')
                                                    <span class="inline-flex items-center gap-1 text-xs font-extrabold text-teal-800 bg-teal-50 px-3 py-1 rounded-xl border border-teal-200 shadow-2xs">
                                                        <span>📄</span>
                                                        <span>Izin</span>
                                                    </span>
                                                @elseif($currentStatus === 'sakit')
                                                    <span class="inline-flex items-center gap-1 text-xs font-extrabold text-amber-800 bg-amber-50 px-3 py-1 rounded-xl border border-amber-200 shadow-2xs">
                                                        <span>🏥</span>
                                                        <span>Sakit</span>
                                                    </span>
                                                @elseif($currentStatus === 'alpa')
                                                    <span class="inline-flex items-center gap-1 text-xs font-extrabold text-rose-800 bg-rose-50 px-3 py-1 rounded-xl border border-rose-200 shadow-2xs">
                                                        <span>❌</span>
                                                        <span>Alpa</span>
                                                    </span>
                                                @elseif($currentStatus === 'libur')
                                                    <span class="inline-flex items-center gap-1 text-xs font-extrabold text-slate-700 bg-slate-100 px-3 py-1 rounded-xl border border-slate-300 shadow-2xs">
                                                        <span>🏖️</span>
                                                        <span>Libur</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(Auth::user()->isAdmin())
                                            <input type="text" name="attendances[{{ $student->id }}][keterangan]"
                                                value="{{ $att ? $att->keterangan : '' }}" placeholder="Catatan tambahan..."
                                                class="w-full px-3 py-1.5 border rounded-xl text-xs font-medium focus:ring-2 focus:ring-red-600 transition {{ $isLateStudent ? 'bg-amber-50/70 border-amber-300 text-amber-950 font-bold' : 'bg-slate-50 border-slate-200' }}">
                                        @else
                                            <div class="text-xs font-medium text-slate-700">
                                                @if($att && $att->keterangan)
                                                    <span class="inline-block py-1 px-2.5 rounded-lg {{ $isLateStudent ? 'bg-amber-50 text-amber-950 border border-amber-200 font-semibold' : 'bg-slate-50 text-slate-800 border border-slate-200' }}">
                                                        {{ $att->keterangan }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 italic">-</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
                                        class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                        Tidak ada siswa terdaftar di {{ $kelas ? 'kelas ' . $kelas : 'sistem' }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            @if(Auth::user()->isAdmin())
                </form>
            @endif
        </div>
    </div>
</x-app-layout>
