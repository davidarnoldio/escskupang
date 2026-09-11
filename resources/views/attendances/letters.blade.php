<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span>Penerimaan Surat Izin / Sakit Orang Tua</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Daftar surat izin dan surat dokter yang dikirimkan Orang Tua murid untuk dikonfirmasi oleh Wali Kelas.
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(Auth::user()->isTeacher())
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-red-50 text-red-900 font-bold text-xs rounded-xl border border-red-200 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                        <span>Wali Kelas {{ $assignedClass ?? 'Semua Kelas' }}</span>
                    </span>
                @elseif(Auth::user()->isAdmin())
                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-amber-50 text-amber-900 font-bold text-xs rounded-xl border border-amber-200 shadow-xs">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <span>Administrator (Mode Pantau)</span>
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        previewModal: false, 
        previewImg: '', 
        previewTitle: '', 
        previewIsPdf: false,
        verifyModal: false,
        verifyAction: 'setujui',
        verifyUrl: '',
        verifyStudent: '',
        verifyTanggal: '',
        verifyNote: '',
        deleteModal: false,
        deleteUrl: '',
        deleteStudent: ''
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-2 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm flex items-center gap-2 shadow-xs">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Info Notice for Admin -->
            @if(Auth::user()->isAdmin() && !Auth::user()->isTeacher())
                <div class="p-4 bg-amber-50/80 border border-amber-200 text-amber-900 rounded-2xl text-xs flex items-start gap-3 shadow-xs">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <strong class="font-bold text-slate-900">Hak Akses Khusus Wali Kelas:</strong>
                        <p class="mt-0.5 text-slate-600">
                            Sebagai Administrator, Anda dapat memantau seluruh riwayat surat izin dan pratinjau berkas. Wewenang untuk <strong>menerima (menyetujui)</strong> atau <strong>menolak</strong> surat izin merupakan hak eksklusif <strong>Guru / Wali Kelas</strong> masing-masing.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Summary KPI Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Surat</div>
                        <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total'] ?? 0 }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-lg">
                        📬
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-amber-200/80 bg-amber-50/30 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Menunggu Konfirmasi</div>
                        <div class="text-2xl font-black text-amber-700 mt-1">{{ $stats['menunggu'] ?? 0 }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-lg">
                        ⏳
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-emerald-200/80 bg-emerald-50/30 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Diterima / Disetujui</div>
                        <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['disetujui'] ?? 0 }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-lg">
                        ✅
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-rose-200/80 bg-rose-50/30 shadow-xs flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Ditolak</div>
                        <div class="text-2xl font-black text-rose-700 mt-1">{{ $stats['ditolak'] ?? 0 }}</div>
                    </div>
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-lg">
                        ❌
                    </div>
                </div>
            </div>

            <!-- Filter & Search Section -->
            <div class="bg-white p-4 rounded-3xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('attendances.letters') }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ empty($statusFilter) ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua ({{ $stats['total'] ?? 0 }})
                    </a>
                    <a href="{{ route('attendances.letters', ['status' => 'menunggu', 'search' => $search]) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'menunggu' ? 'bg-amber-500 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}">
                        ⏳ Menunggu ({{ $stats['menunggu'] ?? 0 }})
                    </a>
                    <a href="{{ route('attendances.letters', ['status' => 'disetujui', 'search' => $search]) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'disetujui' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}">
                        ✅ Diterima ({{ $stats['disetujui'] ?? 0 }})
                    </a>
                    <a href="{{ route('attendances.letters', ['status' => 'ditolak', 'search' => $search]) }}" 
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition {{ $statusFilter === 'ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 text-rose-800 hover:bg-rose-100 border border-rose-200' }}">
                        ❌ Ditolak ({{ $stats['ditolak'] ?? 0 }})
                    </a>
                </div>

                <form method="GET" action="{{ route('attendances.letters') }}" class="flex items-center gap-2">
                    @if($statusFilter)
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                    @endif
                    <div class="relative w-full md:w-64">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama atau NIS..."
                               class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    @if($search)
                        <a href="{{ route('attendances.letters', ['status' => $statusFilter]) }}" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Card Table List -->
            <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-900">Daftar Berkas Surat Masuk</h3>
                    <span class="text-xs font-semibold text-slate-500">Menampilkan {{ $attendances->count() }} dari {{ $attendances->total() }} Surat</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-900 text-xs font-bold text-slate-200 uppercase tracking-wider">
                            <tr>
                                <th class="px-5 py-4">Tanggal</th>
                                <th class="px-5 py-4">NIS & Nama Siswa</th>
                                <th class="px-5 py-4">Kelas</th>
                                <th class="px-5 py-4">Jenis Permohonan</th>
                                <th class="px-5 py-4">Status Surat</th>
                                <th class="px-5 py-4 text-center">Foto / File Surat</th>
                                <th class="px-5 py-4 text-center">Aksi / Respons</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attendances as $att)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-5 py-4 font-bold text-slate-900 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($att->tanggal)->isoFormat('D MMMM YYYY') }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-slate-900">{{ $att->student->nama ?? '-' }}</div>
                                        <div class="text-xs font-mono text-slate-400">NIS: {{ $att->student->nis ?? '-' }}</div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-900 border border-red-200">
                                            {{ $att->student->kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if($att->status == 'sakit')
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300 uppercase">
                                                🏥 Sakit
                                            </span>
                                        @elseif($att->status == 'alpa')
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-rose-100 text-rose-900 border border-rose-300 uppercase">
                                                ⚠️ Alpa (Ditolak)
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-md text-xs font-extrabold bg-teal-50 text-teal-900 border border-teal-200 uppercase">
                                                ✉️ Izin
                                            </span>
                                        @endif
                                        <div class="text-[11px] text-slate-500 mt-1 max-w-xs truncate" title="{{ $att->keterangan }}">
                                            {{ $att->keterangan ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $st = $att->surat_status ?? 'menunggu';
                                        @endphp
                                        @if($st === 'disetujui')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                <span>✅ Diterima / Disetujui</span>
                                            </span>
                                        @elseif($st === 'ditolak')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                                <span>❌ Ditolak</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                                <span>⏳ Menunggu Konfirmasi</span>
                                            </span>
                                        @endif

                                        @if($att->catatan_guru)
                                            <div class="text-[11px] text-slate-500 italic mt-1 max-w-xs truncate" title="{{ $att->catatan_guru }}">
                                                "{{ $att->catatan_guru }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        @if($att->surat_izin)
                                            <button type="button"
                                                @click="
                                                    previewModal = true; 
                                                    previewImg = '{{ $att->surat_izin_url }}'; 
                                                    previewTitle = 'Surat {{ ucfirst($att->status) }} - {{ addslashes($att->student->nama ?? '') }}';
                                                    previewIsPdf = {{ $att->isPdfSurat() ? 'true' : 'false' }};
                                                "
                                                class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs rounded-xl border border-red-200 inline-flex items-center gap-1.5 transition cursor-pointer shadow-2xs">
                                                📷 Pratinjau
                                            </button>
                                        @else
                                            <span class="text-slate-300 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @if(Auth::user()->isTeacher())
                                                <!-- Teacher Verify Actions: Accept / Reject -->
                                                <button type="button"
                                                    @click="
                                                        verifyModal = true;
                                                        verifyAction = 'setujui';
                                                        verifyUrl = '{{ route('attendances.verify-letter', $att) }}';
                                                        verifyStudent = '{{ addslashes($att->student->nama ?? '') }}';
                                                        verifyTanggal = '{{ \Carbon\Carbon::parse($att->tanggal)->isoFormat('D MMMM YYYY') }}';
                                                        verifyNote = 'Surat izin telah diverifikasi dan disetujui oleh Wali Kelas.';
                                                    "
                                                    title="Setujui / Terima Surat"
                                                    class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1">
                                                    <span>✔</span>
                                                    <span class="hidden md:inline">Terima</span>
                                                </button>

                                                <button type="button"
                                                    @click="
                                                        verifyModal = true;
                                                        verifyAction = 'tolak';
                                                        verifyUrl = '{{ route('attendances.verify-letter', $att) }}';
                                                        verifyStudent = '{{ addslashes($att->student->nama ?? '') }}';
                                                        verifyTanggal = '{{ \Carbon\Carbon::parse($att->tanggal)->isoFormat('D MMMM YYYY') }}';
                                                        verifyNote = 'Foto surat tidak terbaca / tidak ada keterangan jelas.';
                                                    "
                                                    title="Tolak Surat Izin"
                                                    class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition cursor-pointer flex items-center gap-1">
                                                    <span>✖</span>
                                                    <span class="hidden md:inline">Tolak</span>
                                                </button>
                                            @else
                                                <!-- Admin Read-Only Notice -->
                                                <span class="text-[11px] font-medium text-slate-400 italic">
                                                    Mode Pantau
                                                </span>
                                            @endif

                                            <!-- Delete Letter Attachment Button (Teacher & Admin) -->
                                            <button type="button"
                                                @click="
                                                    deleteModal = true;
                                                    deleteUrl = '{{ route('attendances.destroy-letter', $att) }}';
                                                    deleteStudent = '{{ addslashes($att->student->nama ?? '') }}';
                                                "
                                                title="Hapus Berkas Surat"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-400 text-xs">
                                        Belum ada data berkas surat izin / sakit yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-slate-100">
                    {{ $attendances->links() }}
                </div>
            </div>

        </div>

        <!-- 1. Modal Preview Foto / PDF Surat -->
        <div x-show="previewModal"
            class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4" @click.outside="previewModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-base text-slate-900" x-text="previewTitle"></h3>
                    <button type="button" @click="previewModal = false"
                        class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>
                
                <div class="max-h-[70vh] overflow-y-auto bg-slate-950 rounded-2xl p-2 text-center flex items-center justify-center min-h-[250px]">
                    <template x-if="!previewIsPdf">
                        <img :src="previewImg" class="max-w-full max-h-[65vh] mx-auto rounded-xl shadow-lg object-contain">
                    </template>
                    <template x-if="previewIsPdf">
                        <div class="text-white space-y-3 p-6">
                            <div class="text-5xl">📄</div>
                            <div class="text-sm font-bold">Dokumen PDF Terlampir</div>
                            <a :href="previewImg" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl inline-flex items-center gap-1.5 transition">
                                🔗 Buka File PDF di Tab Baru
                            </a>
                        </div>
                    </template>
                </div>

                <div class="flex justify-between items-center pt-2">
                    <a :href="previewImg" target="_blank"
                        class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-bold text-xs rounded-xl transition inline-flex items-center gap-1">
                        🔗 Buka File Asli
                    </a>
                    <button type="button" @click="previewModal = false"
                        class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-red-400 font-extrabold text-xs rounded-xl transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Modal Konfirmasi Terima / Tolak Surat (Khusus Guru) -->
        <div x-show="verifyModal"
            class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.outside="verifyModal = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                        <span x-text="verifyAction === 'setujui' ? '✅ Terima / Setujui Surat Izin' : '❌ Tolak Surat Izin'"></span>
                    </h3>
                    <button type="button" @click="verifyModal = false" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
                </div>

                <form :action="verifyUrl" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="action" :value="verifyAction">

                    <div class="p-3 rounded-2xl text-xs space-y-1" :class="verifyAction === 'setujui' ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-rose-50 text-rose-900 border border-rose-200'">
                        <div class="font-bold">Siswa: <span x-text="verifyStudent"></span></div>
                        <div>Tanggal Presensi: <span class="font-semibold" x-text="verifyTanggal"></span></div>
                        <template x-if="verifyAction === 'tolak'">
                            <div class="mt-1 text-[11px] text-rose-700 font-medium">
                                ⚠️ Catatan: Jika ditolak, status kehadiran siswa akan otomatis diubah menjadi <strong>ALPA</strong>.
                            </div>
                        </template>
                    </div>

                    <div>
                        <label for="catatan_guru" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <span x-text="verifyAction === 'setujui' ? 'Catatan Guru / Pesan untuk Orang Tua (Opsional)' : 'Alasan Penolakan Surat (Wajib / Opsional)'"></span>
                        </label>
                        <textarea name="catatan_guru" id="catatan_guru" rows="3" x-model="verifyNote"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-red-500 focus:bg-white transition"
                            placeholder="Tuliskan catatan atau keterangan respons Anda..."></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="verifyModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            :class="verifyAction === 'setujui' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'"
                            class="px-5 py-2 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                            <span x-text="verifyAction === 'setujui' ? 'Ya, Setujui Surat' : 'Ya, Tolak Surat'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 3. Modal Hapus Berkas Surat -->
        <div x-show="deleteModal"
            class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
            <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4" @click.outside="deleteModal = false">
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mx-auto text-xl font-bold">
                        🗑️
                    </div>
                    <h3 class="font-bold text-base text-slate-900">Hapus Berkas Surat?</h3>
                    <p class="text-xs text-slate-500">
                        Berkas foto surat izin siswa <strong x-text="deleteStudent" class="text-slate-800"></strong> akan dihapus permanen dari sistem storage.
                    </p>
                </div>

                <form :action="deleteUrl" method="POST" class="space-y-3">
                    @csrf
                    @method('DELETE')

                    <div class="flex items-center justify-center gap-2 pt-2">
                        <button type="button" @click="deleteModal = false"
                            class="w-1/2 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-1/2 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                            Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>