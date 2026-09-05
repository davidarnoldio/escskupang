<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span>✏️ Edit Guru / Pindahkan Tingkat Kelas</span>
                </h2>
                <p class="text-xs font-semibold text-red-600 mt-1">
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
                        <input id="name" type="text" name="name" value="{{ old('name', $teacher->name) }}" required autofocus class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <x-input-error :messages="$errors->get('name')" class="mt-1 text-xs" />
                    </div>

                    <!-- Email / Username Login -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Alamat Email (Username Login)</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $teacher->email) }}" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
                    </div>

                    <!-- Password Baru (Optional) -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Kata Sandi Baru (Kosongkan jika tidak diubah)</label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" placeholder="••••••••" class="w-full px-4 py-2.5 pe-11 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 end-0 px-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
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

                    <!-- Tingkat Kelas Binaan (Assigned Class) -->
                    <div>
                        <label for="assigned_class" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tingkat Kelas Binaan (Wali Kelas)</label>
                        @php
                            $currentClass = $teacher->getAssignedClass();
                        @endphp
                        <select id="assigned_class" name="assigned_class" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 font-medium">
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
                        <button type="submit" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                            Perbarui Data Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
