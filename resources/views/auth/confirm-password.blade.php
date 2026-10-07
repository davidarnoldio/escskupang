<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-50 via-white to-slate-100">
        
        <!-- Main Card Container -->
        <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-100/80 p-6 sm:p-8 transition-all">
            
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus" class="h-20 w-auto object-contain drop-shadow-md" onerror="this.src='{{ asset('logo.png') }}'">
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Konfirmasi Kata Sandi</h2>
                <div class="inline-block mt-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-full text-[11px] font-extrabold uppercase tracking-wider border border-red-100">
                    NTO National Plus Primary School
                </div>
            </div>

            <div class="mb-5 p-4 bg-amber-50/70 border border-amber-100 rounded-2xl text-xs text-amber-900 leading-relaxed font-medium">
                🔒 Ini adalah area yang dilindungi. Silakan masukkan kata sandi Anda untuk melanjutkan.
            </div>

            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <input id="password" class="w-full px-3.5 py-3 bg-slate-50 border border-slate-200 text-slate-900 text-xs sm:text-sm rounded-2xl focus:ring-2 focus:ring-red-500 focus:border-red-500 focus:bg-white font-medium transition duration-200"
                           type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi akun" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs font-bold text-red-600" />
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-4 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-extrabold rounded-2xl shadow-lg shadow-red-600/30 border border-red-600 hover:border-red-700 transition duration-200 text-xs flex items-center justify-center gap-2 cursor-pointer uppercase tracking-wider">
                        <span>Konfirmasi</span>
                    </button>
                </div>
            </form>
        </div>

        <p class="mt-6 text-center text-[11px] text-slate-400 font-medium">
            &copy; {{ date('Y') }} NTO National Plus Primary School &bull; Excellent Spirit Christian School
        </p>
    </div>
</x-guest-layout>
