@php
    $user = Auth::user();

    // Admin Counts
    $pendingPassRequests = $user?->isAdmin() ? \App\Models\PasswordResetRequest::where('status', 'pending')->count() : 0;
    $pendingPaymentsCount = $user?->isAdmin() ? \App\Models\Payment::where('status', 'menunggu_konfirmasi')->count() : 0;

    // Guru & Admin Homework Submissions Needing Grading
    $pendingHomeworkSubmissionsCount = 0;
    if ($user && ($user->isTeacher() || $user->isAdmin())) {
        $assignedClass = $user->getAssignedClass();
        $hwSubQuery = \App\Models\HomeworkSubmission::whereNull('nilai');
        if ($user->isTeacher() && $assignedClass) {
            $hwSubQuery->whereHas('homework', fn($q) => $q->where('kelas', $assignedClass));
        }
        $pendingHomeworkSubmissionsCount = $hwSubQuery->count();
    }

    // Guru & Admin Attendance Letters
    $pendingLettersCount = 0;
    if ($user && ($user->isTeacher() || $user->isAdmin())) {
        $assignedClass = $user->getAssignedClass();
        $letterQuery = \App\Models\Attendance::whereNotNull('surat_izin')->where('status', 'izin');
        if ($user->isTeacher() && $assignedClass) {
            $letterQuery->whereHas('student', fn($q) => $q->where('kelas', $assignedClass));
        }
        $pendingLettersCount = $letterQuery->count();
    }

    // Parent Portal Badges
    $parentUnpaidCount = 0;
    $parentPendingPRCount = 0;
    if ($user && $user->isParent() && $user->student_id) {
        $parentUnpaidCount = \App\Models\Payment::where('student_id', $user->student_id)
            ->whereIn('status', ['belum_lunas', 'ditolak'])
            ->count();

        $studentClass = $user->student?->kelas;
        if ($studentClass) {
            $parentPendingPRCount = \App\Models\Homework::where('kelas', $studentClass)
                ->where(function($q) use ($user) {
                    $q->whereNull('student_id')->orWhere('student_id', $user->student_id);
                })
                ->whereDoesntHave('submissions', fn($q) => $q->where('student_id', $user->student_id))
                ->count();
        }
    }
@endphp

