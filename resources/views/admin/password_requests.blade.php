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
