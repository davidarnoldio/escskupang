<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Kelola Data Guru & Wali Kelas</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                    Manajemen akun guru dan penugasan tingkat kelas binaan (Wali Kelas) NTO National Plus Primary School
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

    <div class="space-y-6">

        <!-- Flash Message Alert -->
        @if (session('success'))
            <div
                class="p-4 bg-red-50 border border-red-200 text-red-900 text-xs font-extrabold rounded-2xl shadow-2xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
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
                        placeholder="Cari nama atau email guru..."
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
                Total Guru: <span class="text-red-600 font-black text-sm">{{ $teachers->count() }}</span> Akun
                Terdaftar
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
                                {{ $class === 'Unassigned' ? 'Guru Tanpa Penugasan Kelas' : 'Wali Kelas: ' . $class }}
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
                                                            {{ $teacher->name }}</h4>
                                                        <p class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $teacher->email }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-4 pt-3 border-t border-slate-200/60 space-y-2">
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="font-semibold text-slate-500">Kelas Binaan:</span>
                                                    <span
                                                        class="font-extrabold text-red-700 bg-red-50 px-2 py-0.5 rounded-full border border-red-200">
                                                        {{ $teacher->assigned_class ?? 'Belum Ditentukan' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between text-[11px]">
                                                    <span class="font-semibold text-slate-500">Kata Sandi:</span>
                                                    <span
                                                        class="font-mono font-bold text-slate-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 text-[10px]">
                                                        {{ $teacher->plain_password ?? (explode('@', $teacher->email)[0] . '123') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons: Switch Akun + Edit + Delete -->
                                        <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between gap-2">
                                            <form method="POST" action="{{ route('impersonate.switch', $teacher) }}"
                                                class="inline-block flex-1">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full py-1.5 px-3 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-xl text-xs transition cursor-pointer shadow-xs flex items-center justify-center gap-1">
                                                    <span>⚡ Switch Akun</span>
                                                </button>
                                            </form>

                                            <div class="flex items-center gap-1">
                                                <a href="{{ route('teachers.edit', $teacher) }}"
                                                    class="p-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl transition"
                                                    title="Edit Data Guru">
                                                    ✏️
                                                </a>
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
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>