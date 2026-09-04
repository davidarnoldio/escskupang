@php
    $user = Auth::user();
    $pendingPassRequests = $user?->isAdmin() ? \App\Models\PasswordResetRequest::where('status', 'pending')->count() : 0;
    $pendingLettersCount = \App\Models\Attendance::whereNotNull('surat_izin')->where('status', 'izin')->count();
@endphp

<!-- Sidebar Desktop (lg:flex) -->
<aside class="hidden lg:flex w-64 fixed inset-y-0 left-0 bg-[#0b1329] text-slate-300 z-40 flex-col justify-between border-r border-slate-800/80 shadow-2xl">
    
    <!-- Top Header: Logo & Brand -->
    <div class="p-6">
        <div class="flex items-center gap-3.5 mb-8">
            <div class="w-10 h-10 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center p-1.5 shadow-lg shadow-blue-500/10">
                <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="w-full h-full object-contain" onerror="this.src='{{ asset('logo.png') }}'">
            </div>
            <div>
                <h1 class="text-xs font-black text-white tracking-tight uppercase">Portal NTO National Plus</h1>
                <p class="text-[9px] font-extrabold text-blue-400 tracking-wider uppercase">PRIMARY SCHOOL</p>
            </div>
        </div>

        <!-- Vertical Navigation Links -->
        <nav class="space-y-1.5">
            @if($user && $user->isParent())
                <!-- Parent Portal Link -->
                <a href="{{ route('parent.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('parent.dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Portal Presensi Anak</span>
                </a>

                <!-- Parent Payments Link -->
                <a href="{{ route('parent.payments') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('parent.payments') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Tagihan Pembayaran</span>
                </a>

                <!-- Parent Homeworks Link -->
                <a href="{{ route('parent.homeworks') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('parent.homeworks') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Tugas Sekolah (PR)</span>
                </a>
            @else
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>

                <!-- Data Siswa Link -->
                <a href="{{ route('students.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('students.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Data Siswa</span>
                </a>

                <!-- Scan QR Code Link -->
                <a href="{{ route('qr.scan') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('qr.scan') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <span>Scan QR Code</span>
                </a>

                <!-- Presensi Harian Link -->
                <a href="{{ route('attendances.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('attendances.index') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>Presensi Harian</span>
                </a>

                <!-- Rekap Presensi Link -->
                <a href="{{ route('attendances.rekap') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('attendances.rekap') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Rekap Presensi</span>
                </a>

                <!-- Surat Izin Ortu Link -->
                <a href="{{ route('attendances.letters') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('attendances.letters') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span class="flex-1">Surat Izin Ortu</span>
                    @if($pendingLettersCount > 0)
                        <span class="px-1.5 py-0.5 text-[9px] font-black bg-blue-500 text-white rounded-full">
                            {{ $pendingLettersCount }}
                        </span>
                    @endif
                </a>

                <!-- Homework / PR Link (Guru & Admin) -->
                <a href="{{ route('homeworks.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('homeworks.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span>Pekerjaan Rumah (PR)</span>
                </a>

                @if($user && $user->isAdmin())
                    <!-- Pembayaran SPP Link (Admin Only) -->
                    <a href="{{ route('payments.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('payments.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span>Pembayaran SPP</span>
                    </a>

                    <!-- Reset Password Link (Admin Only) -->
                    <a href="{{ route('admin.password-requests.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('admin.password-requests.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        <span class="flex-1">Reset Password</span>
                        @if($pendingPassRequests > 0)
                            <span class="px-1.5 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full animate-pulse">
                                {{ $pendingPassRequests }}
                            </span>
                        @endif
                    </a>

                    <!-- Kelola Guru Link (Admin Only) -->
                    <a href="{{ route('teachers.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('teachers.*') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Kelola Guru</span>
                    </a>

                    <!-- Pengaturan Jam Link (Admin Only) -->
                    <a href="{{ route('settings.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('settings.index') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Pengaturan Jam</span>
                    </a>
                @endif
            @endif
        </nav>
    </div>

    <!-- Bottom User Profile Card & Switch Account Dropdown -->
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/40" x-data="{ openUserMenu: false }">
        
        <!-- User Profile Card Button -->
        <div class="relative">
            <button @click="openUserMenu = !openUserMenu" type="button" class="w-full flex items-center justify-between p-2.5 rounded-2xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 text-left transition duration-200 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-black text-xs shadow-md shrink-0">
                        {{ strtoupper(substr($user->name ?? 'User', 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate max-w-[110px]">{{ $user->name ?? 'User' }}</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span class="truncate">Online</span>
                        </div>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openUserMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <!-- Dropdown Menu Placement between Profile and Logout -->
            <div x-show="openUserMenu" @click.away="openUserMenu = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-14 left-0 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 text-slate-800" style="display: none;">
                
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition">
                    👤 Profile Saya
                </a>

                <!-- Switch Account Section (Placed between Profile and Log Out) -->
                @if(session()->has('impersonated_by'))
                    <div class="border-t border-slate-100 my-1"></div>
                    <form method="POST" action="{{ route('impersonate.leave') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 transition flex items-center gap-2">
                            <span>⏪ Kembali ke Akun Admin</span>
                        </button>
                    </form>
                @elseif($user && $user->isAdmin())
                    <div class="border-t border-slate-100 my-1"></div>
                    <div class="px-4 py-1.5 text-[10px] font-black text-blue-600 uppercase tracking-wider bg-slate-50">
                        🔄 Switch Akun Guru (Wali Kelas)
                    </div>
                    <div class="max-h-48 overflow-y-auto divide-y divide-slate-100">
                        @php
                            $allTeachersNav = \App\Models\User::whereIn('role', ['guru', 'wali_kelas'])->orderBy('name')->get();
                        @endphp
                        @foreach($allTeachersNav as $tNav)
                            <form method="POST" action="{{ route('impersonate.switch', $tNav) }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-1.5 text-xs hover:bg-blue-50 transition flex items-center justify-between text-slate-700 hover:text-blue-900 font-medium">
                                    <span class="truncate font-semibold text-[11px]">{{ $tNav->name }}</span>
                                    <span class="px-1 py-0.5 text-[9px] font-black rounded bg-slate-100 text-slate-600">
                                        {{ $tNav->getAssignedClass() ?? 'Guru' }}
                                    </span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                @endif

                <div class="border-t border-slate-100 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50 transition">
                        🚪 Log Out
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-3 text-center text-[10px] text-slate-500 font-medium">
            &copy; {{ date('Y') }} NTO National Plus Primary School
        </div>
    </div>
</aside>

<!-- Mobile Navigation Header (< lg) -->
<div class="lg:hidden bg-[#0b1329] text-white px-4 py-3 border-b border-slate-800 flex items-center justify-between sticky top-0 z-50 shadow-md" x-data="{ mobileOpen: false }">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="h-8 w-auto" onerror="this.src='{{ asset('logo.png') }}'">
        <span class="text-xs font-black tracking-wider uppercase">NTO NATIONAL PLUS</span>
    </div>

    <button @click="mobileOpen = !mobileOpen" type="button" class="p-2 text-slate-400 hover:text-white focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </button>

    <!-- Mobile Slideout Menu -->
    <div x-show="mobileOpen" @click.away="mobileOpen = false" class="fixed inset-x-0 top-14 bg-[#0b1329] border-b border-slate-800 p-4 space-y-2 text-xs shadow-2xl z-50" style="display: none;">
        @if($user && $user->isParent())
            <a href="{{ route('parent.dashboard') }}" class="block px-4 py-2 rounded-xl bg-blue-600 text-white font-bold">Portal Presensi Anak</a>
            <a href="{{ route('parent.payments') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Tagihan Pembayaran</a>
            <a href="{{ route('parent.homeworks') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Tugas Sekolah (PR)</a>
        @else
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Dashboard</a>
            <a href="{{ route('students.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Data Siswa</a>
            <a href="{{ route('qr.scan') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Scan QR Code</a>
            <a href="{{ route('attendances.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Presensi Harian</a>
            <a href="{{ route('attendances.rekap') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Rekap Presensi</a>
            <a href="{{ route('attendances.letters') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Surat Izin Ortu</a>
            <a href="{{ route('homeworks.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Pekerjaan Rumah (PR)</a>
            @if($user && $user->isAdmin())
                <a href="{{ route('payments.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Pembayaran SPP</a>
                <a href="{{ route('admin.password-requests.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Reset Password ({{ $pendingPassRequests }})</a>
                <a href="{{ route('teachers.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Kelola Guru</a>
                <a href="{{ route('settings.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Pengaturan Jam</a>
            @endif
        @endif
        <div class="border-t border-slate-800 pt-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-rose-400 font-bold">Log Out</button>
            </form>
        </div>
    </div>
</div>
