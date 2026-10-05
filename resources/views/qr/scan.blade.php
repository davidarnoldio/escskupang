<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-2xl text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    <span>Scan QR Code Presensi Siswa & Guru</span>
                </h2>
                <p class="text-xs font-semibold text-slate-500 mt-1">Mendukung Absensi 2 Tahap (Scan 1: Masuk & Scan 2: Pulang) Mandiri</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('attendances.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
                    ← Kembali ke Presensi
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Include html5-qrcode scanner library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div class="py-8" x-data="qrScannerApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Scanner Card (Camera & Manual Input) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-3xl shadow-xs border border-slate-200/80">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-600 animate-pulse"></span>
                                    <span>Kamera Scanner Terpadu</span>
                                </h3>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">Arahkan QR Code Kartu Pelajar Siswa atau Kartu Guru</p>
                            </div>
                            <button @click="toggleCamera()" type="button" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-black rounded-xl transition cursor-pointer flex items-center gap-1.5 shadow-sm">
                                <span x-text="cameraActive ? '⏹ Matikan Kamera' : '🎥 Aktifkan Kamera'"></span>
                            </button>
                        </div>

                        <!-- Camera Preview Box -->
                        <div class="relative w-full bg-slate-950 rounded-2xl overflow-hidden min-h-[320px] flex items-center justify-center border border-slate-800">
                            <div id="reader" class="w-full"></div>
                            
                            <div x-show="!cameraActive" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-slate-400 bg-slate-950">
                                <svg class="w-16 h-16 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-bold text-slate-300">Kamera Belum Aktif</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-xs">Klik tombol "Aktifkan Kamera" di atas untuk mulai memindai QR Code siswa atau guru</p>
                            </div>
                        </div>

                        <!-- Manual / Barcode Scanner Input Form -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <form @submit.prevent="submitManualNis()" class="flex flex-col sm:flex-row gap-2">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    </div>
                                    <input x-model="manualNis" type="text" placeholder="Masukkan / Barcode Scan (NISN Siswa, GURU-1, atau Email Guru)..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-semibold focus:ring-2 focus:ring-red-500 focus:bg-white transition" autofocus>
                                </div>
                                <button type="submit" :disabled="loading" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-xl text-xs transition cursor-pointer flex items-center justify-center gap-2 shadow-sm">
                                    <span x-text="loading ? 'Memproses...' : 'Proses Scan'"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Result Card & Today's Scan Stream -->
                <div class="space-y-6">
                    <!-- Scan Result Feedback Alert -->
                    <div x-show="lastResult" x-transition class="bg-white p-6 rounded-3xl shadow-md border"
                         :class="!lastResult?.success ? 'border-rose-300 bg-rose-50/70' : (lastResult?.scan_type === 'pulang' ? 'border-indigo-200 bg-indigo-50/40' : (lastResult?.attendance?.is_late ? 'border-amber-300 bg-amber-50/80' : 'border-emerald-200 bg-emerald-50/50'))">
                        
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg font-black shrink-0 shadow-sm"
                                 :class="!lastResult?.success ? 'bg-rose-600 text-white' : (lastResult?.scan_type === 'pulang' ? 'bg-indigo-600 text-white' : (lastResult?.attendance?.is_late ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white'))">
                                <span x-text="!lastResult?.success ? '✕' : (lastResult?.scan_type === 'pulang' ? '🏠' : (lastResult?.attendance?.is_late ? '⚠️' : '✓'))"></span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <template x-if="lastResult?.role_type">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider"
                                              :class="lastResult?.role_type === 'teacher' ? 'bg-purple-100 text-purple-900 border border-purple-200' : 'bg-slate-100 text-slate-800 border border-slate-200'"
                                              x-text="lastResult?.role_type === 'teacher' ? '👨‍🏫 Guru' : '🎒 Siswa'"></span>
                                    </template>

                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider"
                                          :class="!lastResult?.success ? 'bg-rose-100 text-rose-900 border border-rose-300 font-extrabold' : (lastResult?.scan_type === 'pulang' ? 'bg-indigo-100 text-indigo-900 border border-indigo-200' : (lastResult?.attendance?.is_late ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-emerald-100 text-emerald-900 border border-emerald-200'))"
                                          x-text="!lastResult?.success ? (lastResult?.already_completed ? 'TIDAK VALID (>2x SCAN)' : (lastResult?.outside_hours ? 'DI LUAR JAM' : 'DITOLAK')) : (lastResult?.scan_type === 'pulang' ? 'SCAN 2: PULANG' : 'SCAN 1: MASUK')"></span>
                                </div>

                                <h4 class="font-extrabold text-sm mt-1"
                                    :class="!lastResult?.success ? 'text-rose-900 font-black' : (lastResult?.scan_type === 'pulang' ? 'text-indigo-950' : (lastResult?.attendance?.is_late ? 'text-amber-950' : 'text-emerald-950'))"
                                    x-text="lastResult?.title || (!lastResult?.success ? 'Presensi Ditolak!' : (lastResult?.scan_type === 'pulang' ? 'Presensi PULANG Berhasil!' : (lastResult?.attendance?.is_late ? 'Presensi MASUK (TERLAMBAT)!' : 'Presensi MASUK (Tepat Waktu)!')))"></h4>
                                
                                <p class="text-xs mt-0.5 font-semibold leading-relaxed"
                                   :class="!lastResult?.success ? 'text-rose-800' : (lastResult?.scan_type === 'pulang' ? 'text-indigo-900' : (lastResult?.attendance?.is_late ? 'text-amber-900' : 'text-emerald-900'))"
                                   x-text="lastResult?.message"></p>
                            </div>
                        </div>

                        <!-- Person Card Detail -->
                        <template x-if="lastResult?.person">
                            <div class="mt-4 p-4 bg-white/90 rounded-2xl border border-slate-100 shadow-2xs space-y-2">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400 font-medium">Nama</span>
                                    <span class="font-extrabold text-xs sm:text-sm text-slate-900" x-text="lastResult.person.nama"></span>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400 font-medium" x-text="lastResult.role_type === 'teacher' ? 'Email / Akun' : 'NISN'"></span>
                                    <span class="font-mono text-xs font-bold text-slate-700" x-text="lastResult.person.nisn || lastResult.person.email"></span>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400 font-medium">Kelas / Peran</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold px-2 py-0.5 bg-slate-900 text-white rounded-md" x-text="lastResult.person.kelas"></span>
                                        <template x-if="lastResult.person.is_abk">
                                            <span class="text-[9px] font-black px-1.5 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded">ABK</span>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400 font-medium">Jam Masuk (WITA)</span>
                                    <span class="font-mono text-xs font-extrabold text-slate-800" x-text="lastResult.attendance?.jam_masuk || '-'"></span>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400 font-medium">Jam Pulang (WITA)</span>
                                    <span class="font-mono text-xs font-extrabold" :class="lastResult.attendance?.jam_pulang ? 'text-indigo-600' : 'text-slate-400'" x-text="lastResult.attendance?.jam_pulang || 'Belum Scan Pulang'"></span>
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-xs text-slate-400 font-medium">Status</span>
                                    <span class="text-xs font-black px-2.5 py-0.5 rounded-md"
                                          :class="!lastResult?.success ? 'bg-rose-100 text-rose-900 border border-rose-300' : (lastResult?.scan_type === 'pulang' ? 'bg-indigo-100 text-indigo-900 border border-indigo-200' : (lastResult?.attendance?.is_late ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300'))"
                                          x-text="lastResult?.attendance?.status_text || (!lastResult?.success ? 'DITOLAK' : 'HADIR')"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Scan History Log (Students & Teachers) -->
                    <div class="bg-white p-6 rounded-3xl shadow-xs border border-slate-200/80">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-100 pb-3">
                            <h3 class="font-extrabold text-xs text-slate-900 uppercase tracking-wider">Aktivitas Hari Ini ({{ \Carbon\Carbon::parse($today)->locale('id')->isoFormat('D MMMM YYYY') }})</h3>
                        </div>
                        
                        <!-- Tabs for student vs teacher stream -->
                        <div x-data="{ activeTab: 'students' }">
                            <div class="flex gap-2 mb-3">
                                <button @click="activeTab = 'students'" type="button" class="flex-1 py-1.5 px-3 rounded-xl text-xs font-bold transition"
                                        :class="activeTab === 'students' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                    🎒 Siswa ({{ count($todayAttendances) }})
                                </button>
                                <button @click="activeTab = 'teachers'" type="button" class="flex-1 py-1.5 px-3 rounded-xl text-xs font-bold transition"
                                        :class="activeTab === 'teachers' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                    👨‍🏫 Guru ({{ count($todayTeacherAttendances) }})
                                </button>
                            </div>

                            <!-- Student List -->
                            <div x-show="activeTab === 'students'" class="divide-y divide-slate-100 max-h-[350px] overflow-y-auto space-y-1">
                                @forelse($todayAttendances as $att)
                                    @php
                                        $isLateHistory = str_contains(strtolower($att->keterangan ?? ''), 'terlambat');
                                    @endphp
                                    <div class="py-2.5 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2.5">
                                            @if($att->student && $att->student->foto_url)
                                                <img src="{{ $att->student->foto_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0">
                                            @else
                                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center border border-slate-200 shrink-0">
                                                    {{ strtoupper(substr($att->student->nama ?? 'S', 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-extrabold text-slate-900 flex items-center gap-1.5">
                                                    <span>{{ $att->student->nama ?? '-' }}</span>
                                                    @if($att->student && $att->student->is_abk)
                                                        <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-amber-100 text-amber-900 border border-amber-300">ABK</span>
                                                    @endif
                                                </p>
                                                <p class="text-[10px] text-slate-400 font-medium">{{ $att->student->kelas ?? '-' }} &bull; In: {{ $att->jam_masuk ?? '-' }} | Out: {{ $att->jam_pulang ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($att->jam_pulang)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-200">
                                                    🏠 PULANG
                                                </span>
                                            @elseif($isLateHistory)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                    ⚠️ TERLAMBAT
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    ✓ MASUK
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-6 text-center font-medium">Belum ada aktivitas presensi siswa hari ini.</p>
                                @endforelse
                            </div>

                            <!-- Teacher List -->
                            <div x-show="activeTab === 'teachers'" class="divide-y divide-slate-100 max-h-[350px] overflow-y-auto space-y-1">
                                @forelse($todayTeacherAttendances as $tAtt)
                                    @php
                                        $isTeacherLate = str_contains(strtolower($tAtt->keterangan ?? ''), 'terlambat');
                                    @endphp
                                    <div class="py-2.5 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-slate-900 text-amber-400 font-black text-[10px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($tAtt->teacher->name ?? 'G', 0, 2)) }}
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-slate-900">{{ $tAtt->teacher->name ?? '-' }}</p>
                                                <p class="text-[10px] text-slate-400 font-medium">In: {{ $tAtt->jam_masuk ?? '-' }} | Out: {{ $tAtt->jam_pulang ?? '-' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($tAtt->jam_pulang)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-indigo-50 text-indigo-800 border border-indigo-200">
                                                    🏠 PULANG
                                                </span>
                                            @elseif($isTeacherLate)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                    ⚠️ TERLAMBAT
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    ✓ MASUK
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 py-6 text-center font-medium">Belum ada aktivitas presensi guru hari ini.</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function qrScannerApp() {
            return {
                cameraActive: false,
                manualNis: '',
                loading: false,
                lastResult: null,
                html5QrCode: null,
                audioCtx: null,

                init() {
                    try {
                        this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    } catch (e) {}
                },

                playBeep(success = true) {
                    try {
                        if (!this.audioCtx) {
                            this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        }
                        const osc = this.audioCtx.createOscillator();
                        const gain = this.audioCtx.createGain();
                        osc.type = success ? 'sine' : 'sawtooth';
                        osc.frequency.value = success ? 880 : 330;
                        gain.gain.setValueAtTime(0.15, this.audioCtx.currentTime);
                        osc.connect(gain);
                        gain.connect(this.audioCtx.destination);
                        osc.start();
                        osc.stop(this.audioCtx.currentTime + (success ? 0.15 : 0.3));
                    } catch (e) {}
                },

                toggleCamera() {
                    if (this.cameraActive) {
                        this.stopCamera();
                    } else {
                        this.startCamera();
                    }
                },

                startCamera() {
                    this.html5QrCode = new Html5Qrcode("reader");
                    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                    this.html5QrCode.start(
                        { facingMode: "environment" },
                        config,
                        (decodedText) => {
                            this.processNis(decodedText);
                        },
                        (errorMessage) => {}
                    ).then(() => {
                        this.cameraActive = true;
                    }).catch(err => {
                        alert("Gagal mengaktifkan kamera: " + err);
                    });
                },

                stopCamera() {
                    if (this.html5QrCode) {
                        this.html5QrCode.stop().then(() => {
                            this.cameraActive = false;
                        });
                    }
                },

                submitManualNis() {
                    if (!this.manualNis.trim()) return;
                    this.processNis(this.manualNis);
                    this.manualNis = '';
                },

                async processNis(code) {
                    if (this.loading) return;
                    this.loading = true;

                    try {
                        const response = await fetch("{{ route('qr.process', [], false) }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ code: code, nis: code })
                        });

                        let data;
                        try {
                            data = await response.json();
                        } catch (jsonErr) {
                            const errText = await response.text();
                            data = {
                                success: false,
                                message: "Respon server (" + response.status + "): " + (errText.replace(/<[^>]*>?/gm, '').trim().substring(0, 150) || "Gagal memproses respon server.")
                            };
                        }
                        this.lastResult = data;
                        this.playBeep(data.success);
                    } catch (error) {
                        this.lastResult = {
                            success: false,
                            message: "Gagal terhubung ke server presensi. Pastikan koneksi internet stabil."
                        };
                        this.playBeep(false);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
