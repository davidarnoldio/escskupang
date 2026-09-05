<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Pelajar QR - {{ $student->nama }} | NTO National Plus Primary School</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-100 flex flex-col items-center justify-center min-h-screen p-4 font-sans text-slate-800">

    <div class="no-print mb-6 flex gap-3">
        <a href="{{ route('students.index') }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-sm transition">
            ← Kembali ke Data Siswa
        </a>
        <button onclick="window.print()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-red-400 font-bold rounded-xl text-sm shadow-md transition cursor-pointer flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Kartu Siswa
        </button>
    </div>

    <!-- Official Student ID Card Component - NTO National Plus Primary School -->
    <div class="w-[360px] bg-white rounded-2xl shadow-2xl border border-red-500/20 overflow-hidden relative">
        <!-- Header Gradient -->
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-red-950 p-4 text-white text-center relative overflow-hidden border-b border-red-500/30 flex items-center justify-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="h-12 w-auto object-contain">
            <div class="text-left">
                <p class="text-[9px] font-extrabold uppercase tracking-widest text-red-400">KARTU TANDA PELAJAR</p>
                <h3 class="font-extrabold text-base tracking-tight text-white leading-tight">NTO NATIONAL PLUS</h3>
                <p class="text-[9px] text-slate-300 font-medium">National Plus Primary School</p>
            </div>
        </div>

        <div class="p-6 text-center space-y-4">
            <!-- Student Avatar Badge -->
            <div class="relative inline-block">
                <div class="w-20 h-20 rounded-2xl bg-slate-900 text-red-300 border-2 border-red-400/40 flex items-center justify-center font-extrabold text-2xl shadow-md mx-auto">
                    {{ strtoupper(substr($student->nama, 0, 2)) }}
                </div>
            </div>

            <!-- Student Info -->
            <div>
                <h4 class="font-bold text-lg text-slate-900 leading-tight">{{ $student->nama }}</h4>
                <p class="text-xs font-mono text-red-600 font-bold mt-0.5">NIS: {{ $student->nis }}</p>
                <span class="inline-block mt-1 px-3 py-0.5 bg-slate-100 text-slate-800 rounded-full text-xs font-semibold">
                    Kelas {{ $student->kelas }} ({{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }})
                </span>
            </div>

            <!-- QR Code Section -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 inline-block shadow-2xs">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($student->nis) }}" 
                     onerror="this.onerror=null; this.src='https://chart.googleapis.com/chart?chs=180x180&cht=qr&chl=' + encodeURIComponent('{{ $student->nis }}');"
                     alt="QR Code NIS {{ $student->nis }}" 
                     class="w-32 h-32 mx-auto rounded-lg">
                <p class="text-[10px] font-mono text-slate-400 mt-1 font-semibold">NIS: {{ $student->nis }}</p>
            </div>
        </div>

        <!-- Footer Bar -->
        <div class="bg-slate-900 border-t border-slate-800 px-4 py-2.5 text-center text-[10px] text-slate-300">
            Tunjukkan QR Code ini ke kamera presensi setiap hari sekolah.
        </div>
    </div>

</body>
</html>
