<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-50 via-white to-slate-100">
        
        <!-- Back Link Navigation -->
        <div class="w-full max-w-md mb-3 flex items-center justify-between text-xs">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-bold text-slate-500 hover:text-red-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Halaman Login</span>
            </a>
            <span class="text-slate-400 font-medium">Bantuan Akun</span>
        </div>

        <!-- Main Card Container -->
        <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-100/80 p-6 sm:p-8 transition-all">
            
            <!-- Header Brand & Logo -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center mb-3 transform hover:scale-105 transition-all duration-300">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus" class="h-20 w-auto object-contain drop-shadow-md" onerror="this.src='{{ asset('logo.png') }}'">
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Lupa Kata Sandi Akun</h2>
                <div class="inline-block mt-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-full text-[11px] font-extrabold uppercase tracking-wider border border-red-100">
                    NTO National Plus Primary School
                </div>
            </div>

            <!-- Petunjuk Informasi -->
            <div class="mb-5 p-4 bg-red-50/70 border border-red-100 rounded-2xl text-xs text-red-900 leading-relaxed font-medium flex items-start gap-3">
                <span class="text-base shrink-0">💡</span>
                <p>
                    <strong>Permintaan Reset Sandi:</strong> Masukkan alamat email akun Anda (Guru / Orang Tua). Permintaan akan dikirimkan langsung ke <strong>Administrator Sekolah</strong> agar kata sandi dapat segera di-reset dan langsung digunakan kembali.
                </p>
            </div>

            <!-- Session Status Alert -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Email Address Input -->
                <div>
                    <label for="email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email Akun Anda</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Masukkan alamat email terdaftar"
                               class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white font-medium transition duration-200">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs font-bold text-red-600" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-extrabold rounded-2xl shadow-lg shadow-red-600/30 border border-red-600 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform active:scale-[0.99] transition duration-200 text-xs flex items-center justify-center gap-2 cursor-pointer uppercase tracking-wider">
                        <span>📢 Kirim Notifikasi Lupa Password ke Admin</span>
                    </button>
                </div>

                <div class="pt-3 text-center">
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-red-600 transition inline-flex items-center gap-1">
                        <span>← Kembali ke Halaman Login</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer Copyright -->
        <p class="mt-6 text-center text-[11px] text-slate-400 font-medium">
            &copy; {{ date('Y') }} NTO National Plus Primary School &bull; Excellent Spirit Christian School
        </p>

    </div>
</x-guest-layout>
