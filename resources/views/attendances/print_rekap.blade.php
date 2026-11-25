<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Siswa - {{ $kelas }} - {{ $bulan }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 10px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-img {
            height: 45px;
        }
        .divider {
            border-bottom: 2px solid #0f172a;
            margin-bottom: 12px;
        }
        .title-section {
            text-align: center;
            margin-bottom: 12px;
        }
        .title-section h1 {
            font-size: 13px;
            font-weight: 800;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .title-section p {
            font-size: 11px;
            margin: 2px 0;
        }
        table.rekap-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.rekap-table th, table.rekap-table td {
            border: 1px solid #334155;
            padding: 4px 3px;
            text-align: center;
            font-size: 10px;
        }
        table.rekap-table th {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        table.rekap-table td.student-name {
            text-align: left;
            padding-left: 6px;
            font-weight: 600;
            white-space: nowrap;
        }
        table.rekap-table td.cell-hadir {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            font-weight: 800 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        table.rekap-table td.cell-terlambat {
            background-color: #fef08a !important;
            color: #854d0e !important;
            font-weight: 800 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        table.rekap-table td.cell-alpa {
            background-color: #ffe4e6 !important;
            color: #9f1239 !important;
            font-weight: 800 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .summary-box {
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 30px;
            font-size: 10px;
            background-color: #f8fafc;
        }
        .signature-section {
            width: 100%;
            margin-top: 20px;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            font-size: 11px;
        }
        .signature-space {
            height: 65px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar for Non-Print View -->
    <div class="no-print" style="margin-bottom: 15px; padding: 10px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <strong>Cetak Rekap Absensi Siswa (Lanskap A4)</strong>
            <span style="color: #64748b; margin-left: 8px;">Kelas: {{ $displayKelas }} | Bulan: {{ \Carbon\Carbon::parse($bulan.'-01')->isoFormat('MMMM YYYY') }}</span>
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

    <!-- Header Logo & School Name -->
    <table class="header-table">
        <tr>
            <td style="width: 200px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="{{ asset('images/logo.png') }}?v={{ time() }}" alt="NTO Logo" class="logo-img" onerror="this.src='{{ asset('logo.png') }}?v={{ time() }}'">
                    <div>
                        <strong style="font-size: 13px; color: #0f172a; display: block;">NTO National Plus</strong>
                        <span style="font-size: 10px; color: #475569;">Primary School</span>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Title Section -->
    <div class="title-section">
        <h1>REKAP ABSENSI SISWA (5 HARI KERJA: SENIN - JUMAT)</h1>
        <p>Bulan: <strong>{{ \Carbon\Carbon::parse($bulan.'-01')->isoFormat('MMMM YYYY') }}</strong> | Kelas: <strong>{{ $displayKelas }}</strong></p>
        <p>Hari Efektif: <strong>{{ count($effectiveDays) }} hari</strong></p>
    </div>

    <!-- Rekap Main Table -->
    <table class="rekap-table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 155px;">Nama</th>
                @if(empty($kelas))
                    <th style="width: 70px;">Kelas</th>
                @endif
                @foreach($effectiveDays as $day)
                    @php
                        $curDate = \Carbon\Carbon::parse(sprintf('%s-%02d', $bulan, $day));
                    @endphp
                    <th style="width: 30px;">
                        <div>{{ $day }}</div>
                        <div style="font-size: 7px; font-weight: normal;">{{ substr($curDate->locale('id')->minDayName, 0, 2) }}</div>
                    </th>
                @endforeach
                <th style="width: 22px;">H</th>
                <th style="width: 22px;">A</th>
                <th style="width: 22px;">S</th>
                <th style="width: 22px;">I</th>
                <th style="width: 22px;">L</th>
                <th style="width: 24px;">TL</th>
                <th style="width: 45px;">%H</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                @php
                    $attMap = $student->attendances->keyBy(function ($att) {
                        return (int) \Carbon\Carbon::parse($att->tanggal)->format('j');
                    });

                    $hCount = 0;
                    $aCount = 0;
                    $sCount = 0;
                    $iCount = 0;
                    $lCount = 0;
                    $tlCount = 0;
                    $targetTerlambat = $student->is_abk ? $jamTerlambatAbk : $jamTerlambatReguler;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="student-name">{{ $student->nama }}</td>
                    @if(empty($kelas))
                        <td style="font-weight: 600;">{{ $student->kelas }}</td>
                    @endif
                    @foreach($effectiveDays as $day)
                        @php
                            $att = $attMap->get($day);
                            $code = '-';
                            $cellClass = '';
                            $jamIn = ($att && $att->jam_masuk) ? substr($att->jam_masuk, 0, 5) : null;
                            $jamOut = ($att && $att->jam_pulang) ? substr($att->jam_pulang, 0, 5) : null;
                            if ($att) {
                                switch ($att->status) {
                                    case 'hadir':
                                        if (!$student->is_abk && (($jamIn && $jamIn > $targetTerlambat) || str_contains(strtolower($att->keterangan ?? ''), 'terlambat'))) {
                                            $code = 'T';
                                            $tlCount++;
                                            $cellClass = 'cell-terlambat';
                                        } else {
                                            $code = 'H';
                                            $hCount++;
                                            $cellClass = 'cell-hadir';
                                        }
                                        break;
                                    case 'alpa':
                                        $code = 'A';
                                        $aCount++;
                                        $cellClass = 'cell-alpa';
                                        break;
                                    case 'sakit':
                                        $code = 'S';
                                        $sCount++;
                                        break;
                                    case 'izin':
                                        $code = 'I';
                                        $iCount++;
                                        break;
                                    case 'libur':
                                        $code = 'L';
                                        $lCount++;
                                        break;
                                }
                            }
                        @endphp
                        <td class="{{ $cellClass }}" style="line-height: 1.15;">
                            <div>{{ $code }}</div>
                            @if($jamIn || $jamOut)
                                <div style="font-size: 6.5px; font-weight: normal; font-family: monospace;">{{ $jamIn ?? '--:--' }}</div>
                                <div style="font-size: 6.5px; font-weight: normal; font-family: monospace;">{{ $jamOut ?? '--:--' }}</div>
                            @endif
                        </td>
                    @endforeach
                    @php
                        $totEff = count($effectiveDays);
                        $pct = $totEff > 0 ? round(($hCount / $totEff) * 100, 1) : 0;
                    @endphp
                    <td><strong>{{ $hCount }}</strong></td>
                    <td><strong>{{ $aCount }}</strong></td>
                    <td><strong>{{ $sCount }}</strong></td>
                    <td><strong>{{ $iCount }}</strong></td>
                    <td><strong>{{ $lCount }}</strong></td>
                    <td><strong>{{ $tlCount }}</strong></td>
                    <td><strong>{{ number_format($pct, 1) }}%</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($effectiveDays) + (empty($kelas) ? 10 : 9) }}">Tidak ada data siswa untuk {{ $displayKelas }}.</td>
                </tr>
            @endforelse
            
            <!-- Total Kelas Footer Row -->
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="{{ count($effectiveDays) + (empty($kelas) ? 3 : 2) }}" style="text-align: center; font-weight: 800;">TOTAL KELAS</td>
                <td>{{ $totalHadir }}</td>
                <td>{{ $totalAlpa }}</td>
                <td>{{ $totalSakit }}</td>
                <td>{{ $totalIzin }}</td>
                <td>{{ $totalLibur }}</td>
                <td>{{ $totalTerlambat }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Box -->
    <div class="summary-box">
        <strong>Ringkasan {{ $displayKelas }}:</strong><br>
        Total Hadir: {{ $totalHadir }} | Alpa: {{ $totalAlpa }} | Sakit: {{ $totalSakit }} | Izin: {{ $totalIzin }} | Libur: {{ $totalLibur }} | Terlambat: {{ $totalTerlambat }}
    </div>

    <!-- Detailed Check-in / Check-out Log Table for Print -->
    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin: 0 0 6px 0; color: #0f172a;">
            Log Detail Jam Masuk &amp; Jam Pulang Siswa
        </h3>
        <table class="rekap-table" style="margin-bottom: 0;">
            <thead>
                <tr>
                    <th style="width: 26px;">No</th>
                    <th style="width: 115px;">Tanggal</th>
                    <th style="width: 160px;">Nama Siswa</th>
                    <th style="width: 70px;">Kelas</th>
                    <th style="width: 70px;">Jam Masuk</th>
                    <th style="width: 70px;">Jam Pulang</th>
                    <th style="width: 90px;">Status</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detailLogs as $idx => $log)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->tanggal)->locale('id')->isoFormat('ddd, D MMM Y') }}</td>
                        <td class="student-name">{{ $log->student->nama }}</td>
                        <td>{{ $log->student->formatted_kelas }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $log->jam_masuk ?? '-' }}</td>
                        <td style="font-family: monospace; font-weight: bold;">{{ $log->jam_pulang ?? '-' }}</td>
                        <td style="font-weight: bold; text-transform: uppercase;">
                            {{ $log->is_late ? 'TERLAMBAT' : strtoupper($log->status) }}{{ $log->is_early_out ? ' / PULANG AWAL' : '' }}
                        </td>
                        <td style="text-align: left; padding-left: 5px;">{{ $log->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Belum ada catatan jam masuk &amp; pulang pada bulan ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala Sekolah NTO National Plus</strong>
                    <div class="signature-space"></div>
                    <strong style="text-decoration: underline;">Beatrice Kianindra, B.Art</strong>
                </td>
                <td>
                    <br>
                    <strong>Administrator Sekolah</strong>
                    <div class="signature-space"></div>
                    <strong style="text-decoration: underline;">Apris Leky Manu, S.T</strong>
                </td>
                <td>
                    Kupang, {{ \Carbon\Carbon::parse($bulan.'-01')->isoFormat('MMMM YYYY') }}<br>
                    <strong>Wali Kelas / Guru Pengajar</strong>
                    <div class="signature-space"></div>
                    <strong style="text-decoration: underline;">{{ $teacherName }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Auto trigger print if opened directly for printing
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(() => window.print(), 500);
            }
        });
    </script>
</body>
</html>
