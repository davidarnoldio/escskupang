<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Notifikasi & Permintaan Reset Password</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-0.5">
                    Kelola laporan Lupa Password dari Guru dan Orang Tua Siswa NTO National Plus
                </p>
            </div>
            <div>
                <a href="#ganti-password-admin" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl shadow-sm transition flex items-center gap-2 cursor-pointer">
                    <span>🔑 Ganti Password Saya</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        
        <!-- Flash Message Alert -->
        @if (session('success'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-900 text-xs font-extrabold rounded-2xl shadow-2xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Change Own Admin Password Card -->
        <div id="ganti-password-admin" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden" x-data="{ showPass: false, showNewPass: false }">
            <div class="px-6 py-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-red-600 flex items-center justify-center font-bold text-sm shadow-md">
                        🔑
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-white">Ganti Kata Sandi Akun Administrator</h3>
                        <p class="text-[11px] text-slate-300">Ubah password akun admin yang sedang aktif ({{ Auth::user()->email }})</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-white/10 rounded-full text-[10px] font-bold text-red-300 border border-white/10">
                    Akun: {{ Auth::user()->name }}
                </span>
            </div>

            <form method="POST" action="{{ route('admin.change-password') }}" class="p-6 space-y-4">
                @csrf
                
                @if ($errors->any())
                    <div class="p-3 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold rounded-2xl">
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Password Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" id="current_password" name="current_password" required
                                   placeholder="Masukkan password lama"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-red-500 focus:bg-white transition pr-10">
                            <button type="button" @click="showPass = !showPass" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs" tabindex="-1">
                                <span x-show="!showPass">👁️</span>
                                <span x-show="showPass">🙈</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showNewPass ? 'text' : 'password'" id="new_password" name="new_password" required minlength="6"
                                   placeholder="Minimal 6 karakter"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-red-500 focus:bg-white transition pr-10">
                            <button type="button" @click="showNewPass = !showNewPass" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs" tabindex="-1">
                                <span x-show="!showNewPass">👁️</span>
                                <span x-show="showNewPass">🙈</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="new_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Ulangi Password Baru <span class="text-rose-500">*</span>
                        </label>
                        <input :type="showNewPass ? 'text' : 'password'" id="new_password_confirmation" name="new_password_confirmation" required minlength="6"
                               placeholder="Ketik ulang password baru"
                               class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-semibold focus:ring-2 focus:ring-red-500 focus:bg-white transition">
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-extrabold text-xs rounded-2xl shadow-md shadow-red-600/30 transition flex items-center gap-2 cursor-pointer">
                        <span>💾 Simpan Kata Sandi Baru</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Pending Password Reset Requests Container -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-amber-500/10 border-b border-amber-200/60 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                    <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">
                        Permintaan Lupa Password Menunggu Reset ({{ $pendingRequests->count() }})
                    </h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase font-black tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Waktu Laporan</th>
                            <th class="px-6 py-3.5">Nama Pengguna</th>
                            <th class="px-6 py-3.5">Email Akun</th>
                            <th class="px-6 py-3.5">Peran (Role)</th>
                            <th class="px-6 py-3.5 text-right">Aksi Reset Instan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pendingRequests as $req)
                            <tr class="hover:bg-amber-50/30 transition duration-150">
                                <td class="px-6 py-4 text-xs font-bold text-slate-500">
                                    {{ $req->created_at ? $req->created_at->diffForHumans() : '-' }}
                                </td>
                                <td class="px-6 py-4 font-extrabold text-slate-900">
                                    {{ $req->name ?? ($req->user ? $req->user->name : 'Pengguna') }}
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">
                                    {{ $req->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-extrabold rounded-full {{ $req->role == 'guru' ? 'bg-teal-50 text-red-800 border border-teal-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ strtoupper($req->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('admin.reset-password', $req->user_id) }}" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input type="text" name="new_password" placeholder="Password Baru..." required class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold w-36 focus:ring-2 focus:ring-red-600 focus:bg-white transition">
                                        <button type="submit" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-xl text-xs shadow-md shadow-red-600/30 transition cursor-pointer">
                                            🔑 Reset Sekarang
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                    Tidak ada permintaan reset password yang menunggu konfirmasi Admin.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- History of Resolved Requests -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider">
                    Riwayat Permintaan Reset Password Selesai ({{ $resolvedRequests->count() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase font-black tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Email Akun</th>
                            <th class="px-6 py-3.5">Peran</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5">Waktu Selesai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($resolvedRequests as $res)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-mono font-bold text-xs text-slate-600">
                                    {{ $res->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-block px-3 py-1 text-xs font-extrabold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ strtoupper($res->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-800 font-extrabold text-xs rounded-full border border-red-200">
                                        ✓ Selesai Dirubah
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-500">
                                    {{ $res->updated_at ? $res->updated_at->isoFormat('D MMMM Y - HH:mm') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-xs font-semibold text-slate-400 italic">
                                    Belum ada riwayat reset password.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
