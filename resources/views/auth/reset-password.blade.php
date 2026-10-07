<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-50 via-white to-slate-100">
        
        <!-- Back Link Navigation -->
        <div class="w-full max-w-md mb-3 flex items-center justify-between text-xs">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 font-bold text-slate-500 hover:text-red-600 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Halaman Login</span>
            </a>
            <span class="text-slate-400 font-medium">Reset Password</span>
        </div>

        <!-- Main Card Container -->
        <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-100/80 p-6 sm:p-8 transition-all">
            
            <!-- Header Brand & Logo -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center mb-3 transform hover:scale-105 transition-all duration-300">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus" class="h-20 w-auto object-contain drop-shadow-md" onerror="this.src='{{ asset('logo.png') }}'">
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Atur Ulang Kata Sandi</h2>
                <div class="inline-block mt-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-full text-[11px] font-extrabold uppercase tracking-wider border border-red-100">
                    NTO National Plus Primary School
                </div>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                               class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white font-medium transition duration-200">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs font-bold text-red-600" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Masukkan kata sandi baru"
                               class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white font-medium transition duration-200">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs font-bold text-red-600" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi kata sandi baru"
                               class="w-full pl-10 pr-3.5 py-3 bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white font-medium transition duration-200">
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs font-bold text-red-600" />
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-extrabold rounded-2xl shadow-lg shadow-red-600/30 border border-red-600 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform active:scale-[0.99] transition duration-200 text-xs flex items-center justify-center gap-2 cursor-pointer uppercase tracking-wider">
                        <span>🔐 Simpan Kata Sandi Baru</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Copyright -->
        <p class="mt-6 text-center text-[11px] text-slate-400 font-medium">
            &copy; {{ date('Y') }} NTO National Plus Primary School &bull; Excellent Spirit Christian School
        </p>

    </div>
</x-guest-layout>
