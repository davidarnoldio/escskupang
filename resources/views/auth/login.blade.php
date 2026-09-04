<x-guest-layout>
    <div class="min-h-screen grid lg:grid-cols-12 bg-[#fee6a8]">
        
        <!-- PANEL KIRI: ~45% Width (lg:col-span-5) Dark Forest Green Hero Panel -->
        <div class="lg:col-span-5 relative bg-[#025a3f] p-8 lg:p-10 flex flex-col justify-between overflow-hidden text-white min-h-[520px] lg:min-h-screen shadow-2xl z-10">
            
            <!-- Subtle Watermark Tree Graphic on Lower Left -->
            <div class="absolute -bottom-16 -left-16 w-80 h-80 opacity-15 pointer-events-none z-0">
                <svg viewBox="0 0 200 200" fill="none" class="w-full h-full text-emerald-300">
                    <circle cx="100" cy="100" r="80" stroke="currentColor" stroke-width="4"/>
                    <path d="M100 40 Q120 70 100 120 M100 70 Q70 90 60 110 M100 90 Q130 100 140 120" stroke="currentColor" stroke-width="6" stroke-linecap="round"/>
                </svg>
            </div>

            <!-- Subtle Matrix Dot Grid Pattern on Upper Right -->
            <div class="absolute top-10 right-10 opacity-20 pointer-events-none z-0">
                <div class="grid grid-cols-6 gap-2">
                    <template x-for="i in 24">
                        <div class="w-1.5 h-1.5 bg-emerald-200 rounded-full"></div>
                    </template>
                </div>
            </div>

            <!-- Top Left Pill Badge -->
            <div class="relative z-10 flex items-center">
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-emerald-400/40 bg-emerald-950/40 text-emerald-100 text-[11px] font-semibold backdrop-blur-md shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>NTO National Plus Primary School</span>
                </span>
            </div>

            <!-- Central Hero Content Group -->
            <div class="relative z-10 my-auto py-6 flex flex-col items-center text-center">
                
                <!-- School Logo Emblem Box Card -->
                <div class="w-full max-w-[260px] bg-white rounded-3xl p-6 shadow-2xl border border-white/60 mb-6 text-center transform hover:scale-105 transition duration-300">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus"
                         class="h-28 w-auto mx-auto object-contain drop-shadow-sm mb-3"
                         onerror="this.src='{{ asset('logo.png') }}'">
                    
                    <h2 class="text-sm font-black text-slate-900 tracking-wide uppercase leading-tight">
                        NTO NATIONAL PLUS
                    </h2>
                    <p class="text-[10px] font-black text-emerald-600 tracking-wider uppercase mt-0.5">
                        PRIMARY SCHOOL
                    </p>
                    
                    <div class="w-10 h-0.5 bg-emerald-500 mx-auto my-2 rounded-full"></div>
                    
                    <p class="text-[9px] font-medium text-slate-500 italic leading-snug">
                        "Nurtured in God • Observed in Humanity • Taught in Knowledge"
                    </p>
                </div>

                <!-- Main Portal Title -->
                <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    Portal Presensi Sekolah
                </h1>
                
                <!-- Subtitle Description -->
                <p class="text-xs text-emerald-100/90 font-medium leading-relaxed max-w-xs mt-3">
                    Sistem informasi presensi & akademik terpadu untuk siswa, guru, dan orang tua.
                </p>

            </div>

            <!-- Bottom Location Badge Pill -->
            <div class="relative z-10 flex justify-center lg:justify-start">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-emerald-400/40 bg-emerald-950/40 text-emerald-100 text-[11px] font-medium backdrop-blur-md shadow-xs">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Kupang, Nusa Tenggara Timur - Indonesia</span>
                </span>
            </div>

        </div>

        <!-- PANEL KANAN: ~55% Width (lg:col-span-7) Soft Cream Page Background -->
        <div class="lg:col-span-7 flex flex-col justify-between items-center p-6 lg:p-12 relative min-h-screen z-10 bg-[#fee6a8]">
            
            <!-- Login Card Container -->
            <div class="w-full my-auto flex flex-col items-center">
                <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 p-8 sm:p-10 transition-all duration-300">
                    
                    <!-- Top Circular Shield Icon Badge -->
                    <div class="w-14 h-14 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center mx-auto mb-5 text-emerald-600 shadow-xs">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>

                    <!-- Card Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang Kembali!</h2>
                        <p class="text-xs font-medium text-slate-500 mt-1.5">
                            Silakan masuk untuk melanjutkan ke portal presensi.
                        </p>
                        <div class="w-12 h-1 bg-emerald-500 rounded-full mx-auto mt-3"></div>
                    </div>

                    <!-- Session Status Message -->
                    <x-auth-session-status class="mb-4 text-xs" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                                       autocomplete="username" placeholder="admin@nto-kupang.sch.id"
                                       class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition duration-200">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
                        </div>

                        <!-- Password -->
                        <div x-data="{ showPassword: false }">
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block text-xs font-bold text-slate-700">Kata Sandi</label>
                                @if (Route::has('password.request'))
                                    <a class="text-xs font-bold text-emerald-600 hover:text-emerald-700 transition" href="{{ route('password.request') }}">
                                        Lupa kata sandi?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                       autocomplete="current-password" placeholder="••••••••"
                                       class="w-full pl-11 pr-12 py-3 bg-white border border-slate-200 rounded-xl text-slate-900 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition duration-200">

                                <!-- Password Eye Toggle Button -->
                                <button type="button" @click="showPassword = !showPassword" id="togglePasswordBtn"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-emerald-600 focus:outline-none transition cursor-pointer"
                                        title="Tampilkan / Sembunyikan Kata Sandi">
                                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.02 10.02 0 013.122-.563c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.43 3.978m-3.83-3.83a3 3 0 00-4.243-4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs text-rose-600 font-semibold" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" name="remember"
                                       class="rounded border-slate-300 text-emerald-600 shadow-xs focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                <span class="ms-2.5 text-xs font-semibold text-slate-700">Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Primary Action Button -->
                        <div class="pt-2">
                            <button type="submit"
                                    class="w-full py-3.5 px-6 bg-[#059669] hover:bg-[#047857] text-white font-bold rounded-xl shadow-md shadow-emerald-700/20 text-sm flex items-center justify-center gap-2 cursor-pointer transition duration-200 transform active:scale-[0.99]">
                                <span>Masuk Sekarang</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Security Guarantee Notice -->
                    <div class="mt-6 pt-5 border-t border-slate-100 text-center flex items-center justify-center gap-2 text-xs font-medium text-slate-400">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Keamanan data kami terjamin dan terenkripsi.</span>
                    </div>

                </div>
            </div>

            <!-- Footer Copyright Notice -->
            <div class="text-center text-xs text-slate-500 font-medium pb-2">
                &copy; {{ date('Y') }} <strong class="text-emerald-700 font-bold">NTO National Plus Primary School</strong>. Hak Cipta Dilindungi.
            </div>

        </div>

    </div>

    <!-- Eye Toggle Native Fallback JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passInput = document.getElementById('password');
            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', function (e) {
                    if (passInput.type === 'password') {
                        passInput.type = 'text';
                    } else {
                        passInput.type = 'password';
                    }
                });
            }
        });
    </script>
</x-guest-layout>