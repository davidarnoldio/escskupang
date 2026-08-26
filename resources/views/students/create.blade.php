<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                Tambah Siswa Baru ESCS Kupang
            </h2>
            <a href="{{ route('students.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                Kembali ke Data Siswa
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-6 sm:p-8">
                <form method="POST" action="{{ route('students.store') }}" class="space-y-6">
                    @csrf

                    <!-- NIS -->
                    <div>
                        <label for="nis" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">NIS (Nomor Induk Siswa) <span class="text-rose-500">*</span></label>
                        <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required placeholder="Contoh: 10201" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:bg-white transition">
                        <x-input-error :messages="$errors->get('nis')" class="mt-1" />
                    </div>

                    <!-- Nama Siswa -->
                    <div>
                        <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Ahmad Subagja" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:bg-white transition">
                        <x-input-error :messages="$errors->get('nama')" class="mt-1" />
                    </div>

                    <!-- Kelas & Jenis Kelamin Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="kelas" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tingkat Kelas <span class="text-rose-500">*</span></label>
                            <select id="kelas" name="kelas" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-sky-500 focus:bg-white transition cursor-pointer">
                                <option value="" disabled {{ old('kelas') ? '' : 'selected' }}>-- Pilih Tingkat Kelas --</option>
                                @foreach(\App\Models\Student::OFFICIAL_CLASSES as $cls)
                                    <option value="{{ $cls }}" {{ old('kelas') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kelas')" class="mt-1" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <div class="flex items-center gap-6 pt-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', 'L') == 'L' ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 focus:ring-sky-500">
                                    <span class="ms-2 text-sm text-slate-700 font-semibold">Laki-laki (L)</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} class="w-4 h-4 text-sky-600 border-slate-300 focus:ring-sky-500">
                                    <span class="ms-2 text-sm text-slate-700 font-semibold">Perempuan (P)</span>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('jenis_kelamin')" class="mt-1" />
                        </div>
                    </div>

                    <!-- Kategori Siswa (ABK / Regular) -->
                    <div class="p-4 bg-indigo-50/60 border border-indigo-200/80 rounded-2xl">
                        <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wider mb-2">Kategori Kebutuhan Siswa</label>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="is_abk" value="0" {{ old('is_abk', '0') == '0' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                <span class="ms-2 text-sm text-slate-800 font-semibold">Siswa Regular (Umum)</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="is_abk" value="1" {{ old('is_abk') == '1' ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500">
                                <span class="ms-2 text-sm text-indigo-950 font-bold flex items-center gap-1">
                                    <span>♿ Siswa Berkebutuhan Khusus (ABK)</span>
                                </span>
                            </label>
                        </div>
                        <p class="text-[11px] text-indigo-700 mt-2">Siswa Berkebutuhan Khusus (ABK) memiliki aturan jam kehadiran & toleransi keterlambatan tersendiri pada mesin presensi QR.</p>
                    </div>

                    <!-- Telepon -->
                    <div>
                        <label for="telepon" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nomor Telepon / HP Ortu</label>
                        <input type="text" id="telepon" name="telepon" value="{{ old('telepon') }}" placeholder="Contoh: 081234567890" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:bg-white transition">
                        <x-input-error :messages="$errors->get('telepon')" class="mt-1" />
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label for="alamat" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Tempat Tinggal</label>
                        <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap siswa..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:bg-white transition">{{ old('alamat') }}</textarea>
                        <x-input-error :messages="$errors->get('alamat')" class="mt-1" />
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                        <a href="{{ route('students.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-sky-400 font-bold text-sm rounded-xl shadow-md transition cursor-pointer">
                            Simpan Data Siswa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
