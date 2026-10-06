<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>➕ Tambah Data & Akun Guru Baru</span>
                </h2>
                <p class="text-xs font-semibold text-red-600 mt-1">
                    Hanya Administrator yang memiliki akses untuk mendaftarkan akun dan biodata Dapodik guru
                </p>
            </div>
            <a href="{{ route('teachers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('teachers.store') }}" class="space-y-8">
                @csrf

                <!-- SEKSI 1: Akun Login & Penugasan Kelas -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-black text-lg">
                            1
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Akun Login & Penugasan Kelas</h3>
                            <p class="text-xs text-slate-500 font-medium">Informasi kredensial login dan kelas binaan wali kelas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap Guru -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Nama Lengkap Guru <span class="text-red-500">*</span>
                            </label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                placeholder="Contoh: Maria Skolastika, S.Pd"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                        </div>

                        <!-- Email / Username Login -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Alamat Email (Username Login) <span class="text-red-500">*</span>
                            </label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                placeholder="guru@sekolah.sch.id"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                        </div>

                        <!-- Password -->
                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Kata Sandi (Password) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                    placeholder="Minimal 6 karakter..."
                                    class="w-full px-4 py-2.5 pe-11 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 end-0 px-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                    <template x-if="!showPassword">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="showPassword">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.05 10.05 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                                    </template>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                        </div>

                        <!-- Tingkat Kelas Binaan -->
                        <div class="md:col-span-2">
                            <label for="assigned_class" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Tingkat Kelas Binaan (Wali Kelas)
                            </label>
                            <x-class-combobox name="assigned_class" id="assigned_class" :value="old('assigned_class')" :officialClasses="$officialClasses" />
                            <p class="text-[11px] text-slate-500 font-medium mt-1.5">
                                💡 Guru secara otomatis akan mengelola siswa di kelas binaan ini saat login. Bisa pilih dari opsi atau ketik manual (contoh: 6A, 5C, dsb).
                            </p>
                            <x-input-error :messages="$errors->get('assigned_class')" class="mt-1 text-xs" />
                        </div>
                    </div>
                </div>

                <!-- SEKSI 2: Data Pribadi Guru -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-lg">
                            2
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Data Pribadi Guru</h3>
                            <p class="text-xs text-slate-500 font-medium">NIK, Jenis Kelamin, Kelahiran, Ibu Kandung, dan Kontak</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIK (Nomor Induk Kependudukan)</label>
                            <input id="nik" type="text" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK sesuai KTP"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <x-input-error :messages="$errors->get('nik')" class="mt-1 text-xs" />
                        </div>

                        <!-- Jenis Kelamin (JK) -->
                        <div>
                            <label for="jenis_kelamin" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Kelamin (JK)</label>
                            <select id="jenis_kelamin" name="jenis_kelamin"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                            </select>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1 text-xs" />
                        </div>

                        <!-- Tempat Lahir -->
                        <div>
                            <label for="tempat_lahir" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tempat Lahir</label>
                            <input id="tempat_lahir" type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Contoh: Kupang"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-1 text-xs" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tanggal_lahir" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Lahir</label>
                            <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-1 text-xs" />
                        </div>

                        <!-- Nama Ibu Kandung -->
                        <div>
                            <label for="nama_ibu_kandung" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Ibu Kandung</label>
                            <input id="nama_ibu_kandung" type="text" name="nama_ibu_kandung" value="{{ old('nama_ibu_kandung') }}" placeholder="Nama ibu kandung"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <x-input-error :messages="$errors->get('nama_ibu_kandung')" class="mt-1 text-xs" />
                        </div>

                        <!-- Nomor Telepon / HP -->
                        <div>
                            <label for="no_hp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Telepon / WhatsApp</label>
                            <input id="no_hp" type="text" name="no_hp" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <x-input-error :messages="$errors->get('no_hp')" class="mt-1 text-xs" />
                        </div>
                    </div>
                </div>

                <!-- SEKSI 3: Alamat Domisili -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-lg">
                            3
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Alamat Tempat Tinggal</h3>
                            <p class="text-xs text-slate-500 font-medium">Alamat rumah, RT/RW, Dusun, Desa, Kecamatan, dan Koordinat Lintang/Bujur</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Alamat Jalan -->
                        <div>
                            <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Jalan / Rumah</label>
                            <textarea id="alamat" name="alamat" rows="2" placeholder="Jl. Contoh No. 123..."
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">{{ old('alamat') }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-1 text-xs" />
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- RT -->
                            <div>
                                <label for="rt" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">RT</label>
                                <input id="rt" type="text" name="rt" value="{{ old('rt') }}" placeholder="001"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('rt')" class="mt-1 text-xs" />
                            </div>

                            <!-- RW -->
                            <div>
                                <label for="rw" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">RW</label>
                                <input id="rw" type="text" name="rw" value="{{ old('rw') }}" placeholder="002"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('rw')" class="mt-1 text-xs" />
                            </div>

                            <!-- Dusun -->
                            <div class="col-span-2">
                                <label for="dusun" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Dusun</label>
                                <input id="dusun" type="text" name="dusun" value="{{ old('dusun') }}" placeholder="Nama Dusun"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('dusun')" class="mt-1 text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Desa / Kelurahan -->
                            <div>
                                <label for="desa_kelurahan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Desa / Kelurahan</label>
                                <input id="desa_kelurahan" type="text" name="desa_kelurahan" value="{{ old('desa_kelurahan') }}" placeholder="Desa / Kelurahan"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('desa_kelurahan')" class="mt-1 text-xs" />
                            </div>

                            <!-- Kecamatan -->
                            <div>
                                <label for="kecamatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kecamatan</label>
                                <input id="kecamatan" type="text" name="kecamatan" value="{{ old('kecamatan') }}" placeholder="Kecamatan"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('kecamatan')" class="mt-1 text-xs" />
                            </div>

                            <!-- Kode Pos -->
                            <div>
                                <label for="kode_pos" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kode Pos</label>
                                <input id="kode_pos" type="text" name="kode_pos" value="{{ old('kode_pos') }}" placeholder="85xxx"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('kode_pos')" class="mt-1 text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Lintang (Latitude) -->
                            <div>
                                <label for="lintang" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lintang (Latitude)</label>
                                <input id="lintang" type="text" name="lintang" value="{{ old('lintang') }}" placeholder="Contoh: -10.1772"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('lintang')" class="mt-1 text-xs" />
                            </div>

                            <!-- Bujur (Longitude) -->
                            <div>
                                <label for="bujur" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bujur (Longitude)</label>
                                <input id="bujur" type="text" name="bujur" value="{{ old('bujur') }}" placeholder="Contoh: 123.6067"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition">
                                <x-input-error :messages="$errors->get('bujur')" class="mt-1 text-xs" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEKSI 4: Status Kepegawaian & Penugasan -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-black text-lg">
                            4
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Status Kepegawaian & Penugasan</h3>
                            <p class="text-xs text-slate-500 font-medium">Status kepegawaian, NUPTK, SK, TMT, Lembaga Pengangkat, dan Sumber Gaji</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status Kepegawaian -->
                        <div>
                            <label for="status_kepegawaian" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status Kepegawaian</label>
                            <select id="status_kepegawaian" name="status_kepegawaian"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                                <option value="">-- Pilih Status Kepegawaian --</option>
                                @foreach($statusKepegawaianOptions as $status)
                                    <option value="{{ $status }}" {{ old('status_kepegawaian') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status_kepegawaian')" class="mt-1 text-xs" />
                        </div>

                        <!-- NIY / NIGK -->
                        <div>
                            <label for="niy_nigk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NIY / NIGK</label>
                            <input id="niy_nigk" type="text" name="niy_nigk" value="{{ old('niy_nigk') }}" placeholder="Nomor Induk Yayasan / Guru Kristen"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('niy_nigk')" class="mt-1 text-xs" />
                        </div>

                        <!-- NUPTK -->
                        <div>
                            <label for="nuptk" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NUPTK</label>
                            <input id="nuptk" type="text" name="nuptk" value="{{ old('nuptk') }}" placeholder="16 digit NUPTK"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('nuptk')" class="mt-1 text-xs" />
                        </div>

                        <!-- SK Pengangkatan -->
                        <div>
                            <label for="sk_pengangkatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">SK Pengangkatan</label>
                            <input id="sk_pengangkatan" type="text" name="sk_pengangkatan" value="{{ old('sk_pengangkatan') }}" placeholder="Nomor SK Pengangkatan"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('sk_pengangkatan')" class="mt-1 text-xs" />
                        </div>

                        <!-- TMT Pengangkatan -->
                        <div>
                            <label for="tmt_pengangkatan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">TMT Pengangkatan</label>
                            <input id="tmt_pengangkatan" type="date" name="tmt_pengangkatan" value="{{ old('tmt_pengangkatan') }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('tmt_pengangkatan')" class="mt-1 text-xs" />
                        </div>

                        <!-- Lembaga Pengangkat -->
                        <div>
                            <label for="lembaga_pengangkat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Lembaga Pengangkat</label>
                            <input id="lembaga_pengangkat" type="text" name="lembaga_pengangkat" value="{{ old('lembaga_pengangkat') }}" placeholder="Contoh: Yayasan / Kepala Sekolah / Pemerintah Daerah"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('lembaga_pengangkat')" class="mt-1 text-xs" />
                        </div>

                        <!-- Sumber Gaji -->
                        <div>
                            <label for="sumber_gaji" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sumber Gaji</label>
                            <input id="sumber_gaji" type="text" name="sumber_gaji" value="{{ old('sumber_gaji') }}" placeholder="Contoh: Yayasan / BOS / APBD"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('sumber_gaji')" class="mt-1 text-xs" />
                        </div>

                        <!-- Keahlian Laboratorium -->
                        <div>
                            <label for="keahlian_laboratorium" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keahlian Laboratorium</label>
                            <input id="keahlian_laboratorium" type="text" name="keahlian_laboratorium" value="{{ old('keahlian_laboratorium') }}" placeholder="Contoh: Komputer, Bahasa, IPA, dsb"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('keahlian_laboratorium')" class="mt-1 text-xs" />
                        </div>

                        <!-- Mampu Menangani Kebutuhan Khusus -->
                        <div>
                            <label for="mampu_menangani_kebutuhan_khusus" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Mampu Menangani Kebutuhan Khusus</label>
                            <select id="mampu_menangani_kebutuhan_khusus" name="mampu_menangani_kebutuhan_khusus"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                                <option value="">-- Pilih --</option>
                                <option value="Tidak" {{ old('mampu_menangani_kebutuhan_khusus') == 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                <option value="Ya" {{ old('mampu_menangani_kebutuhan_khusus') == 'Ya' ? 'selected' : '' }}>Ya</option>
                            </select>
                            <x-input-error :messages="$errors->get('mampu_menangani_kebutuhan_khusus')" class="mt-1 text-xs" />
                        </div>

                        <!-- Alasan Keluar Kerja -->
                        <div>
                            <label for="alasan_keluar_kerja" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alasan Keluar Kerja (Jika Nonaktif)</label>
                            <input id="alasan_keluar_kerja" type="text" name="alasan_keluar_kerja" value="{{ old('alasan_keluar_kerja') }}" placeholder="Kosongkan jika masih aktif bekerja"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <x-input-error :messages="$errors->get('alasan_keluar_kerja')" class="mt-1 text-xs" />
                        </div>
                    </div>
                </div>

                <!-- SEKSI 5: Riwayat Sertifikasi & Prestasi -->
                <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-slate-100">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-black text-lg">
                            5
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Riwayat Sertifikasi & Prestasi Guru</h3>
                            <p class="text-xs text-slate-500 font-medium">Informasi sertifikasi pendidik, nomor registrasi guru (NRG), dan nomor peserta prestasi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jenis Sertifikasi -->
                        <div>
                            <label for="jenis_sertifikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Sertifikasi</label>
                            <input id="jenis_sertifikasi" type="text" name="jenis_sertifikasi" value="{{ old('jenis_sertifikasi') }}" placeholder="Contoh: Sertifikasi Pendidik Guru Kelas SD"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <x-input-error :messages="$errors->get('jenis_sertifikasi')" class="mt-1 text-xs" />
                        </div>

                        <!-- Nomor Sertifikasi -->
                        <div>
                            <label for="nomor_sertifikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Sertifikasi</label>
                            <input id="nomor_sertifikasi" type="text" name="nomor_sertifikasi" value="{{ old('nomor_sertifikasi') }}" placeholder="Nomor sertifikat pendidik"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <x-input-error :messages="$errors->get('nomor_sertifikasi')" class="mt-1 text-xs" />
                        </div>

                        <!-- Tahun Sertifikasi -->
                        <div>
                            <label for="tahun_sertifikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun Sertifikasi</label>
                            <input id="tahun_sertifikasi" type="text" name="tahun_sertifikasi" value="{{ old('tahun_sertifikasi') }}" placeholder="Contoh: 2023"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <x-input-error :messages="$errors->get('tahun_sertifikasi')" class="mt-1 text-xs" />
                        </div>

                        <!-- Bidang Studi Sertifikasi -->
                        <div>
                            <label for="bidang_studi_sertifikasi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bidang Studi Sertifikasi</label>
                            <input id="bidang_studi_sertifikasi" type="text" name="bidang_studi_sertifikasi" value="{{ old('bidang_studi_sertifikasi') }}" placeholder="Contoh: Pendidikan Guru Sekolah Dasar (PGSD)"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <x-input-error :messages="$errors->get('bidang_studi_sertifikasi')" class="mt-1 text-xs" />
                        </div>

                        <!-- NRG -->
                        <div>
                            <label for="nrg" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">NRG (Nomor Registrasi Guru)</label>
                            <input id="nrg" type="text" name="nrg" value="{{ old('nrg') }}" placeholder="Nomor Registrasi Guru"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <x-input-error :messages="$errors->get('nrg')" class="mt-1 text-xs" />
                        </div>

                        <!-- Nomor Peserta (Prestasi) -->
                        <div>
                            <label for="nomor_peserta" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nomor Peserta (Prestasi Guru)</label>
                            <input id="nomor_peserta" type="text" name="nomor_peserta" value="{{ old('nomor_peserta') }}" placeholder="Nomor peserta lomba / prestasi guru"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <x-input-error :messages="$errors->get('nomor_peserta')" class="mt-1 text-xs" />
                        </div>
                    </div>
                </div>

                <!-- Submit Action Footer -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
                    <p class="text-xs text-slate-500 font-medium">
                        Pastikan seluruh data yang dimasukkan telah sesuai dengan dokumen resmi guru.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('teachers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-xl shadow-md transition cursor-pointer">
                            Simpan Data Guru
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
