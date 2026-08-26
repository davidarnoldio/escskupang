<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    Profil Detail Siswa
                </h2>
                <p class="text-xs text-slate-500 mt-1">Informasi lengkap, status ABK, dan riwayat presensi siswa</p>
            </div>
            <a href="{{ route('students.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded-xl text-xs transition">
                ← Kembali ke Data Siswa
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Profile Summary Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xs border border-slate-200/80 flex flex-col md:flex-row items-center md:items-start gap-6">
                <!-- Avatar / Photo -->
                <div class="relative shrink-0">
                    @if($student->foto)
                        <img src="{{ asset($student->foto) }}" alt="{{ $student->nama }}" class="w-32 h-32 rounded-2xl object-cover border-4 border-white shadow-md">
                    @else
                        <div class="w-32 h-32 rounded-2xl bg-slate-900 text-sky-400 border-4 border-slate-800 flex items-center justify-center font-extrabold text-4xl shadow-md">
                            {{ strtoupper(substr($student->nama, 0, 2)) }}
                        </div>
                    @endif
                    @if($student->is_abk)
                        <span class="absolute -bottom-2 -right-2 px-3 py-1 bg-purple-600 text-white font-extrabold text-[10px] rounded-full shadow-md border-2 border-white flex items-center gap-1">
                            ♿ ABK
                        </span>
                    @endif
                </div>

                <!-- Info Block -->
                <div class="flex-1 text-center md:text-left space-y-3">
                    <div>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                            <h3 class="font-extrabold text-2xl text-slate-900">{{ $student->nama }}</h3>
                            <span class="px-3 py-0.5 bg-sky-50 text-sky-900 border border-sky-200 font-bold text-xs rounded-full">
                                Kelas {{ $student->kelas }}
                            </span>
                            @if($student->is_abk)
                                <span class="px-3 py-0.5 bg-purple-100 text-purple-900 border border-purple-200 font-bold text-xs rounded-full">
                                    Berkebutuhan Khusus (ABK)
                                </span>
                            @endif
                        </div>
                        <p class="text-xs font-mono text-slate-400 font-bold mt-1">NIS: {{ $student->nis }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-600">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Jenis Kelamin</span>
                            <span class="font-bold text-slate-800">{{ $student->jenis_kelamin == 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Telepon Orang Tua</span>
                            <span class="font-bold text-slate-800">{{ $student->telepon ?: '-' }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 sm:col-span-2">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Alamat Rumah</span>
                            <span class="font-bold text-slate-800">{{ $student->alamat ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-wrap items-center justify-center md:justify-start gap-3">
                        <a href="{{ route('students.qr-card', $student) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-sky-400 font-extrabold text-xs rounded-xl transition inline-flex items-center gap-1.5 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>Kartu QR Pelajar</span>
                        </a>
                        <a href="{{ route('students.edit', $student) }}" class="px-4 py-2 bg-sky-50 hover:bg-sky-100 text-sky-900 border border-sky-200 font-extrabold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                            <span>✏️ Edit Data</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Attendance History Table -->
            <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h4 class="font-bold text-base text-slate-900">Riwayat Presensi Siswa</h4>
                    <span class="text-xs font-semibold text-slate-500">Total: {{ $student->attendances->count() }} Catatan</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-900 text-xs font-bold text-slate-200 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Tanggal</th>
                                <th class="px-6 py-3.5">Status Presensi</th>
                                <th class="px-6 py-3.5">Keterangan / Waktu Scan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($student->attendances->sortByDesc('tanggal') as $att)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        {{ \Carbon\Carbon::parse($att->tanggal)->isoFormat('D MMMM YYYY') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badges = [
                                                'hadir' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'izin' => 'bg-sky-50 text-sky-900 border-sky-200',
                                                'sakit' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'alpa' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                'libur' => 'bg-purple-100 text-purple-800 border-purple-200',
                                            ];
                                        @endphp
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase border {{ $badges[$att->status] ?? 'bg-slate-100 text-slate-700' }}">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-slate-700">
                                        {{ $att->keterangan ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-slate-400 text-xs">
                                        Belum ada riwayat presensi tercatat untuk siswa ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
