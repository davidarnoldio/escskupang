<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-900 leading-tight flex items-center gap-2">
            <span>📋</span> Buku Bimbingan & Observasi Siswa
        </h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto px-4 text-center">
        <div class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xs space-y-4">
            <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-3xl mx-auto">
                🏫
            </div>
            <h3 class="font-extrabold text-base text-slate-900">Akun Anda Belum Ditugaskan Sebagai Wali Kelas</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed">
                Fitur Buku Bimbingan & Observasi Siswa ini dikhususkan untuk Wali Kelas yang mengampu kelas tertentu. Silakan hubungi Administrator sekolah untuk menetapkan kelas binaan Anda pada menu Kelola Guru.
            </p>
            <div class="pt-2">
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition">
                    Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
