<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    Profil Detail Siswa
                </h2>
                <p class="text-xs text-slate-500 mt-1">Informasi identitas lengkap, status ABK, dan riwayat presensi siswa</p>
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
                    @if($student->foto_url)
                        <img src="{{ $student->foto_url }}" alt="{{ $student->nama }}" class="w-32 h-32 rounded-2xl object-cover border-4 border-white shadow-md">
                    @else
                        <div class="w-32 h-32 rounded-2xl bg-slate-900 text-red-400 border-4 border-slate-800 flex items-center justify-center font-extrabold text-4xl shadow-md">
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
                            <span class="px-3 py-0.5 bg-red-50 text-red-900 border border-red-200 font-bold text-xs rounded-full">
                                Kelas {{ $student->kelas }}
                            </span>
                            @if($student->status === 'lulus')
                                <span class="px-3 py-0.5 bg-emerald-100 text-emerald-900 border border-emerald-300 font-extrabold text-xs rounded-full flex items-center gap-1">
                                    🎓 Alumni (Angkatan {{ $student->tahun_lulus ?? '-' }})
                                </span>
                            @endif
                            @if($student->is_abk)
                                <span class="px-3 py-0.5 bg-purple-100 text-purple-900 border border-purple-200 font-bold text-xs rounded-full">
                                    Berkebutuhan Khusus (ABK)
                                </span>
                            @endif
                        </div>
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mt-1.5">
                            <p class="text-xs font-mono text-slate-500 font-bold">NISN: <span class="text-slate-800">{{ $student->nisn ?? $student->nis }}</span></p>
                            @if(auth()->user() && auth()->user()->isAdmin() && $student->nik)
                                <span class="text-slate-300">•</span>
                                <p class="text-xs font-mono text-slate-500 font-bold">NIK: <span class="text-slate-800">{{ $student->nik }}</span></p>
                            @endif
                        </div>
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
                        <a href="{{ route('students.qr-card', $student) }}" target="_blank" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-red-400 font-extrabold text-xs rounded-xl transition inline-flex items-center gap-1.5 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            <span>Kartu QR Pelajar</span>
                        </a>

                        @if($student->status === 'lulus')
                            <a href="{{ route('alumni.print-skl', $student) }}" target="_blank" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 font-extrabold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                                <span>📜 Cetak SKL (PDF)</span>
                            </a>
                        @endif

                        @if(auth()->user() && auth()->user()->isAdmin())
                            <a href="{{ route('students.edit', $student) }}" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-900 border border-red-200 font-extrabold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                                <span>✏️ Edit Data Lengkap</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- DETAIL KHUSUS ADMINISTRATOR -->
            @if(auth()->user() && auth()->user()->isAdmin())
                <div class="space-y-6">
                    <div class="flex items-center gap-2 px-1">
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold rounded-lg flex items-center gap-1">
                            🔒 Akses Khusus Administrator
                        </span>
                        <span class="text-xs text-slate-400">Data kependudukan, fisik, prestasi, dan identitas orang tua</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- KARTU 1: KEPENDUDUKAN & KELAHIRAN -->
                        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
                            <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                                <span>🪪</span> Dokumen Kependudukan & Kelahiran
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">NIK Siswa</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $student->nik ?: '-' }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">No. KK</span>
                                    <span class="font-mono font-bold text-slate-800">{{ $student->no_kk ?: '-' }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Tempat Lahir</span>
                                    <span class="font-bold text-slate-800">{{ $student->tempat_lahir ?: '-' }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Tanggal Lahir</span>
                                    <span class="font-bold text-slate-800">{{ $student->tanggal_lahir ? $student->tanggal_lahir->isoFormat('D MMMM YYYY') : '-' }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">No. Akta Kelahiran</span>
                                    <span class="font-bold text-slate-800">{{ $student->no_akta_kelahiran ?: '-' }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Agama</span>
                                    <span class="font-bold text-slate-800">{{ $student->agama ?: '-' }}</span>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-xl col-span-2">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Kewarganegaraan</span>
                                    <span class="font-bold text-slate-800">{{ $student->kewarganegaraan ?: 'WNI' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- KARTU 2: PRESTASI & FISIK -->
                        <div class="space-y-6">
                            <!-- PRESTASI -->
                            <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
                                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                                    <span>🏆</span> Bidang Prestasi Siswa
                                </h4>
                                <div class="p-4 bg-amber-50/50 rounded-2xl border border-amber-200/60">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori:</span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-200 text-amber-900">
                                            {{ $student->kategori_prestasi ?: 'Tidak Ada' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-700 font-medium">
                                        {{ $student->keterangan_prestasi ?: 'Belum ada keterangan prestasi tercatat.' }}
                                    </p>
                                </div>
                            </div>

                            <!-- FISIK (ANTROPOMETRI) -->
                            <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4">
                                <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                                    <span>📏</span> Data Fisik Siswa (Periodik)
                                </h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                                    <div class="p-3 bg-slate-50 rounded-xl text-center">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Tinggi Badan</span>
                                        <span class="font-extrabold text-base text-slate-900">{{ $student->tinggi_badan ? $student->tinggi_badan . ' cm' : '-' }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl text-center">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Berat Badan</span>
                                        <span class="font-extrabold text-base text-slate-900">{{ $student->berat_badan ? $student->berat_badan . ' kg' : '-' }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl text-center">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Lingkar Kepala</span>
                                        <span class="font-extrabold text-base text-slate-900">{{ $student->lingkar_kepala ? $student->lingkar_kepala . ' cm' : '-' }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl text-center">
                                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Saudara</span>
                                        <span class="font-extrabold text-base text-slate-900">{{ $student->jumlah_saudara_kandung ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KARTU 3: DATA ORANG TUA (AYAH & IBU) -->
                        <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 space-y-4 md:col-span-2">
                            <h4 class="font-bold text-sm text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                                <span>👨‍👩‍👧</span> Data Orang Tua Kandung
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- AYAH -->
                                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70 space-y-3">
                                    <div class="font-bold text-xs text-slate-800 flex items-center gap-1.5 pb-2 border-b border-slate-200/60">
                                        <span>👨</span> Data Ayah
                                    </div>
                                    <div class="space-y-2 text-xs">
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">Nama Lengkap</span>
                                            <span class="font-bold text-slate-800">{{ $student->nama_ayah ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">NIK Ayah</span>
                                            <span class="font-mono font-bold text-slate-800">{{ $student->nik_ayah ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">Tahun Lahir</span>
                                            <span class="font-bold text-slate-800">{{ $student->tahun_lahir_ayah ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">Pendidikan Terakhir</span>
                                            <span class="font-bold text-slate-800">{{ $student->pendidikan_ayah ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1">
                                            <span class="text-slate-400">Penghasilan Bulanan</span>
                                            <span class="font-bold text-slate-800">{{ $student->penghasilan_ayah ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- IBU -->
                                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/70 space-y-3">
                                    <div class="font-bold text-xs text-slate-800 flex items-center gap-1.5 pb-2 border-b border-slate-200/60">
                                        <span>👩</span> Data Ibu
                                    </div>
                                    <div class="space-y-2 text-xs">
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">Nama Lengkap</span>
                                            <span class="font-bold text-slate-800">{{ $student->nama_ibu ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">NIK Ibu</span>
                                            <span class="font-mono font-bold text-slate-800">{{ $student->nik_ibu ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">Tahun Lahir</span>
                                            <span class="font-bold text-slate-800">{{ $student->tahun_lahir_ibu ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-100">
                                            <span class="text-slate-400">Pendidikan Terakhir</span>
                                            <span class="font-bold text-slate-800">{{ $student->pendidikan_ibu ?: '-' }}</span>
                                        </div>
                                        <div class="flex justify-between py-1">
                                            <span class="text-slate-400">Penghasilan Bulanan</span>
                                            <span class="font-bold text-slate-800">{{ $student->penghasilan_ibu ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
                                                'hadir' => 'bg-red-100 text-red-800 border-red-200',
                                                'izin' => 'bg-teal-50 text-teal-900 border-teal-200',
                                                'sakit' => 'bg-amber-100 text-amber-800 border-amber-200',
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
