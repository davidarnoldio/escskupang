<x-guest-layout>
    <div class="mb-6 text-center">
        <!-- Logo / Icon -->
        <div class="inline-flex items-center justify-center mb-3 transform hover:scale-105 transition-all duration-300">
            <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus" class="h-20 w-auto object-contain drop-shadow-xl" onerror="this.src='{{ asset('logo.png') }}'">
        </div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Lupa Kata Sandi Akun</h2>
        <p class="text-xs font-semibold text-emerald-600 mt-1">NTO National Plus Primary School</p>
    </div>

    <div class="mb-5 p-4 bg-emerald-50/80 border border-emerald-100 rounded-2xl text-xs text-emerald-900 leading-relaxed font-medium">
        💡 <strong>Permintaan Reset Sandi:</strong> Masukkan alamat email akun Anda (Guru / Orang Tua) lalu klik tombol di bawah ini. Notifikasi akan **segera dikirimkan langsung ke Administrator Sekolah** agar kata sandi Anda dapat di-reset dan langsung digunakan kembali.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email Akun Anda</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@nto-kupang.sch.id atau nama@student.sch.id"
                       class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition duration-200">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs" />
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-950/20 border border-emerald-600 hover:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transform active:scale-[0.99] transition duration-200 text-xs flex items-center justify-center gap-2 cursor-pointer uppercase tracking-wider">
                <span>📢 Kirim Notifikasi Lupa Password ke Admin</span>
            </button>
        </div>

        <div class="pt-3 text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition">
                ← Kembali ke Halaman Login
            </a>
        </div>
    </form>
</x-guest-layout>
