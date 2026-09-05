<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NTO National Plus Primary School') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-slate-800 bg-white selection:bg-red-600 selection:text-white min-h-screen">
        
        <!-- Left Sidebar Navigation Component -->
        @include('layouts.navigation')

        <!-- Main Workspace Frame (lg:pl-64) -->
        <div class="lg:pl-64 flex flex-col min-h-screen">
            
            <!-- Sticky Impersonation Banner Alert -->
            @if(session()->has('impersonated_by'))
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold px-4 py-2.5 text-xs text-center border-b border-amber-600 shadow-md flex items-center justify-center gap-3 z-30">
                    <span>⚠️ <strong>Mode Peninjauan Guru:</strong> Anda sedang mengakses sebagai <u>{{ Auth::user()?->name }}</u> (Wali Kelas {{ Auth::user()?->getAssignedClass() ?? 'Guru' }}).</span>
                    <form method="POST" action="{{ route('impersonate.leave') }}" class="inline-block">
                        @csrf
                        <button type="submit" class="px-3 py-1 bg-slate-950 hover:bg-slate-900 text-white rounded-xl text-xs font-extrabold transition cursor-pointer shadow-xs">
                            ⏪ Kembali ke Akun Admin
                        </button>
                    </form>
                </div>
            @endif

            <!-- Top Greeting Bar Header matching Reference Image 2 -->
            <header class="px-6 lg:px-8 py-5 flex items-center justify-between bg-white border-b border-slate-100">
                <div>
                    @isset($header)
                        {{ $header }}
                    @else
                        <h2 class="text-xl lg:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>Selamat Datang, {{ Auth::user()?->name ?? 'User' }}!</span>
                            <span>👋</span>
                        </h2>
                        <p class="text-xs font-semibold text-slate-500 mt-0.5">
                            Berikut ringkasan presensi hari ini di NTO National Plus.
                        </p>
                    @endisset
                </div>

                <!-- Right Action Badges: Date Pill & Notification Bell -->
                <div class="flex items-center gap-3">
                    <!-- Dynamic Date Badge -->
                    <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-2xl shadow-xs border border-slate-200/80 text-xs font-bold text-slate-700">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>

                    <!-- Notification Bell Icon Button -->
                    @php
                        $pendingNotifCount = Auth::user()?->isAdmin() ? \App\Models\PasswordResetRequest::where('status', 'pending')->count() : 0;
                    @endphp
                    <a href="{{ Auth::user()?->isAdmin() ? route('admin.password-requests.index') : route('attendances.letters') }}"
                       class="relative w-10 h-10 rounded-2xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600 hover:text-red-600 transition shadow-xs cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if($pendingNotifCount > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-600 text-white font-black text-[10px] rounded-full flex items-center justify-center shadow-xs animate-pulse">
                                {{ $pendingNotifCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </header>

            <!-- Main Page Content Canvas -->
            <main class="flex-1 px-6 lg:px-8 pb-8">
                {{ $slot }}
            </main>
        </div>

    </body>
</html>
