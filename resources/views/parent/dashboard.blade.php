<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Portal Orang Tua Siswa</span>
                </h2>
                <p class="text-xs text-slate-500 mt-1">Pemantauan Presensi & Kartu Digital Siswa NTO National Plus</p>
            </div>
            @if($student)
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-sky-50 text-sky-900 font-bold text-xs rounded-xl border border-sky-200">
                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                    <span>Anak: {{ $student->nama }} (Kelas {{ $student->kelas }})</span>
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alert Messages -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm flex items-center gap-2 shadow-xs">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm space-y-1 shadow-xs">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-2 font-medium">
                            <span>⚠️</span> <span>{{ $error }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            @if(!$student)
                <div class="bg-white p-8 rounded-3xl shadow-xs text-center border border-slate-200">
                    <p class="text-slate-500 font-bold">Akun Orang Tua ini belum terhubung dengan data siswa.</p>
                </div>
            @else

                <!-- Top Row: Student Profile Card & Upload Photo & Today Status & Digital QR -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <!-- Student Profile Card with Cropper Upload -->
                    <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 text-white p-6 rounded-3xl shadow-xl border border-sky-500/30 relative overflow-hidden flex flex-col justify-between">
                        <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-sky-500/10 rounded-full blur-md"></div>
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-sky-400">PROFIL ANAK</span>
                            
                            <!-- Student Photo / Avatar -->
                            <div class="mt-3 mb-4 flex items-center gap-4">
                                <div class="relative group">
                                    @if($student->foto)
                                        <img src="{{ asset($student->foto) }}" alt="{{ $student->nama }}" class="w-20 h-20 rounded-2xl object-cover border-2 border-sky-400/50 shadow-md">
                                    @else
                                        <div class="w-20 h-20 rounded-2xl bg-slate-800 text-sky-400 font-extrabold text-2xl flex items-center justify-center border-2 border-sky-400/40 shadow-md">
                                            {{ strtoupper(substr($student->nama, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-white leading-tight">{{ $student->nama }}</h3>
                                    <p class="text-xs font-mono text-sky-300 font-bold mt-0.5">NIS: {{ $student->nis }}</p>
                                    <span class="inline-block mt-1 px-2.5 py-0.5 bg-white/10 text-white rounded-full text-[11px] font-semibold border border-white/10">
                                        Kelas {{ $student->kelas }} ({{ $student->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }})
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Attendance Card -->
                    <div class="bg-white p-6 rounded-3xl shadow-xs border border-slate-200/80 flex flex-col justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Presensi Hari Ini</p>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5">{{ now()->isoFormat('D MMMM YYYY') }}</p>

                            <div class="mt-4">
                                @if(!$todayAttendance)
                                    <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                                        ⏳ Belum Presensi
                                    </span>
                                    <p class="text-xs text-slate-400 mt-2">Siswa belum memindai QR Code hari ini.</p>
                                @else
                                    @php
                                        $statusClasses = [
                                            'hadir' => 'bg-emerald-500 text-white shadow-emerald-500/20',
                                            'izin' => 'bg-sky-500 text-white shadow-sky-500/20',
                                            'sakit' => 'bg-blue-600 text-white shadow-blue-600/20',
                                            'alpa' => 'bg-rose-500 text-white shadow-rose-500/20',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-4 py-2 rounded-2xl text-sm font-extrabold uppercase shadow-md {{ $statusClasses[$todayAttendance->status] ?? 'bg-slate-700 text-white' }}">
                                        ✔ {{ strtoupper($todayAttendance->status) }}
                                    </span>
                                    <p class="text-xs font-medium text-slate-600 mt-2">
                                        Catatan: <span class="font-semibold text-slate-900">{{ $todayAttendance->keterangan ?? 'Hadir tepat waktu' }}</span>
                                    </p>
                                    @if($todayAttendance->surat_izin)
                                        <div class="mt-2 text-xs">
                                            <a href="{{ asset($todayAttendance->surat_izin) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                                📷 Lihat Surat Izin / Dokter
                                            </a>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>Tingkat Kehadiran Bulanan:</span>
                            <span class="font-bold text-sky-600 text-sm">{{ $persentase }}%</span>
                        </div>
                    </div>

                    <!-- Digital QR Card Preview -->
                    <div class="bg-white p-6 rounded-3xl shadow-xs border border-slate-200/80 text-center flex flex-col items-center justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kartu Digital & QR Code</p>
                            <div class="mt-3 p-3 bg-slate-50 rounded-2xl border border-slate-200 inline-block shadow-2xs">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($student->nis) }}" 
                                     alt="QR Code {{ $student->nis }}"
                                     class="w-32 h-32 mx-auto rounded-xl">
                            </div>
                        </div>

                        <a href="{{ route('students.qr-card', $student) }}" target="_blank" class="w-full mt-3 py-2 bg-slate-900 hover:bg-slate-800 text-sky-400 font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            <span>Cetak Kartu Tanda Pelajar</span>
                        </a>
                    </div>

                </div>

                <!-- Form Unggah Foto Surat Izin / Sakit ke Wali Kelas -->
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 p-6">
                    <div class="border-b border-slate-100 pb-3 mb-4">
                        <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Kirim Foto Surat Izin / Sakit ke Wali Kelas</span>
                        </h3>
                        <p class="text-xs text-slate-500">Unggah foto surat permohonan izin atau surat keterangan dokter untuk dikonfirmasi oleh Wali Kelas ({{ $student->kelas }}).</p>
                    </div>

                    <form method="POST" action="{{ route('parent.upload-letter') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        @csrf
                        <div>
                            <label for="letter_tanggal" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Presensi</label>
                            <input type="date" id="letter_tanggal" name="tanggal" value="{{ now()->format('Y-m-d') }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:bg-white transition cursor-pointer">
                        </div>

                        <div>
                            <label for="letter_status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Keterangan</label>
                            <select id="letter_status" name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:bg-white transition cursor-pointer">
                                <option value="izin">Izin (Acara Keluarga / Urusan)</option>
                                <option value="sakit">Sakit (Surat Dokter / Sakit)</option>
                            </select>
                        </div>

                        <div>
                            <label for="surat_izin" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">File Foto Surat (JPG/PNG)</label>
                            <input type="file" id="surat_izin" name="surat_izin" accept="image/*" required class="block w-full text-[11px] text-slate-500 file:me-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>

                        <div>
                            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                <span>Kirim ke Wali Kelas</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Settings Form (Edit Email @student.sch.id & Password) -->
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 p-6">
                    <div class="border-b border-slate-100 pb-3 mb-4">
                        <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Pengaturan Akun Orang Tua (Email & Password)</span>
                        </h3>
                        <p class="text-xs text-slate-500">Perbarui email login (wajib berakhiran <span class="font-mono text-sky-600 font-bold">@student.sch.id</span>) dan kata sandi baru.</p>
                    </div>

                    <form method="POST" action="{{ route('parent.update-account') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Login (@student.sch.id)</label>
                            <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:bg-white transition">
                        </div>
                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Sandi Baru (Opsional)</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" id="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3 py-2 pe-10 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:bg-white transition">
                                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 end-0 px-2.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                    <template x-if="!showPassword">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </template>
                                    <template x-if="showPassword">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.05 10.05 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                                    </template>
                                </button>
                            </div>
                        </div>
                        <div x-data="{ showConfirmPassword: false }">
                            <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Konfirmasi Kata Sandi Baru</label>
                            <div class="flex items-center gap-2">
                                <div class="relative flex-1">
                                    <input :type="showConfirmPassword ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" placeholder="Ulangi kata sandi baru" class="w-full px-3 py-2 pe-10 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-sky-500 focus:bg-white transition">
                                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 end-0 px-2.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none cursor-pointer">
                                        <template x-if="!showConfirmPassword">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </template>
                                        <template x-if="showConfirmPassword">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.05 10.05 0 014.122-.963c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18"/></svg>
                                        </template>
                                    </button>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-sky-400 font-bold text-xs rounded-xl transition cursor-pointer shrink-0">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                <!-- Parent Child Attendance Analytics Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    <!-- 7-Day Attendance Trend Line Chart -->
                    <div class="lg:col-span-7 bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-bold text-base text-slate-900 flex items-center gap-2">
                                    <span>📊 Tren Kehadiran Anak (7 Hari Terakhir)</span>
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5 font-medium">Grafik riwayat kehadiran {{ $student->nama }} per hari</p>
                            </div>
                            <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-sky-50 text-sky-700 border border-sky-200/80">
                                7 Hari Terakhir
                            </span>
                        </div>
                        <div class="relative h-60 w-full">
                            <canvas id="parentWeeklyChart"></canvas>
                        </div>
                    </div>

                    <!-- Monthly Distribution Doughnut Chart -->
                    <div class="lg:col-span-5 bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-base text-slate-900">🍩 Ringkasan Presensi Bulanan</h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-medium">Persentase kehadiran {{ $student->nama }} ({{ $persentase }}% Hadir)</p>
                        </div>
                        <div class="relative h-48 w-full flex items-center justify-center my-2">
                            <canvas id="parentMonthlyChart"></canvas>
                        </div>
                        <!-- Mini Legend Summary -->
                        <div class="grid grid-cols-2 gap-2 text-xs font-semibold pt-3 border-t border-slate-100">
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-slate-600">Hadir:</span> <strong class="text-slate-900">{{ $totalHadir }}</strong></div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span><span class="text-slate-600">Izin:</span> <strong class="text-slate-900">{{ $totalIzin }}</strong></div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span class="text-slate-600">Sakit:</span> <strong class="text-slate-900">{{ $totalSakit }}</strong></div>
                            <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span><span class="text-slate-600">Alpa:</span> <strong class="text-slate-900">{{ $totalAlpa }}</strong></div>
                        </div>
                    </div>

                </div>

                <!-- History Table Card -->
                <div class="bg-white rounded-3xl shadow-xs border border-slate-200/80 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">Riwayat Presensi Bulanan</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Daftar kehadiran anak di sekolah</p>
                        </div>
                        <form method="GET" action="{{ route('parent.dashboard') }}" class="flex items-center gap-2">
                            <select name="month" onchange="this.form.submit()" class="py-1.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-sky-500">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                        Bulan {{ date('F', mktime(0,0,0,$m,1)) }}
                                    </option>
                                @endfor
                            </select>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-900 text-xs font-bold text-slate-200 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Keterangan</th>
                                    <th class="px-6 py-4 text-center">Lampiran Surat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($attendances as $att)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 font-bold text-slate-900">
                                            {{ \Carbon\Carbon::parse($att->tanggal)->isoFormat('D MMMM YYYY') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $badges = [
                                                    'hadir' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                    'izin' => 'bg-sky-50 text-sky-900 border-sky-200',
                                                    'sakit' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'alpa' => 'bg-rose-100 text-rose-800 border-rose-200',
                                                    'libur' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                ];
                                            @endphp
                                            <span class="inline-block px-2.5 py-0.5 rounded-md text-xs font-bold border uppercase {{ $badges[$att->status] ?? 'bg-slate-100 text-slate-700' }}">
                                                {{ $att->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-slate-600 text-xs">
                                            {{ $att->keterangan ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($att->surat_izin)
                                                <a href="{{ asset($att->surat_izin) }}" target="_blank" class="px-3 py-1 bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs rounded-lg border border-blue-200 inline-flex items-center gap-1 transition">
                                                    📷 Lihat Surat
                                                </a>
                                            @else
                                                <span class="text-slate-300 text-xs">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 text-xs">
                                            Belum ada data presensi tercatat untuk bulan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @endif

        </div>
    </div>

    <!-- Chart.js CDN for Parent Portal -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Parent 7-Day Trend Chart
            const parentWeeklyCanvas = document.getElementById('parentWeeklyChart');
            if (parentWeeklyCanvas) {
                const pCtx = parentWeeklyCanvas.getContext('2d');
                
                const hadirGrad = pCtx.createLinearGradient(0, 0, 0, 200);
                hadirGrad.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
                hadirGrad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                new Chart(pCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($weeklyDates ?? []) !!},
                        datasets: [
                            {
                                label: 'Hadir',
                                data: {!! json_encode($weeklyHadir ?? []) !!},
                                borderColor: '#10b981',
                                backgroundColor: hadirGrad,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3,
                                pointBackgroundColor: '#10b981',
                                pointRadius: 4
                            },
                            {
                                label: 'Izin / Sakit',
                                data: {!! json_encode($weeklyIzinSakit ?? []) !!},
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 2,
                                fill: false,
                                tension: 0.3,
                                pointBackgroundColor: '#3b82f6',
                                pointRadius: 3
                            },
                            {
                                label: 'Alpa',
                                data: {!! json_encode($weeklyAlpa ?? []) !!},
                                borderColor: '#f43f5e',
                                backgroundColor: 'rgba(244, 63, 94, 0.1)',
                                borderWidth: 2,
                                borderDash: [3, 3],
                                fill: false,
                                tension: 0.3,
                                pointBackgroundColor: '#f43f5e',
                                pointRadius: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Figtree', size: 11, weight: 'bold' } }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { family: 'Figtree', size: 11, weight: 'bold' } } },
                            y: {
                                min: 0,
                                max: 1,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(val) { return val === 1 ? 'Ada' : 'Tidak'; },
                                    font: { family: 'Figtree', size: 11 }
                                },
                                grid: { color: '#f1f5f9' }
                            }
                        }
                    }
                });
            }

            // 2. Parent Monthly Distribution Doughnut Chart
            const parentMonthlyCanvas = document.getElementById('parentMonthlyChart');
            if (parentMonthlyCanvas) {
                const pmCtx = parentMonthlyCanvas.getContext('2d');
                new Chart(pmCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                        datasets: [{
                            data: [{{ $totalHadir ?? 0 }}, {{ $totalIzin ?? 0 }}, {{ $totalSakit ?? 0 }}, {{ $totalAlpa ?? 0 }}],
                            backgroundColor: ['#10b981', '#38bdf8', '#f59e0b', '#f43f5e'],
                            borderWidth: 3,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
