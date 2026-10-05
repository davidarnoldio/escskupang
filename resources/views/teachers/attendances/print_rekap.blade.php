<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi Guru - {{ $carbonMonth->locale('id')->isoFormat('MMMM YYYY') }} | NTO National Plus</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 10px;
            padding: 15px;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .logo-img {
            height: 48px;
            width: auto;
            object-contain: contain;
        }
        .title-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .title-section h1 {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #0f172a;
        }
        .title-section p {
            font-size: 11px;
            color: #475569;
            margin-top: 3px;
        }
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }
        .rekap-table th, .rekap-table td {
            border: 1px solid #94a3b8;
            padding: 4px 2px;
            text-align: center;
        }
        .rekap-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: 800;
        }
        .rekap-table td.name-col {
            text-align: left;
            padding-left: 6px;
            font-weight: 700;
            white-space: nowrap;
        }
        .rekap-table td.class-col {
            text-align: left;
            padding-left: 4px;
            color: #475569;
            white-space: nowrap;
        }
        .sunday-col {
            background-color: #fee2e2 !important;
            color: #b91c1c;
        }
        .badge-h { font-weight: 900; color: #065f46; }
        .badge-t { font-weight: 900; color: #92400e; }
        .badge-i { font-weight: 900; color: #075985; }
        .badge-s { font-weight: 900; color: #3730a3; }
        .badge-a { font-weight: 900; color: #9f1239; }

        .stat-card {
            border: 1px solid #cbd5e1;
            padding: 6px 12px;
            border-radius: 6px;
            display: inline-block;
            margin-right: 8px;
            font-size: 10px;
        }

        .signatures {
            margin-top: 25px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signatures table {
            width: 100%;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 10.5px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 6mm;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Non-Print Controls -->
    <div class="no-print" style="margin-bottom: 12px; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <strong>Laporan Rekapitulasi Presensi Guru (A4 Lanskap)</strong>
            <span style="color: #64748b; margin-left: 8px;">Periode: {{ $carbonMonth->locale('id')->isoFormat('MMMM YYYY') }} &bull; Total Guru: {{ count($teachers) }}</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" style="padding: 6px 16px; background: #0f172a; color: #fbbf24; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                🖨️ Cetak Rekapan (PDF)
            </button>
            <button onclick="window.close()" style="padding: 6px 12px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>

    <!-- Letterhead Kop Sekolah -->
    <table class="header-table">
        <tr>
            <td style="width: 60px; vertical-align: middle;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="logo-img" onerror="this.src='{{ asset('logo.png') }}'">
            </td>
            <td style="vertical-align: middle; padding-left: 10px;">
                <div style="font-size: 15px; font-weight: 900; color: #0f172a; letter-spacing: 0.5px;">NTO NATIONAL PLUS PRIMARY SCHOOL</div>
                <div style="font-size: 9px; color: #475569; margin-top: 2px;">Jl. Palapa No. 1, Kota Kupang, Nusa Tenggara Timur &bull; Telp: (0380) 123456</div>
                <div style="font-size: 8.5px; color: #64748b;">NPSN: 12345678 &bull; Status Akreditasi: A &bull; Email: info@nto-kupang.sch.id</div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <span style="display: inline-block; padding: 4px 8px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 9px; font-weight: bold;">
                    DOKUMEN RESMI
                </span>
            </td>
        </tr>
    </table>

    <!-- Title Section -->
    <div class="title-section">
        <h1>REKAPITULASI PRESENSI DEWAN GURU & STAF</h1>
        <p>Bulan: <strong>{{ $carbonMonth->locale('id')->isoFormat('MMMM YYYY') }}</strong> | Total Hari: <strong>{{ $daysInMonth }} Hari</strong></p>
    </div>

    <!-- Matrix Table -->
    <table class="rekap-table">
        <thead>
            <tr>
                <th style="width: 24px;">No</th>
                <th style="width: 140px; text-align: left; padding-left: 6px;">Nama Guru</th>
                <th style="width: 80px; text-align: left; padding-left: 4px;">Penugasan</th>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    @php
                        $curDate = \Carbon\Carbon::parse(sprintf('%s-%02d', $bulan, $d));
                        $isSunday = $curDate->isSunday();
                    @endphp
                    <th style="width: 18px;" class="{{ $isSunday ? 'sunday-col' : '' }}">
                        <div>{{ $d }}</div>
                        <div style="font-size: 7px; font-weight: normal;">{{ substr($curDate->locale('id')->minDayName, 0, 1) }}</div>
                    </th>
                @endfor
                <th style="width: 24px; background: #e2e8f0;">H</th>
                <th style="width: 24px; background: #fef3c7;">T</th>
                <th style="width: 24px; background: #e0f2fe;">I</th>
                <th style="width: 24px; background: #e0e7ff;">S</th>
                <th style="width: 24px; background: #ffe4e6;">A</th>
            </tr>
        </thead>
        <tbody>
            @forelse($teachers as $idx => $teacher)
                @php
                    $totals = $teacherTotals[$teacher->id] ?? ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
                @endphp
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td class="name-col">{{ $teacher->name }}</td>
                    <td class="class-col">{{ $teacher->getAssignedClass() ?? 'Guru Pengajar' }}</td>
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $dStr = sprintf('%s-%02d', $bulan, $d);
                            $curDate = \Carbon\Carbon::parse($dStr);
                            $isSunday = $curDate->isSunday();
                            $att = $matrix[$teacher->id][$dStr] ?? null;
                            $isLate = $att && $att->status === 'hadir' && str_contains(strtolower($att->keterangan ?? ''), 'terlambat');
                        @endphp
                        <td class="{{ $isSunday ? 'sunday-col' : '' }}">
                            @if(!$att)
                                <span style="color: #cbd5e1;">-</span>
                            @elseif($att->status === 'hadir')
                                @if($isLate)
                                    <span class="badge-t">T</span>
                                @else
                                    <span class="badge-h">H</span>
                                @endif
                            @elseif($att->status === 'izin')
                                <span class="badge-i">I</span>
                            @elseif($att->status === 'sakit')
                                <span class="badge-s">S</span>
                            @elseif($att->status === 'alpa')
                                <span class="badge-a">A</span>
                            @endif
                        </td>
                    @endfor
                    <td style="font-weight: 800; background: #f8fafc;">{{ $totals['hadir'] }}</td>
                    <td style="font-weight: 800; background: #fffbeb;">{{ $totals['terlambat'] }}</td>
                    <td style="font-weight: 800; background: #f0f9ff;">{{ $totals['izin'] }}</td>
                    <td style="font-weight: 800; background: #eef2ff;">{{ $totals['sakit'] }}</td>
                    <td style="font-weight: 800; background: #fff1f2;">{{ $totals['alpa'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $daysInMonth + 8 }}" style="padding: 20px; text-align: center; color: #94a3b8;">
                        Tidak ada data guru yang terdaftar.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Legend & Stats Summary -->
    <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 9px;">
        <div>
            <strong>Keterangan:</strong> 
            <span class="badge-h" style="margin-left: 5px;">H = Hadir Tepat Waktu</span> &bull; 
            <span class="badge-t" style="margin-left: 5px;">T = Terlambat</span> &bull; 
            <span class="badge-i" style="margin-left: 5px;">I = Izin</span> &bull; 
            <span class="badge-s" style="margin-left: 5px;">S = Sakit</span> &bull; 
            <span class="badge-a" style="margin-left: 5px;">A = Alpa (Tanpa Keterangan)</span>
        </div>
        <div>
            <span class="stat-card">Total Hadir: <strong>{{ $grandHadir }}</strong></span>
            <span class="stat-card">Total Terlambat: <strong>{{ $grandTerlambat }}</strong></span>
            <span class="stat-card">Total Izin: <strong>{{ $grandIzin }}</strong></span>
            <span class="stat-card">Total Sakit: <strong>{{ $grandSakit }}</strong></span>
            <span class="stat-card">Total Alpa: <strong>{{ $grandAlpa }}</strong></span>
        </div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <table>
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <p style="font-weight: bold; margin-top: 2px;">Kepala Sekolah NTO National Plus</p>
                    <div style="height: 50px;"></div>
                    <p style="font-weight: 800; text-decoration: underline;">( .................................................... )</p>
                    <p style="color: #64748b; font-size: 9px; margin-top: 2px;">NIP. -</p>
                </td>
                <td>
                    <p>Kupang, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</p>
                    <p style="font-weight: bold; margin-top: 2px;">Administrator / Tata Usaha</p>
                    <div style="height: 50px;"></div>
                    <p style="font-weight: 800; text-decoration: underline;">{{ auth()->user()->name }}</p>
                    <p style="color: #64748b; font-size: 9px; margin-top: 2px;">Petugas Presensi</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
