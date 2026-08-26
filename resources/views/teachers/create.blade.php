<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>➕ Tambah Akun Guru / Wali Kelas Baru</span>
                </h2>
                <p class="text-xs font-semibold text-sky-600 mt-1">
                    Buat akun login baru dan tentukan tingkat kelas binaan guru
                </p>
            </div>
            <a href="{{ route('teachers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100">
                <form method="POST" action="{{ route('teachers.store') }}" class="space-y-6">
                    @csrf

                    <!-- Nama Lengkap Guru -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap Guru</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: Maria Skolastika, S.Pd" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                    </div>

                    <!-- Email / Username Login -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email (Username Login)</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="nama.guru@escs-kupang.sch.id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi (Password)</label>
                        <input id="password" type="password" name="password" required placeholder="Minimal 6 karakter..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                    </div>

                    <!-- Tingkat Kelas Binaan (Assigned Class) -->
                    <div>
                        <label for="assigned_class" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tingkat Kelas Binaan (Wali Kelas)</label>
                        <select id="assigned_class" name="assigned_class" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 font-medium">
                            <option value="">-- Pilih Tingkat Kelas (Opsional) --</option>
                            @foreach($officialClasses as $classOption)
                                <option value="{{ $classOption }}" {{ old('assigned_class') == $classOption ? 'selected' : '' }}>
                                    Kelas {{ $classOption }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">
                            💡 Guru secara otomatis akan melihat dan mengelola data siswa pada tingkat kelas yang dipilih ini saat login.
                        </p>
                        <x-input-error :messages="$errors->get('assigned_class')" class="mt-1 text-xs" />
                    </div>

                    <!-- Submit Action -->
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <a href="{{ route('teachers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                            Simpan Akun Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
