<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 leading-tight">
                    Scan QR Code Presensi - ESCS Kupang
                </h2>
                <p class="text-xs text-slate-500 mt-1">Arahkan kamera ke QR Code siswa atau gunakan barcode scanner</p>
            </div>
            <a href="{{ route('attendances.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition">
                Kembali ke Presensi Harian
            </a>
        </div>
    </x-slot>

    <!-- Include html5-qrcode scanner library -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div class="py-8" x-data="qrScannerApp()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Scanner Card (Camera & Manual Input) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200/80">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-lg text-slate-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                Scanner Kamera Live
                            </h3>
                            <button @click="toggleCamera()" type="button" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-sky-400 text-xs font-bold rounded-xl transition cursor-pointer">
                                <span x-text="cameraActive ? '⏹ Matikan Kamera' : '🎥 Aktifkan Kamera'"></span>
                            </button>
                        </div>

                        <!-- Camera Preview Box -->
                        <div class="relative w-full bg-slate-950 rounded-2xl overflow-hidden min-h-[300px] flex items-center justify-center border border-slate-800">
                            <div id="reader" class="w-full"></div>
                            
                            <div x-show="!cameraActive" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-slate-400 bg-slate-950">
                                <svg class="w-16 h-16 text-slate-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-bold text-slate-300">Kamera Belum Aktif</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-xs">Klik tombol "Aktifkan Kamera" di atas untuk mulai memindai QR Code siswa ESCS Kupang</p>
                            </div>
                        </div>

                        <!-- Manual / Barcode Scanner Input Form -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <form @submit.prevent="submitManualNis()" class="flex gap-2">
                                <div class="relative flex-1">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    </div>
                                    <input x-model="manualNis" type="text" placeholder="Masukkan / Scan NIS Manual (Contoh: 10201)..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:bg-white transition" autofocus>
                                </div>
                                <button type="submit" :disabled="loading" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-sky-400 font-bold rounded-xl text-sm transition cursor-pointer flex items-center gap-2">
                                    <span x-text="loading ? 'Proses...' : 'Proses Scan'"></span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Result Card & Today's Scan Stream -->
                <div class="space-y-6">
                    <!-- Scan Result Feedback Alert -->
                    <div x-show="lastResult" x-transition class="bg-white p-6 rounded-2xl shadow-xs border"
                         :class="!lastResult?.success ? 'border-rose-200 bg-rose-50/40' : (lastResult?.attendance?.is_late ? 'border-amber-300 bg-amber-50/70' : 'border-emerald-200 bg-emerald-50/40')">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold shrink-0"
                                 :class="!lastResult?.success ? 'bg-rose-500 text-white' : (lastResult?.attendance?.is_late ? 'bg-amber-500 text-white shadow-md' : 'bg-emerald-500 text-white')">
                                <span x-text="!lastResult?.success ? '✕' : (lastResult?.attendance?.is_late ? '⚠️' : '✓')"></span>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm"
                                    :class="!lastResult?.success ? 'text-rose-900' : (lastResult?.attendance?.is_late ? 'text-amber-900 font-extrabold' : 'text-emerald-900')"
                                    x-text="!lastResult?.success ? 'Presensi Gagal!' : (lastResult?.attendance?.is_late ? 'Presensi Berhasil (TERLAMBAT)!' : 'Presensi Berhasil (Tepat Waktu)!')"></h4>
                                <p class="text-xs" :class="lastResult?.attendance?.is_late ? 'text-amber-950 font-medium' : 'text-slate-600'" x-text="lastResult?.message"></p>
                            </div>
                        </div>

                        <template x-if="lastResult?.success && lastResult?.student">
                            <div class="mt-4 p-4 bg-white rounded-xl border shadow-2xs space-y-2"
                                 :class="lastResult?.attendance?.is_late ? 'border-amber-300 bg-amber-50/30' : 'border-emerald-100'">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400">Nama Siswa</span>
                                    <span class="font-bold text-sm text-slate-900" x-text="lastResult.student.nama"></span>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400">NIS</span>
                                    <span class="font-mono text-xs font-bold text-slate-700" x-text="lastResult.student.nis"></span>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400">Kelas</span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold px-2 py-0.5 bg-slate-900 text-sky-400 rounded-md" x-text="lastResult.student.kelas"></span>
                                        <template x-if="lastResult.student.is_abk">
                                            <span class="text-[10px] font-black px-1.5 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded">ABK</span>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                    <span class="text-xs text-slate-400">Status Presensi</span>
                                    <span class="text-xs font-black px-2.5 py-0.5 rounded-md"
                                          :class="lastResult.attendance?.is_late ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-emerald-100 text-emerald-800 border border-emerald-300'"
                                          x-text="lastResult.attendance?.status_text || 'HADIR'"></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-slate-400">Waktu Scan (WITA)</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs font-bold" :class="lastResult.attendance?.is_late ? 'text-amber-700 font-extrabold' : 'text-emerald-600'" x-text="lastResult.attendance.waktu"></span>
                                        <template x-if="lastResult.attendance?.is_late">
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 bg-amber-100 text-amber-800 rounded border border-amber-200">
                                                Batas: <span x-text="lastResult.attendance.jam_terlambat"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Scan History Log -->
                    <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-200/80">
                        <h3 class="font-bold text-sm text-slate-900 mb-3">Scan Terakhir Hari Ini ({{ \Carbon\Carbon::parse($today)->locale('id')->isoFormat('D MMMM YYYY') }})</h3>
                        
                        <div class="divide-y divide-slate-100 max-h-[350px] overflow-y-auto space-y-1">
                            @forelse($todayAttendances as $att)
                                @php
                                    $isLateHistory = str_contains(strtolower($att->keterangan ?? ''), 'terlambat');
                                @endphp
                                <div class="py-2.5 flex items-center justify-between text-xs">
                                    <div>
                                        <p class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $att->student->nama }}</span>
                                            @if($att->student->is_abk)
                                                <span class="px-1.5 py-0.2 text-[9px] font-black rounded bg-amber-100 text-amber-900 border border-amber-300">ABK</span>
                                            @endif
                                        </p>
                                        <p class="text-[11px] text-slate-400">{{ $att->student->kelas }} (NIS: {{ $att->student->nis }})</p>
                                    </div>
                                    <div class="text-right">
                                        @if($isLateHistory)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                                ⚠️ TERLAMBAT
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                HADIR
                                            </span>
                                        @endif
                                        <p class="font-mono text-[10px] text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($att->updated_at)->timezone('Asia/Makassar')->format('H:i:s') }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 py-4 text-center">Belum ada aktivitas scan hari ini.</p>
                            @endforelse
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

                init() {
                    this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                },

                playBeep() {
                    try {
                        const osc = this.audioCtx.createOscillator();
                        const gain = this.audioCtx.createGain();
                        osc.type = 'sine';
                        osc.frequency.value = 880;
                        gain.gain.setValueAtTime(0.1, this.audioCtx.currentTime);
                        osc.connect(gain);
                        gain.connect(this.audioCtx.destination);
                        osc.start();
                        osc.stop(this.audioCtx.currentTime + 0.15);
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
                            this.playBeep();
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
                    this.playBeep();
                    this.processNis(this.manualNis);
                    this.manualNis = '';
                },

                async processNis(nis) {
                    this.loading = true;
                    try {
                        const response = await fetch("{{ route('qr.process') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ nis: nis })
                        });

                        const data = await response.json();
                        this.lastResult = data;
                    } catch (error) {
                        this.lastResult = {
                            success: false,
                            message: "Terjadi kesalahan jaringan/server!"
                        };
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>
