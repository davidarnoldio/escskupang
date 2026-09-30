<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    Tambah Siswa Baru
                </h2>
                <p class="text-xs text-slate-500 mt-1">Formulir pendaftaran biodata lengkap siswa khusus Administrator
                </p>
            </div>
            <a href="{{ route('students.index') }}"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                ← Kembali ke Data Siswa
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- CARD 1: IDENTITAS POKOK SISWA -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div
                            class="w-9 h-9 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg">
                            🪪
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Identitas & Administrasi Siswa</h3>
                            <p class="text-xs text-slate-500">Nomor induk, nomor kependudukan, nama, dan kelas siswa</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- NISN -->
                        <div>
                            <label for="nisn"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                NISN (Nomor Induk Siswa Nasional) <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nisn" name="nisn" value="{{ old('nisn') }}" required
                                placeholder="Contoh: 0012345678"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('nisn')" class="mt-1" />
                        </div>

                        <!-- NIK Siswa -->
                        <div>
                            <label for="nik"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                NIK Siswa (16 Digit)
                            </label>
                            <input type="text" id="nik" name="nik" value="{{ old('nik') }}" maxlength="20"
                                placeholder="Contoh: 3201012345670001"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition font-mono">
                            <x-input-error :messages="$errors->get('nik')" class="mt-1" />
                        </div>

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="nama"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nama Lengkap Siswa <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                                placeholder="Contoh: Ahmad Subagja"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                        </div>

                        <!-- No KK -->
                        <div>
                            <label for="no_kk"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                No. Kartu Keluarga (KK)
                            </label>
                            <input type="text" id="no_kk" name="no_kk" value="{{ old('no_kk') }}" maxlength="20"
                                placeholder="Contoh: 3201012345670002"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition font-mono">
                            <x-input-error :messages="$errors->get('no_kk')" class="mt-1" />
                        </div>

                        <!-- Tingkat / Kelas -->
                        <div>
                            <label for="kelas"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tingkat / Nama Kelas <span class="text-rose-500">*</span>
                            </label>
                            <x-class-combobox name="kelas" id="kelas" :value="old('kelas')"
                                :officialClasses="\App\Models\Student::getAllClasses()" :required="true" />
                            <p class="text-[11px] text-slate-400 mt-1 font-medium">Bisa pilih opsi atau ketik manual
                                (misal: 6A, 5C, TK-A).</p>
                            <x-input-error :messages="$errors->get('kelas')" class="mt-1" />
                        </div>

                        <!-- Jenis Kelamin Radio Button -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-6 pt-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', 'L') == 'L' ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                                    <span class="ms-2 text-sm text-slate-700 font-semibold">Laki-laki (L)</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                                    <span class="ms-2 text-sm text-slate-700 font-semibold">Perempuan (P)</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1" />
                        </div>

                        <!-- Foto Profil Siswa -->
                        <div class="sm:col-span-2">
                            <label for="foto"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Foto Profil Siswa (Opsional)
                            </label>
                            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 5 MB.</p>
                            <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Kategori Siswa (ABK / Regular) -->
                    <div class="mt-6 p-4 bg-red-50/60 border border-red-200/80 rounded-2xl">
                        <label class="block text-xs font-bold text-red-900 uppercase tracking-wider mb-2">Kategori
                            Kebutuhan Siswa</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="is_abk" value="0" {{ old('is_abk', '0') == '0' ? 'checked' : '' }} class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                                <span class="ms-2 text-sm text-slate-800 font-semibold">Siswa Regular (Umum)</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="is_abk" value="1" {{ old('is_abk') == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                                <span class="ms-2 text-sm text-red-950 font-bold flex items-center gap-1">
                                    <span>♿ Siswa Berkebutuhan Khusus (ABK)</span>
                                </span>
                            </label>
                        </div>
                        <p class="text-[11px] text-red-700 mt-2">Siswa ABK memiliki aturan toleransi jam masuk &
                            keterlambatan tersendiri pada presensi QR.</p>
                    </div>
                </div>

                <!-- CARD 2: PRESTASI SISWA -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div
                            class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg">
                            🏆
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Bidang Prestasi Siswa</h3>
                            <p class="text-xs text-slate-500">Pilih kategori prestasi yang diraih oleh siswa</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                            Kategori Prestasi
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                            @php
                                $prestasiOptions = [
                                    'Tidak Ada' => '⚪ Tidak Ada',
                                    'Akademik' => '📚 Akademik',
                                    'Seni' => '🎨 Seni',
                                    'Olahraga' => '⚽ Olahraga',
                                    'Lain-lain' => '✨ Lain-lain',
                                ];
                                $selectedPrestasi = old('kategori_prestasi', 'Tidak Ada');
                            @endphp
                            @foreach($prestasiOptions as $val => $label)
                                <label
                                    class="flex items-center p-3 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-100/80 cursor-pointer transition has-checked:border-red-500 has-checked:bg-red-50/60 has-checked:text-red-950">
                                    <input type="radio" name="kategori_prestasi" value="{{ $val }}" {{ $selectedPrestasi == $val ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                                    <span class="ms-2 text-xs font-bold text-slate-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('kategori_prestasi')" class="mt-1" />

                        <div class="pt-2">
                            <label for="keterangan_prestasi"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Keterangan / Detail Prestasi (Opsional)
                            </label>
                            <input type="text" id="keterangan_prestasi" name="keterangan_prestasi"
                                value="{{ old('keterangan_prestasi') }}"
                                placeholder="Contoh: Juara 1 Olimpiade Matematika Tingkat Kota Kupang 2025"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('keterangan_prestasi')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- CARD 3: TEMPAT, TANGGAL LAHIR, AGAMA & DOMISILI -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div
                            class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-lg">
                            📍
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Kelahiran, Agama & Domisili</h3>
                            <p class="text-xs text-slate-500">Tempat lahir, dokumen kelahiran, keyakinan, dan alamat
                                tinggal</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Tempat Lahir -->
                        <div>
                            <label for="tempat_lahir"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tempat Lahir
                            </label>
                            <input type="text" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                placeholder="Contoh: Kupang"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('tempat_lahir')" class="mt-1" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label for="tanggal_lahir"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tanggal Lahir
                            </label>
                            <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ old('tanggal_lahir') }}"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('tanggal_lahir')" class="mt-1" />
                        </div>

                        <!-- No Akta Kelahiran -->
                        <div>
                            <label for="no_akta_kelahiran"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                No. Akta Kelahiran
                            </label>
                            <input type="text" id="no_akta_kelahiran" name="no_akta_kelahiran"
                                value="{{ old('no_akta_kelahiran') }}" placeholder="Contoh: 5371-LT-12345678"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('no_akta_kelahiran')" class="mt-1" />
                        </div>

                        <!-- Agama -->
                        <div>
                            <label for="agama"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Agama
                            </label>
                            <select id="agama" name="agama"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                                <option value="">-- Pilih Agama --</option>
                                @foreach(['Kristen Protestan', 'Katolik', 'Islam', 'Hindu', 'Buddha', 'Khonghucu'] as $agm)
                                    <option value="{{ $agm }}" {{ old('agama') == $agm ? 'selected' : '' }}>{{ $agm }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('agama')" class="mt-1" />
                        </div>

                        <!-- Kewarganegaraan Radio Button -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Kewarganegaraan
                            </label>
                            <div class="flex items-center gap-6 pt-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="kewarganegaraan" value="WNI" {{ old('kewarganegaraan', 'WNI') == 'WNI' ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                                    <span class="ms-2 text-sm text-slate-700 font-semibold">WNI (Warga Negara
                                        Indonesia)</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="kewarganegaraan" value="WNA" {{ old('kewarganegaraan') == 'WNA' ? 'checked' : '' }}
                                        class="w-4 h-4 text-red-600 border-slate-300 focus:ring-red-500">
                                    <span class="ms-2 text-sm text-slate-700 font-semibold">WNA (Asing)</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('kewarganegaraan')" class="mt-1" />
                        </div>

                        <!-- Telepon Orang Tua -->
                        <div>
                            <label for="telepon"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Nomor Telepon / HP Orang Tua
                            </label>
                            <input type="text" id="telepon" name="telepon" value="{{ old('telepon') }}"
                                placeholder="Contoh: 081234567890"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('telepon')" class="mt-1" />
                        </div>

                        <!-- Alamat Lengkap -->
                        <div class="sm:col-span-2">
                            <label for="alamat"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Alamat Lengkap Tempat Tinggal
                            </label>
                            <textarea id="alamat" name="alamat" rows="3"
                                placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota..."
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">{{ old('alamat') }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- CARD 4: DATA FISIK SISWA (ANTROPOMETRI) -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
                    <div class="flex items-center gap-3 pb-4 mb-6 border-b border-slate-100">
                        <div
                            class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">
                            📏
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Data Fisik & Keluarga Siswa</h3>
                            <p class="text-xs text-slate-500">Ukuran fisik periodik siswa dan jumlah saudara kandung</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                        <!-- Tinggi Badan -->
                        <div>
                            <label for="tinggi_badan"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tinggi Badan (cm)
                            </label>
                            <input type="number" id="tinggi_badan" name="tinggi_badan" value="{{ old('tinggi_badan') }}"
                                min="20" max="250" placeholder="Contoh: 120"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('tinggi_badan')" class="mt-1" />
                        </div>

                        <!-- Berat Badan -->
                        <div>
                            <label for="berat_badan"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Berat Badan (kg)
                            </label>
                            <input type="number" id="berat_badan" name="berat_badan" value="{{ old('berat_badan') }}"
                                min="3" max="200" placeholder="Contoh: 25"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('berat_badan')" class="mt-1" />
                        </div>

                        <!-- Lingkar Kepala -->
                        <div>
                            <label for="lingkar_kepala"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Lingkar Kepala (cm)
                            </label>
                            <input type="number" id="lingkar_kepala" name="lingkar_kepala"
                                value="{{ old('lingkar_kepala') }}" min="10" max="100" placeholder="Contoh: 50"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('lingkar_kepala')" class="mt-1" />
                        </div>

                        <!-- Jumlah Saudara Kandung -->
                        <div>
                            <label for="jumlah_saudara_kandung"
                                class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Jml Saudara Kandung
                            </label>
                            <input type="number" id="jumlah_saudara_kandung" name="jumlah_saudara_kandung"
                                value="{{ old('jumlah_saudara_kandung', 0) }}" min="0" max="30" placeholder="0"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                            <x-input-error :messages="$errors->get('jumlah_saudara_kandung')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- CARD 5: DATA ORANG TUA KANDUNG -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8 space-y-8">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div
                            class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                            👨‍👩‍👧
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">Data Orang Tua Kandung</h3>
                            <p class="text-xs text-slate-500">Identitas ayah dan ibu kandung siswa (Kerahasiaan data
                                terjaga)</p>
                        </div>
                    </div>

                    @php
                        $pendidikanList = ['SD / Sederajat', 'SMP / Sederajat', 'SMA / SMK / Sederajat', 'D1 / D2 / D3', 'S1 / D4', 'S2', 'S3', 'Tidak Sekolah'];
                        $penghasilanList = [
                            'Kurang dari Rp 1.000.000',
                            'Rp 1.000.000 - Rp 2.000.000',
                            'Rp 2.000.000 - Rp 5.000.000',
                            'Lebih dari Rp 5.000.000',
                            'Tidak Berpenghasilan',
                        ];
                    @endphp

                    <!-- SUB: DATA AYAH -->
                    <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                        <h4 class="font-bold text-sm text-slate-800 flex items-center gap-2 mb-4">
                            <span>👨</span> Data Ayah Kandung
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_ayah"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama
                                    Ayah</label>
                                <input type="text" id="nama_ayah" name="nama_ayah" value="{{ old('nama_ayah') }}"
                                    placeholder="Nama lengkap ayah"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition">
                                <x-input-error :messages="$errors->get('nama_ayah')" class="mt-1" />
                            </div>
                            <div>
                                <label for="nik_ayah"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIK
                                    Ayah (16 Digit)</label>
                                <input type="text" id="nik_ayah" name="nik_ayah" value="{{ old('nik_ayah') }}"
                                    maxlength="20" placeholder="320101..."
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition font-mono">
                                <x-input-error :messages="$errors->get('nik_ayah')" class="mt-1" />
                            </div>
                            <div>
                                <label for="tahun_lahir_ayah"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tahun
                                    Lahir Ayah</label>
                                <input type="text" id="tahun_lahir_ayah" name="tahun_lahir_ayah"
                                    value="{{ old('tahun_lahir_ayah') }}" maxlength="4" placeholder="Contoh: 1980"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition font-mono">
                                <x-input-error :messages="$errors->get('tahun_lahir_ayah')" class="mt-1" />
                            </div>
                            <div>
                                <label for="pendidikan_ayah"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pendidikan
                                    Terakhir Ayah</label>
                                <select id="pendidikan_ayah" name="pendidikan_ayah"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach($pendidikanList as $pnd)
                                        <option value="{{ $pnd }}" {{ old('pendidikan_ayah') == $pnd ? 'selected' : '' }}>
                                            {{ $pnd }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pendidikan_ayah')" class="mt-1" />
                            </div>
                            <div class="sm:col-span-2">
                                <label for="penghasilan_ayah"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penghasilan
                                    Bulanan Ayah</label>
                                <select id="penghasilan_ayah" name="penghasilan_ayah"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition">
                                    <option value="">-- Pilih Penghasilan --</option>
                                    @foreach($penghasilanList as $pgh)
                                        <option value="{{ $pgh }}" {{ old('penghasilan_ayah') == $pgh ? 'selected' : '' }}>
                                            {{ $pgh }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('penghasilan_ayah')" class="mt-1" />
                            </div>
                        </div>
                    </div>

                    <!-- SUB: DATA IBU -->
                    <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/70">
                        <h4 class="font-bold text-sm text-slate-800 flex items-center gap-2 mb-4">
                            <span>👩</span> Data Ibu Kandung
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nama_ibu"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama
                                    Ibu</label>
                                <input type="text" id="nama_ibu" name="nama_ibu" value="{{ old('nama_ibu') }}"
                                    placeholder="Nama lengkap ibu"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition">
                                <x-input-error :messages="$errors->get('nama_ibu')" class="mt-1" />
                            </div>
                            <div>
                                <label for="nik_ibu"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIK Ibu
                                    (16 Digit)</label>
                                <input type="text" id="nik_ibu" name="nik_ibu" value="{{ old('nik_ibu') }}"
                                    maxlength="20" placeholder="320101..."
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition font-mono">
                                <x-input-error :messages="$errors->get('nik_ibu')" class="mt-1" />
                            </div>
                            <div>
                                <label for="tahun_lahir_ibu"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tahun
                                    Lahir Ibu</label>
                                <input type="text" id="tahun_lahir_ibu" name="tahun_lahir_ibu"
                                    value="{{ old('tahun_lahir_ibu') }}" maxlength="4" placeholder="Contoh: 1985"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition font-mono">
                                <x-input-error :messages="$errors->get('tahun_lahir_ibu')" class="mt-1" />
                            </div>
                            <div>
                                <label for="pendidikan_ibu"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pendidikan
                                    Terakhir Ibu</label>
                                <select id="pendidikan_ibu" name="pendidikan_ibu"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition">
                                    <option value="">-- Pilih Pendidikan --</option>
                                    @foreach($pendidikanList as $pnd)
                                        <option value="{{ $pnd }}" {{ old('pendidikan_ibu') == $pnd ? 'selected' : '' }}>
                                            {{ $pnd }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('pendidikan_ibu')" class="mt-1" />
                            </div>
                            <div class="sm:col-span-2">
                                <label for="penghasilan_ibu"
                                    class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Penghasilan
                                    Bulanan Ibu</label>
                                <select id="penghasilan_ibu" name="penghasilan_ibu"
                                    class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 transition">
                                    <option value="">-- Pilih Penghasilan --</option>
                                    @foreach($penghasilanList as $pgh)
                                        <option value="{{ $pgh }}" {{ old('penghasilan_ibu') == $pgh ? 'selected' : '' }}>
                                            {{ $pgh }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('penghasilan_ibu')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div
                    class="p-6 bg-white rounded-2xl shadow-xs border border-slate-200/80 flex items-center justify-between">
                    <a href="{{ route('students.index') }}"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-sm rounded-xl shadow-md transition cursor-pointer flex items-center gap-2">
                        <span>💾</span>
                        <span>Simpan Data Siswa Lengkap</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>