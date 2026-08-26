<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>✏️ Edit Guru / Pindahkan Tingkat Kelas</span>
                </h2>
                <p class="text-xs font-semibold text-sky-600 mt-1">
                    Ubah rincian akun guru atau pindahkan penugasan tingkat kelas binaan
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
                <form method="POST" action="{{ route('teachers.update', $teacher) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Lengkap Guru -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Lengkap Guru</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $teacher->name) }}" required autofocus class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                    </div>

                    <!-- Email / Username Login -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email (Username Login)</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $teacher->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                    </div>

                    <!-- Password Baru (Optional) -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                        <input id="password" type="password" name="password" placeholder="••••••••" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs" />
                    </div>

                    <!-- Tingkat Kelas Binaan (Assigned Class) -->
                    <div>
                        <label for="assigned_class" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tingkat Kelas Binaan (Wali Kelas)</label>
                        @php
                            $currentClass = $teacher->getAssignedClass();
                        @endphp
                        <select id="assigned_class" name="assigned_class" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 font-medium">
                            <option value="">-- Pilih Tingkat Kelas (Opsional) --</option>
                            @foreach($officialClasses as $classOption)
                                <option value="{{ $classOption }}" {{ old('assigned_class', $currentClass) == $classOption ? 'selected' : '' }}>
                                    Kelas {{ $classOption }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 font-medium mt-1">
                            🔄 <strong>Efek Pindah Kelas:</strong> Begitu kelas diubah dan disimpan, akun guru ini akan **secara otomatis dan langsung** menampilkan murid-murid di kelas baru tersebut saat guru login.
                        </p>
                        <x-input-error :messages="$errors->get('assigned_class')" class="mt-1 text-xs" />
                    </div>

                    <!-- Submit Action -->
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <a href="{{ route('teachers.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-slate-900 hover:bg-sky-600 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                            Perbarui Data Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
