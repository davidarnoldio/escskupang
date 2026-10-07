<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-8 sm:px-6 lg:px-8 bg-gradient-to-b from-slate-50 via-white to-slate-100">
        
        <!-- Main Card Container -->
        <div class="w-full max-w-md sm:max-w-lg bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-100/80 p-6 sm:p-8 transition-all">
            
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center mb-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo NTO National Plus" class="h-20 w-auto object-contain drop-shadow-md" onerror="this.src='{{ asset('logo.png') }}'">
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Verifikasi Alamat Email</h2>
                <div class="inline-block mt-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-full text-[11px] font-extrabold uppercase tracking-wider border border-red-100">
                    NTO National Plus Primary School
                </div>
            </div>

            <div class="mb-5 p-4 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs text-slate-700 leading-relaxed font-medium">
                {{ __('Terima kasih telah mendaftar! Sebelum mulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan. Jika tidak menerima email, kami dapat mengirimkannya kembali.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold">
                    {{ __('Tautan verifikasi baru telah berhasil dikirim ke alamat email yang Anda daftarkan.') }}
                </div>
            @endif

            <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto py-2.5 px-5 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-extrabold rounded-xl shadow-md shadow-red-600/30 text-xs transition cursor-pointer">
                        {{ __('Kirim Ulang Email Verifikasi') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto text-center">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-slate-500 hover:text-red-600 transition">
                        {{ __('Keluar (Log Out)') }}
                    </button>
                </form>
            </div>
        </div>

        <p class="mt-6 text-center text-[11px] text-slate-400 font-medium">
            &copy; {{ date('Y') }} NTO National Plus Primary School &bull; Excellent Spirit Christian School
        </p>
    </div>
</x-guest-layout>
