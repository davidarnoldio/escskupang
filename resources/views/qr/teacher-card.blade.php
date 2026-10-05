<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Presensi Guru QR - {{ $teacher->name }} | NTO National Plus</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 flex flex-col items-center justify-center min-h-screen p-4 font-sans text-slate-800">

    <div class="no-print mb-6 flex gap-3 justify-center">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('teachers.index') }}"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-sm transition">
                ← Kembali ke Kelola Guru
            </a>
        @else
            <a href="{{ route('dashboard') }}"
                class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-sm transition">
                ← Kembali ke Dashboard
            </a>
        @endif
        <button onclick="window.print()"
            class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-red-400 font-bold rounded-xl text-sm shadow-md transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            Cetak Kartu Guru
        </button>
    </div>

    <!-- Official Teacher ID Card Component - NTO National Plus -->
    <div class="w-[360px] bg-white rounded-2xl shadow-2xl border border-red-500/20 overflow-hidden relative">
        <!-- Header Gradient -->
        <div
            class="bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 p-4 text-white text-center relative overflow-hidden border-b border-red-500/30 flex items-center justify-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="h-12 w-auto object-contain" onerror="this.src='{{ asset('logo.png') }}'">
            <div class="text-left">
                <p class="text-[9px] font-extrabold uppercase tracking-widest text-red-400">KARTU PRESENSI GURU / STAFF
                </p>
                <h3 class="font-extrabold text-base tracking-tight text-white leading-tight">NTO NATIONAL PLUS</h3>
                <p class="text-[9px] text-slate-300 font-medium">Excellent Spirit Christian School</p>
            </div>
        </div>

        <div class="p-6 text-center space-y-4">
            <!-- Teacher Avatar Badge -->
            <div class="relative inline-block">
                <div
                    class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-slate-900 via-slate-800 to-red-900 text-red-400 border-2 border-red-400/50 flex items-center justify-center font-extrabold text-2xl shadow-md mx-auto">
                    {{ strtoupper(substr($teacher->name, 0, 2)) }}
                </div>
            </div>

            <!-- Teacher Info -->
            <div>
                <h4 class="font-bold text-lg text-slate-900 leading-tight">{{ $teacher->name }}</h4>
                <p class="text-xs font-mono text-red-600 font-bold mt-0.5">{{ $teacher->email }}</p>
                <span
                    class="inline-block mt-1 px-3 py-0.5 bg-red-50 text-red-700 border border-red-200 rounded-full text-xs font-bold">
                    {{ $teacher->getAssignedClass() ? 'Wali Kelas ' . $teacher->getAssignedClass() : 'Guru Pengajar' }}
                </span>
            </div>

            <!-- QR Code Section -->
            @php
                $qrCodeData = $teacher->qr_code_value; // e.g. GURU-1
            @endphp
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 inline-block shadow-2xs">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrCodeData) }}"
                    onerror="this.onerror=null; this.src='https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=' + encodeURIComponent('{{ $qrCodeData }}');"
                    alt="QR Code Guru {{ $teacher->name }}" class="w-32 h-32 mx-auto rounded-lg">
                <p class="text-[10px] font-mono text-slate-500 mt-1 font-bold">{{ $qrCodeData }}</p>
            </div>
        </div>

        <!-- Footer Bar -->
        <div class="bg-slate-900 border-t border-slate-800 px-4 py-2.5 text-center text-[10px] text-slate-300">
            Arahkan QR Code ini ke kamera saat Presensi Masuk & Pulang.
        </div>
    </div>

</body>

</html>