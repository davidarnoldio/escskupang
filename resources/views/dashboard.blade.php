<x-app-layout>
    <div class="space-y-6">
        
        <!-- 1. Top Row: 4 Stat Cards with Sparklines -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Card 1: Total Siswa -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative overflow-hidden flex flex-col justify-between hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total Siswa</div>
                        <div class="text-3xl font-black text-slate-900 mt-1">{{ $totalSiswa }}</div>
                        <div class="text-xs font-semibold text-slate-500 mt-1">Siswa terdaftar</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-600/10 text-blue-600 flex items-center justify-center text-xl font-bold shadow-xs">
                        👥
                    </div>
                </div>
                <!-- Sparkline SVG Curve -->
                <div class="mt-4 pt-2">
                    <svg class="w-full h-8 text-blue-500 stroke-current" fill="none" viewBox="0 0 100 25" preserveAspectRatio="none">
                        <path d="M0 20 Q 25 5, 50 15 T 100 8" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <!-- Card 2: Hadir Hari Ini -->
            @php
                $hadirPercentage = $totalSiswa > 0 ? round(($hadirHariIni / $totalSiswa) * 100, 1) : 0;
            @endphp
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative overflow-hidden flex flex-col justify-between hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Hadir Hari Ini</div>
                        <div class="text-3xl font-black text-slate-900 mt-1">{{ $hadirHariIni }}</div>
                        <div class="text-xs font-semibold text-emerald-600 mt-1">{{ $hadirPercentage }}% dari total siswa</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-xl font-bold shadow-xs">
                        ✓
                    </div>
                </div>
                <!-- Sparkline SVG Curve -->
                <div class="mt-4 pt-2">
                    <svg class="w-full h-8 text-emerald-500 stroke-current" fill="none" viewBox="0 0 100 25" preserveAspectRatio="none">
                        <path d="M0 18 Q 25 22, 50 8 T 100 12" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <!-- Card 3: Izin & Sakit -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative overflow-hidden flex flex-col justify-between hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-blue-600">Izin & Sakit</div>
                        <div class="text-3xl font-black text-slate-900 mt-1">{{ $izinSakitHariIni }}</div>
                        <div class="text-xs font-semibold text-slate-500 mt-1">Keterangan resmi</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-xl font-bold shadow-xs">
                        ⓘ
                    </div>
                </div>
                <!-- Sparkline SVG Curve -->
                <div class="mt-4 pt-2">
                    <svg class="w-full h-8 text-blue-500 stroke-current" fill="none" viewBox="0 0 100 25" preserveAspectRatio="none">
                        <path d="M0 12 Q 25 18, 50 10 T 100 20" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <!-- Card 4: Alpa Hari Ini -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative overflow-hidden flex flex-col justify-between hover:shadow-md transition duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-wider text-rose-600">Alpa Hari Ini</div>
                        <div class="text-3xl font-black text-slate-900 mt-1">{{ $alpaHariIni }}</div>
                        <div class="text-xs font-semibold text-rose-600 mt-1">Tanpa keterangan</div>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 flex items-center justify-center text-xl font-bold shadow-xs">
                        ✕
                    </div>
                </div>
                <!-- Sparkline SVG Curve -->
                <div class="mt-4 pt-2">
                    <svg class="w-full h-8 text-rose-500 stroke-current" fill="none" viewBox="0 0 100 25" preserveAspectRatio="none">
                        <path d="M0 15 Q 25 8, 50 20 T 100 5" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

        </div>

        <!-- 2. Interactive Analytics Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Main Trend Chart (8 cols on lg) -->
            <div class="lg:col-span-8 bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <span>📊 Tren Presensi 7 Hari Terakhir</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">Grafik perbandingan status presensi harian siswa</p>
                    </div>
                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200/80">
                        7 Hari Terakhir
                    </span>
                </div>
                <div class="relative h-64 sm:h-72 w-full">
                    <canvas id="weeklyAttendanceChart"></canvas>
                </div>
            </div>

            <!-- Today's Attendance Distribution Doughnut Chart (4 cols on lg) -->
            <div class="lg:col-span-4 bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">🍩 Distribusi Hari Ini</h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Proporsi status presensi siswa</p>
                </div>
                <div class="relative h-52 w-full flex items-center justify-center my-2">
                    <canvas id="todayDistributionChart"></canvas>
                </div>
                <!-- Mini Legend Summary -->
                <div class="grid grid-cols-2 gap-2 text-xs font-semibold pt-3 border-t border-slate-100">
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span><span class="text-slate-600">Hadir:</span> <strong class="text-slate-900">{{ $hadirHariIni }}</strong></div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span><span class="text-slate-600">Izin:</span> <strong class="text-slate-900">{{ $izinHariIni }}</strong></div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span><span class="text-slate-600">Sakit:</span> <strong class="text-slate-900">{{ $sakitHariIni }}</strong></div>
                    <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span><span class="text-slate-600">Alpa:</span> <strong class="text-slate-900">{{ $alpaHariIni }}</strong></div>
                </div>
            </div>

        </div>

        <!-- 3. Bottom Row Grid: Aksi Cepat & Siswa Terbaru -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left Column (5 cols): Aksi Cepat Tiles -->
            <div class="lg:col-span-5 bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Aksi Cepat</h3>
                    <p class="text-xs text-slate-500 mt-0.5 font-medium">Akses fitur presensi utama dengan mudah</p>
                </div>

                <div class="space-y-3.5 mt-6">
                    <!-- Tile 1: Scan QR Code Presensi -->
                    <a href="{{ route('qr.scan') }}" class="block p-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/30 hover:shadow-xl hover:scale-[1.01] transition duration-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-xl">
                                    📷
                                </div>
                                <div>
                                    <div class="text-sm font-extrabold">Scan QR Code Presensi</div>
                                    <div class="text-xs text-blue-100/90 font-medium mt-0.5">Scan QR untuk presensi cepat</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </a>

                    <!-- Tile 2: Input Presensi Manual -->
                    <a href="{{ route('attendances.index') }}" class="block p-4 rounded-2xl bg-slate-50/80 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-200 transition duration-200 group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition">
                                    ✍️
                                </div>
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition">Input Presensi Manual</div>
                                    <div class="text-xs text-slate-500 font-medium mt-0.5">Input presensi secara manual</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </a>

                    <!-- Tile 3: Tambah Siswa Baru -->
                    <a href="{{ route('students.create') }}" class="block p-4 rounded-2xl bg-slate-50/80 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-200 transition duration-200 group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition">
                                    👤
                                </div>
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition">Tambah Siswa Baru</div>
                                    <div class="text-xs text-slate-500 font-medium mt-0.5">Daftarkan siswa baru ke sistem</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </a>

                    <!-- Tile 4: Rekap Absensi Bulanan -->
                    <a href="{{ route('attendances.rekap') }}" class="block p-4 rounded-2xl bg-slate-50/80 hover:bg-blue-50/60 border border-slate-200/80 hover:border-blue-200 transition duration-200 group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-blue-600/10 text-blue-600 flex items-center justify-center text-xl group-hover:scale-110 transition">
                                    📊
                                </div>
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900 group-hover:text-blue-600 transition">Rekap Absensi Bulanan</div>
                                    <div class="text-xs text-slate-500 font-medium mt-0.5">Lihat rekap absensi per bulan</div>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Right Column (7 cols): Siswa Terbaru -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Siswa Terbaru</h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-medium">Daftar siswa yang baru terdaftar</p>
                        </div>
                        <a href="{{ route('students.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                            Lihat Semua Siswa →
                        </a>
                    </div>

                    <!-- Students List -->
                    <div class="divide-y divide-slate-100 mt-5">
                        @forelse($recentStudents as $student)
                            <div class="py-3.5 flex items-center justify-between hover:bg-slate-50/80 px-2 rounded-2xl transition">
                                <div class="flex items-center gap-3.5">
                                    <!-- Student Circle Avatar Badge -->
                                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                                        {{ strtoupper(substr($student->nama, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                            <span>{{ $student->nama }}</span>
                                            @if($student->is_abk)
                                                <span class="px-1.5 py-0.5 text-[9px] font-black rounded bg-amber-100 text-amber-900 border border-amber-300">ABK</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] font-semibold text-slate-400 mt-0.5">
                                            NIS: {{ $student->nis ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Class Badge Pill -->
                                <div>
                                    <span class="px-3 py-1 text-[11px] font-extrabold rounded-full bg-blue-50 text-blue-700 border border-blue-200/80">
                                        Kelas {{ $student->kelas }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-xs font-semibold text-slate-400 italic">
                                Belum ada siswa terdaftar di {{ $assignedClass ? 'kelas ' . $assignedClass : 'sistem' }}.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                    <a href="{{ route('students.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                        <span>👥 Lihat Semua Siswa</span>
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Weekly Trend Line Chart
            const weeklyCanvas = document.getElementById('weeklyAttendanceChart');
            if (weeklyCanvas) {
                const weeklyCtx = weeklyCanvas.getContext('2d');
                
                const hadirGradient = weeklyCtx.createLinearGradient(0, 0, 0, 250);
                hadirGradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
                hadirGradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

                const izinSakitGradient = weeklyCtx.createLinearGradient(0, 0, 0, 250);
                izinSakitGradient.addColorStop(0, 'rgba(59, 130, 246, 0.25)');
                izinSakitGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');

                new Chart(weeklyCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($weeklyDates) !!},
                        datasets: [
                            {
                                label: 'Hadir',
                                data: {!! json_encode($weeklyHadir) !!},
                                borderColor: '#10b981',
                                backgroundColor: hadirGradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#10b981',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Izin / Sakit',
                                data: {!! json_encode($weeklyIzinSakit) !!},
                                borderColor: '#3b82f6',
                                backgroundColor: izinSakitGradient,
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.35,
                                pointBackgroundColor: '#3b82f6',
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Alpa',
                                data: {!! json_encode($weeklyAlpa) !!},
                                borderColor: '#f43f5e',
                                backgroundColor: 'rgba(244, 63, 94, 0.05)',
                                borderWidth: 2,
                                borderDash: [4, 4],
                                fill: false,
                                tension: 0.35,
                                pointBackgroundColor: '#f43f5e',
                                pointRadius: 3,
                                pointHoverRadius: 5
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
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    font: { family: 'Figtree', size: 12, weight: 'bold' }
                                }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                padding: 10,
                                cornerRadius: 12
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Figtree', size: 11, weight: 'bold' }, color: '#64748b' }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { precision: 0, font: { family: 'Figtree', size: 11 }, color: '#64748b' }
                            }
                        }
                    }
                });
            }

            // 2. Today's Distribution Doughnut Chart
            const todayCanvas = document.getElementById('todayDistributionChart');
            if (todayCanvas) {
                const todayCtx = todayCanvas.getContext('2d');
                new Chart(todayCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Izin', 'Sakit', 'Alpa', 'Belum Presensi'],
                        datasets: [{
                            data: [
                                {{ $hadirHariIni }},
                                {{ $izinHariIni }},
                                {{ $sakitHariIni }},
                                {{ $alpaHariIni }},
                                {{ $belumPresensiHariIni }}
                            ],
                            backgroundColor: ['#10b981', '#38bdf8', '#f59e0b', '#f43f5e', '#e2e8f0'],
                            borderWidth: 3,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ' + context.raw + ' siswa';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