<!-- Sidebar Desktop (lg:flex) -->
<aside class="hidden lg:flex w-64 fixed inset-y-0 left-0 bg-[#7f1d1d] text-slate-100 z-40 flex-col justify-between border-r border-red-900/60 shadow-2xl overflow-hidden">
    
    <!-- Background Tree Logo Watermark Shadow (Monochromatic Mint Matching Gambar 1) -->
    <div class="absolute -bottom-16 -right-16 w-72 h-72 opacity-15 pointer-events-none z-0 select-none">
        <svg viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full text-red-400">
            <path d="M 30 350 Q 200 310 370 350" stroke="currentColor" stroke-width="8" opacity="0.5"/>
            <path d="M 190 330 C 190 260 170 210 155 170 M 210 330 C 210 260 230 210 245 170" stroke="currentColor" stroke-width="14"/>
            <path d="M 200 330 L 200 140" stroke="currentColor" stroke-width="16"/>
            <path d="M 200 240 C 150 210 100 200 50 210" stroke="currentColor" stroke-width="8"/>
            <path d="M 200 240 C 250 210 300 200 350 210" stroke="currentColor" stroke-width="8"/>
            <circle cx="50" cy="210" r="25" fill="currentColor" opacity="0.35"/>
            <circle cx="100" cy="190" r="32" fill="currentColor" opacity="0.4"/>
            <circle cx="150" cy="160" r="38" fill="currentColor" opacity="0.45"/>
            <circle cx="250" cy="160" r="38" fill="currentColor" opacity="0.45"/>
            <circle cx="300" cy="190" r="32" fill="currentColor" opacity="0.4"/>
            <circle cx="350" cy="210" r="25" fill="currentColor" opacity="0.35"/>
            <circle cx="200" cy="55" r="42" fill="currentColor" opacity="0.5"/>
        </svg>
    </div>
    
    <!-- Top Header: Logo & Brand -->
    <div class="p-6 relative z-10">
        <div class="flex items-center gap-3.5 mb-8">
            <div class="w-10 h-10 rounded-xl bg-red-600/20 border border-red-500/30 flex items-center justify-center p-1.5 shadow-lg shadow-red-500/10">
                <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="w-full h-full object-contain" onerror="this.src='{{ asset('logo.png') }}'">
            </div>
            <div>
                <h1 class="text-xs font-black text-white tracking-tight uppercase">Portal NTO National Plus</h1>
                <p class="text-[9px] font-extrabold text-red-400 tracking-wider uppercase">PRIMARY SCHOOL</p>
            </div>
        </div>

        <!-- Vertical Navigation Links -->
        <nav class="space-y-1.5">
            @if($user && $user->isParent())
                <!-- Parent Portal Link -->
                <a href="{{ route('parent.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('parent.dashboard') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Portal Presensi Anak</span>
                </a>

                <!-- Parent Payments Link -->
                <a href="{{ route('parent.payments') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('parent.payments') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="flex-1">Tagihan Pembayaran</span>
                    @if($parentUnpaidCount > 0)
                        <span class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full animate-pulse shadow-sm shadow-rose-500/50">
                            {{ $parentUnpaidCount }}
                        </span>
                    @endif
                </a>

                <!-- Parent Homeworks Link -->
                <a href="{{ route('parent.homeworks') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('parent.homeworks') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span class="flex-1">Tugas Sekolah (PR)</span>
                    @if($parentPendingPRCount > 0)
                        <span class="px-2 py-0.5 text-[9px] font-black bg-amber-500 text-white rounded-full animate-pulse shadow-sm shadow-amber-500/50">
                            {{ $parentPendingPRCount }}
                        </span>
                    @endif
                </a>
            @else
                <!-- Dashboard Link -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span>Dashboard</span>
                </a>

                <!-- Data Siswa Link -->
                <a href="{{ route('students.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('students.*') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Data Siswa</span>
                </a>

                <!-- Scan QR Code Link -->
                <a href="{{ route('qr.scan') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('qr.scan') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <span>Scan QR Code</span>
                </a>

                <!-- Presensi Harian Link -->
                <a href="{{ route('attendances.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('attendances.index') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    <span>Presensi Harian</span>
                </a>

                <!-- Rekap Presensi Link -->
                <a href="{{ route('attendances.rekap') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('attendances.rekap') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Rekap Presensi</span>
                </a>

                <!-- Surat Izin Ortu Link -->
                <a href="{{ route('attendances.letters') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('attendances.letters') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span class="flex-1">Surat Izin Ortu</span>
                    @if($pendingLettersCount > 0)
                        <span class="px-2 py-0.5 text-[9px] font-black bg-red-600 text-white rounded-full animate-pulse shadow-sm shadow-red-500/50">
                            {{ $pendingLettersCount }}
                        </span>
                    @endif
                </a>

                <!-- Homework / PR Link (Guru & Admin) -->
                <a href="{{ route('homeworks.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('homeworks.*') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span class="flex-1">Pekerjaan Rumah (PR)</span>
                    @if($pendingHomeworkSubmissionsCount > 0)
                        <span class="px-2 py-0.5 text-[9px] font-black bg-amber-500 text-white rounded-full animate-pulse shadow-sm shadow-amber-500/50" title="{{ $pendingHomeworkSubmissionsCount }} PR perlu dinilai">
                            {{ $pendingHomeworkSubmissionsCount }}
                        </span>
                    @endif
                </a>

                @if($user && $user->isAdmin())
                    <!-- Pembayaran SPP Link (Admin Only) -->
                    <a href="{{ route('payments.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('payments.*') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="flex-1">Pembayaran SPP</span>
                        @if($pendingPaymentsCount > 0)
                            <span class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full animate-pulse shadow-sm shadow-rose-500/50" title="{{ $pendingPaymentsCount }} bukti pembayaran perlu konfirmasi">
                                {{ $pendingPaymentsCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Reset Password Link (Admin Only) -->
                    <a href="{{ route('admin.password-requests.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('admin.password-requests.*') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        <span class="flex-1">Reset Password</span>
                        @if($pendingPassRequests > 0)
                            <span class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full animate-pulse shadow-sm shadow-rose-500/50">
                                {{ $pendingPassRequests }}
                            </span>
                        @endif
                    </a>

                    <!-- Kelola Guru Link (Admin Only) -->
                    <a href="{{ route('teachers.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('teachers.*') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        <span>Kelola Guru</span>
                    </a>

                    <!-- Pengaturan Jam Link (Admin Only) -->
                    <a href="{{ route('settings.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition duration-200 {{ request()->routeIs('settings.index') ? 'bg-gradient-to-r from-red-600 to-red-800 text-white shadow-lg shadow-red-600/30 font-extrabold' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Pengaturan Jam</span>
                    </a>
                @endif
            @endif
        </nav>
    </div>

    <!-- Bottom User Profile Card & Switch Account Dropdown -->
    <div class="p-4 border-t border-slate-800/80 bg-slate-950/40 relative z-10" x-data="{ openUserMenu: false }">
        
        <!-- User Profile Card Button -->
        <div class="relative">
            <button @click="openUserMenu = !openUserMenu" type="button" class="w-full flex items-center justify-between p-2.5 rounded-2xl bg-slate-900/80 hover:bg-slate-800 border border-slate-800 text-left transition duration-200 cursor-pointer">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-red-600 to-red-800 text-white flex items-center justify-center font-black text-xs shadow-md shrink-0">
                        {{ strtoupper(substr($user->name ?? 'User', 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-white truncate max-w-[110px]">{{ $user->name ?? 'User' }}</div>
                        <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                            <span class="truncate">Online</span>
                        </div>
                    </div>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openUserMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>

            <!-- Dropdown Menu Placement between Profile and Logout -->
            <div x-show="openUserMenu" @click.away="openUserMenu = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute bottom-14 left-0 w-full bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 text-slate-800" style="display: none;">
                
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-bold text-slate-700 hover:bg-red-50 hover:text-red-600 transition">
                    👤 Profile Saya
                </a>

                <!-- Switch Account Section (Placed between Profile and Log Out) -->
                @if(session()->has('impersonated_by'))
                    <div class="border-t border-slate-100 my-1"></div>
                    <form method="POST" action="{{ route('impersonate.leave') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 transition flex items-center gap-2">
                            <span>⏪ Kembali ke Akun Admin</span>
                        </button>
                    </form>
                @elseif($user && $user->isAdmin())
                    <div class="border-t border-slate-100 my-1"></div>
                    <div class="px-4 py-1.5 text-[10px] font-black text-red-600 uppercase tracking-wider bg-slate-50">
                        🔄 Switch Akun Guru (Wali Kelas)
                    </div>
                    <div class="max-h-48 overflow-y-auto divide-y divide-slate-100">
                        @php
                            $allTeachersNav = \App\Models\User::whereIn('role', ['guru', 'wali_kelas'])->orderBy('name')->get();
                        @endphp
                        @foreach($allTeachersNav as $tNav)
                            <form method="POST" action="{{ route('impersonate.switch', $tNav) }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-1.5 text-xs hover:bg-red-50 transition flex items-center justify-between text-slate-700 hover:text-red-900 font-medium">
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
<div class="lg:hidden bg-[#7f1d1d] text-white px-4 py-3 border-b border-red-900 flex items-center justify-between sticky top-0 z-50 shadow-md" x-data="{ mobileOpen: false }">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="h-8 w-auto" onerror="this.src='{{ asset('logo.png') }}'">
        <span class="text-xs font-black tracking-wider uppercase">NTO NATIONAL PLUS</span>
    </div>

    <button @click="mobileOpen = !mobileOpen" type="button" class="p-2 text-red-200 hover:text-white focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </button>

    <!-- Mobile Slideout Menu -->
    <div x-show="mobileOpen" @click.away="mobileOpen = false" class="fixed inset-x-0 top-14 bg-[#7f1d1d] border-b border-red-900 p-4 space-y-2 text-xs shadow-2xl z-50" style="display: none;">
        @if($user && $user->isParent())
            <a href="{{ route('parent.dashboard') }}" class="block px-4 py-2 rounded-xl bg-red-600 text-white font-bold">Portal Presensi Anak</a>
            <a href="{{ route('parent.payments') }}" class="flex items-center justify-between px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">
                <span>Tagihan Pembayaran</span>
                @if($parentUnpaidCount > 0)
                    <span class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full">
                        {{ $parentUnpaidCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('parent.homeworks') }}" class="flex items-center justify-between px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">
                <span>Tugas Sekolah (PR)</span>
                @if($parentPendingPRCount > 0)
                    <span class="px-2 py-0.5 text-[9px] font-black bg-amber-500 text-white rounded-full">
                        {{ $parentPendingPRCount }}
                    </span>
                @endif
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Dashboard</a>
            <a href="{{ route('students.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Data Siswa</a>
            <a href="{{ route('qr.scan') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Scan QR Code</a>
            <a href="{{ route('attendances.index') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Presensi Harian</a>
            <a href="{{ route('attendances.rekap') }}" class="block px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">Rekap Presensi</a>
            <a href="{{ route('attendances.letters') }}" class="flex items-center justify-between px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">
                <span>Surat Izin Ortu</span>
                @if($pendingLettersCount > 0)
                    <span class="px-2 py-0.5 text-[9px] font-black bg-red-600 text-white rounded-full">
                        {{ $pendingLettersCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('homeworks.index') }}" class="flex items-center justify-between px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">
                <span>Pekerjaan Rumah (PR)</span>
                @if($pendingHomeworkSubmissionsCount > 0)
                    <span class="px-2 py-0.5 text-[9px] font-black bg-amber-500 text-white rounded-full">
                        {{ $pendingHomeworkSubmissionsCount }}
                    </span>
                @endif
            </a>
            @if($user && $user->isAdmin())
                <a href="{{ route('payments.index') }}" class="flex items-center justify-between px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">
                    <span>Pembayaran SPP</span>
                    @if($pendingPaymentsCount > 0)
                        <span class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full">
                            {{ $pendingPaymentsCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.password-requests.index') }}" class="flex items-center justify-between px-4 py-2 rounded-xl text-slate-300 hover:bg-slate-800 font-bold">
                    <span>Reset Password</span>
                    @if($pendingPassRequests > 0)
                        <span class="px-2 py-0.5 text-[9px] font-black bg-rose-500 text-white rounded-full">
                            {{ $pendingPassRequests }}
                        </span>
                    @endif
                </a>
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
