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
            width: 50%;
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
            <span style="color: #64748b; margin-left: 8px;">Kelas: {{ $kelas }} | Bulan: {{ \Carbon\Carbon::parse($bulan.'-01')->isoFormat('MMMM YYYY') }}</span>
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
                    <img src="{{ asset('images/logo.png') }}" alt="ESCS Logo" class="logo-img" onerror="this.src='{{ asset('logo.png') }}'">
                    <div>
                        <strong style="font-size: 13px; color: #0f172a; display: block;">ESCS</strong>
                        <span style="font-size: 10px; color: #475569;">Kota Kupang</span>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Title Section -->
    <div class="title-section">
        <h1>REKAP ABSENSI SISWA</h1>
        <p>Bulan: <strong>{{ \Carbon\Carbon::parse($bulan.'-01')->isoFormat('MMMM YYYY') }}</strong> | Kelas: <strong>{{ $kelas }}</strong></p>
        <p>Hari Efektif: <strong>{{ count($effectiveDays) }} hari</strong></p>
    </div>

    <!-- Rekap Main Table -->
    <table class="rekap-table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 170px;">Nama</th>
                @foreach($effectiveDays as $day)
                    <th style="width: 20px;">{{ $day }}</th>
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
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="student-name">{{ $student->nama }}</td>
                    @foreach($effectiveDays as $day)
                        @php
                            $att = $attMap->get($day);
                            $code = '-';
                            if ($att) {
                                switch ($att->status) {
                                    case 'hadir':
                                        $code = 'H';
                                        $hCount++;
                                        break;
                                    case 'alpa':
                                        $code = 'A';
                                        $aCount++;
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
                        <td>{{ $code }}</td>
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
                    <td colspan="{{ count($effectiveDays) + 8 }}">Tidak ada data siswa untuk kelas ini.</td>
                </tr>
            @endforelse
            
            <!-- Total Kelas Footer Row -->
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="{{ count($effectiveDays) + 2 }}" style="text-align: center; font-weight: 800;">TOTAL KELAS</td>
                <td>{{ $totalHadir }}</td>
                <td>{{ $totalAlpa }}</td>
                <td>{{ $totalSakit }}</td>
                <td>{{ $totalIzin }}</td>
                <td>{{ $totalLibur }}</td>
                <td>0</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Box -->
    <div class="summary-box">
        <strong>Ringkasan Kelas {{ $kelas }}:</strong><br>
        Total Hadir: {{ $totalHadir }} | Alpa: {{ $totalAlpa }} | Sakit: {{ $totalSakit }} | Izin: {{ $totalIzin }} | Libur: {{ $totalLibur }} | Terlambat: 0
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>Kepala Sekolah</strong>
                    <div class="signature-space"></div>
                    ___________________________
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($bulan.'-01')->isoFormat('MMMM YYYY') }},<br>
                    <strong>Wali Kelas</strong>
                    <div class="signature-space"></div>
                    <strong>{{ $teacherName }}</strong>
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
