<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Kelola Data Guru & Wali Kelas</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                    Manajemen akun guru, penugasan wali kelas, dan biodata Dapodik lengkap (Khusus Administrator)
                </p>
            </div>
            <div>
                <a href="{{ route('teachers.create') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-red-600/30 transition duration-200 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Tambah Guru Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{
        showModal: false,
        selectedTeacher: null,
        openDetail(teacher) {
            this.selectedTeacher = teacher;
            this.showModal = true;
        }
    }" class="space-y-6">

        <!-- Flash Message Alert -->
        @if (session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-extrabold rounded-2xl shadow-2xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Search Filter Bar -->
        <div
            class="bg-white p-5 rounded-3xl shadow-sm border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <form method="GET" action="{{ route('teachers.index') }}" class="w-full md:w-auto flex items-center gap-2">
                <div class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama, email, NIK, atau NUPTK..."
                        class="w-full pl-9 pr-3.5 py-2.5 text-xs font-semibold rounded-2xl bg-slate-50 border-slate-200 focus:ring-2 focus:ring-red-600 focus:bg-white transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl shadow-sm transition cursor-pointer">Cari</button>
                @if(!empty($search))
                    <a href="{{ route('teachers.index') }}"
                        class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-2xl transition">Reset</a>
                @endif
            </form>
            <div class="text-xs font-extrabold text-slate-500">
                Total Guru Terdaftar: <span class="text-red-600 font-black text-sm">{{ $teachers->count() }}</span> Akun
            </div>
        </div>

        <!-- Grouped by Class Matrix View -->
        <div class="space-y-6">
            @php
                $officialClasses = \App\Models\Student::OFFICIAL_CLASSES;
            @endphp

            @foreach(array_merge($officialClasses, ['Unassigned']) as $class)
                @php
                    $classTeachers = $teachersByClass[$class] ?? [];
                @endphp

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <!-- Class Header Bar -->
                    <div class="px-6 py-4 bg-slate-50/60 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-red-600 shadow-xs"></div>
                            <h3 class="font-extrabold text-slate-900 text-sm">
                                {{ $class === 'Unassigned' ? 'Guru Tanpa Penugasan Kelas' : \App\Models\Student::formatWaliClass($class) }}
                            </h3>
                        </div>
                        <span
                            class="px-3 py-1 bg-white text-slate-700 font-extrabold text-xs rounded-full border border-slate-200 shadow-2xs">
                            {{ count($classTeachers) }} Guru
                        </span>
                    </div>

                    <div class="p-6">
                        @if(empty($classTeachers))
                            <div class="py-6 text-center text-xs font-semibold text-slate-400 italic">
                                Belum ada akun guru yang ditugaskan di tingkat ini.
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                @foreach($classTeachers as $teacher)
                                    <div
                                        class="bg-slate-50/80 rounded-2xl p-5 border border-slate-200/80 space-y-4 hover:border-red-300 hover:bg-red-50/30 transition duration-200 relative flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-center gap-3">
                                                    <div
                                                        class="w-10 h-10 rounded-full bg-red-600 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                                        {{ strtoupper(substr($teacher->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <h4 class="font-extrabold text-slate-900 text-xs leading-tight">
                                                            {{ $teacher->name }}
                                                        </h4>
                                                        <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                            {{ $teacher->email }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4 pt-3 border-t border-slate-200/60 space-y-2">
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="font-semibold text-slate-500">Kelas Binaan:</span>
                                                    <span
                                                        class="font-extrabold text-red-700 bg-red-50 px-2 py-0.5 rounded-full border border-red-200">
                                                        {{ $teacher->getAssignedClass() ? \App\Models\Student::formatClass($teacher->getAssignedClass()) : 'Belum Ditentukan' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="font-semibold text-slate-500">Status Pegawai:</span>
                                                    <span
                                                        class="font-bold text-slate-800 bg-slate-200/70 px-2 py-0.5 rounded-full text-[10px]">
                                                        {{ $teacher->status_kepegawaian ?? 'Belum Diisi' }}
                                                    </span>
                                                </div>
                                                @if($teacher->nuptk)
                                                    <div class="flex items-center justify-between text-[11px]">
                                                        <span class="font-semibold text-slate-500">NUPTK:</span>
                                                        <span class="font-mono text-slate-700 text-[10px]">{{ $teacher->nuptk }}</span>
                                                    </div>
                                                @endif
                                                @if($teacher->no_hp)
                                                    <div class="flex items-center justify-between text-[11px]">
                                                        <span class="font-semibold text-slate-500">No. WhatsApp:</span>
                                                        <span class="font-mono text-slate-700 text-[10px]">{{ $teacher->no_hp }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons: Switch Akun + Detail + Edit + Delete -->
                                        <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between gap-1.5">
                                            <form method="POST" action="{{ route('impersonate.switch', $teacher) }}"
                                                class="inline-block flex-1">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full py-1.5 px-2 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-xl text-xs transition cursor-pointer shadow-xs flex items-center justify-center gap-1">
                                                    <span>⚡ Switch</span>
                                                </button>
                                            </form>

                                            <!-- Detail Biodata Button -->
                                            <button type="button"
                                                @click="openDetail({{ json_encode($teacher) }})"
                                                class="p-1.5 bg-blue-100 hover:bg-blue-200 text-blue-900 rounded-xl transition cursor-pointer"
                                                title="Lihat Biodata Lengkap Dapodik">
                                                📋
                                            </button>

                                            <!-- Cetak QR -->
                                            <a href="{{ \Illuminate\Support\Facades\Route::has('teachers.qr-card') ? route('teachers.qr-card', $teacher) : url('/teachers/' . $teacher->id . '/qr-card') }}"
                                                target="_blank"
                                                class="p-1.5 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-xl transition"
                                                title="Cetak Kartu QR Guru">
                                                🪪
                                            </a>

                                            <!-- Edit Guru -->
                                            <a href="{{ route('teachers.edit', $teacher) }}"
                                                class="p-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl transition"
                                                title="Edit Biodata & Akun Guru">
                                                ✏️
                                            </a>

                                            <!-- Hapus Guru -->
                                            <form method="POST" action="{{ route('teachers.destroy', $teacher) }}"
                                                class="inline-block"
                                                onsubmit="return confirm('Yakin ingin menghapus data guru ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 rounded-xl transition cursor-pointer"
                                                    title="Hapus Guru">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- MODAL DETAIL BIODATA GURU DAPODIK -->
        <div x-show="showModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background backdrop -->
                <div x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs"
                    @click="showModal = false" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Panel -->
                <div x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl border border-slate-100">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center font-black">
                                📋
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900" x-text="selectedTeacher?.name"></h3>
                                <p class="text-xs text-slate-500 font-mono" x-text="selectedTeacher?.email"></p>
                            </div>
                        </div>
                        <button type="button" @click="showModal = false"
                            class="p-2 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Modal Content (Scrollable) -->
                    <div class="py-6 max-h-[70vh] overflow-y-auto space-y-6 text-xs">
                        <!-- Group 1: Data Pribadi -->
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px] mb-3 text-red-600 flex items-center gap-1.5">
                                <span>👤</span> Data Pribadi Guru
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-slate-400 font-semibold block">NIK:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="selectedTeacher?.nik || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Jenis Kelamin:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.jenis_kelamin === 'L' ? 'Laki-laki (L)' : (selectedTeacher?.jenis_kelamin === 'P' ? 'Perempuan (P)' : '-')"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Tempat, Tanggal Lahir:</span>
                                    <span class="font-bold text-slate-800" x-text="(selectedTeacher?.tempat_lahir || '') + (selectedTeacher?.tanggal_lahir ? ', ' + selectedTeacher?.tanggal_lahir : '') || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Nama Ibu Kandung:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.nama_ibu_kandung || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Nomor Telepon / HP:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="selectedTeacher?.no_hp || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Kelas Binaan:</span>
                                    <span class="font-bold text-red-600" x-text="selectedTeacher?.assigned_class || 'Belum Ditugaskan'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Group 2: Alamat Tempat Tinggal -->
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px] mb-3 text-emerald-600 flex items-center gap-1.5">
                                <span>🏠</span> Alamat Tempat Tinggal
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div class="col-span-2 sm:col-span-3">
                                    <span class="text-slate-400 font-semibold block">Alamat Jalan:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.alamat || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">RT / RW:</span>
                                    <span class="font-bold text-slate-800" x-text="(selectedTeacher?.rt || '-') + ' / ' + (selectedTeacher?.rw || '-')"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Dusun:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.dusun || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Desa / Kelurahan:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.desa_kelurahan || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Kecamatan:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.kecamatan || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Kode Pos:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.kode_pos || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Koordinat (Lintang, Bujur):</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="(selectedTeacher?.lintang || '-') + ', ' + (selectedTeacher?.bujur || '-')"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Group 3: Status Kepegawaian -->
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px] mb-3 text-amber-600 flex items-center gap-1.5">
                                <span>💼</span> Status Kepegawaian & Penugasan
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-slate-400 font-semibold block">Status Kepegawaian:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.status_kepegawaian || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">NIY / NIGK:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="selectedTeacher?.niy_nigk || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">NUPTK:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="selectedTeacher?.nuptk || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">SK Pengangkatan:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.sk_pengangkatan || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">TMT Pengangkatan:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.tmt_pengangkatan || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Lembaga Pengangkat:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.lembaga_pengangkat || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Sumber Gaji:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.sumber_gaji || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Keahlian Laboratorium:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.keahlian_laboratorium || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Tangani Kebutuhan Khusus:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.mampu_menangani_kebutuhan_khusus || '-'"></span>
                                </div>
                                <div class="col-span-2 sm:col-span-3" x-show="selectedTeacher?.alasan_keluar_kerja">
                                    <span class="text-slate-400 font-semibold block">Alasan Keluar Kerja:</span>
                                    <span class="font-bold text-rose-600" x-text="selectedTeacher?.alasan_keluar_kerja"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Group 4: Sertifikasi & Prestasi -->
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                            <h4 class="font-extrabold text-slate-800 uppercase tracking-wider text-[11px] mb-3 text-purple-600 flex items-center gap-1.5">
                                <span>🎓</span> Riwayat Sertifikasi & Prestasi Guru
                            </h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-slate-400 font-semibold block">Jenis Sertifikasi:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.jenis_sertifikasi || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Nomor Sertifikasi:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="selectedTeacher?.nomor_sertifikasi || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Tahun Sertifikasi:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.tahun_sertifikasi || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Bidang Studi:</span>
                                    <span class="font-bold text-slate-800" x-text="selectedTeacher?.bidang_studi_sertifikasi || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">NRG:</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="selectedTeacher?.nrg || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-400 font-semibold block">Nomor Peserta (Prestasi):</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="selectedTeacher?.nomor_peserta || '-'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a :href="'/teachers/' + selectedTeacher?.id + '/edit'"
                            class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition">
                            ✏️ Edit Data Guru Ini
                        </a>
                        <button type="button" @click="showModal = false"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl transition cursor-pointer">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>