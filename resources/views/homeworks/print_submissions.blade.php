<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai Tugas - {{ $homework->judul }} - {{ $homework->kelas }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
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
            height: 48px;
        }
        .divider {
            border-bottom: 2px solid #0f172a;
            margin-bottom: 12px;
        }
        .title-section {
            text-align: center;
            margin-bottom: 14px;
        }
        .title-section h1 {
            font-size: 14px;
            font-weight: 800;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .title-section p {
            font-size: 11px;
            margin: 2px 0;
            color: #475569;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        .meta-grid td {
            padding: 6px 10px;
            font-size: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .meta-grid tr:last-child td {
            border-bottom: none;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 14px;
            font-size: 10px;
            background-color: #f0fdf4;
            color: #166534;
        }
        table.rekap-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.rekap-table th, table.rekap-table td {
            border: 1px solid #334155;
            padding: 6px 6px;
            text-align: center;
            font-size: 10px;
        }
        table.rekap-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }
        table.rekap-table td.student-name {
            text-align: left;
            padding-left: 8px;
            font-weight: 600;
        }
        table.rekap-table td.cell-kumpul {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        table.rekap-table td.cell-belum {
            background-color: #ffe4e6 !important;
            color: #9f1239 !important;
            font-weight: bold;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        table.rekap-table td.grade-cell {
            font-weight: 800;
            font-size: 11px;
        }
        .signature-section {
            width: 100%;
            margin-top: 30px;
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
            height: 60px;
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
    <div class="no-print" style="margin-bottom: 15px; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <strong>Cetak Rekap Nilai Tugas / PR (A4 Portrait)</strong>
            <span style="color: #64748b; margin-left: 8px;">Tugas: {{ $homework->judul }} | Kelas: {{ $homework->kelas }}</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" style="padding: 6px 16px; background: #dc2626; color: #ffffff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 12px;">
                🖨️ Cetak Rekapan (PDF)
            </button>
            <button onclick="window.close()" style="padding: 6px 12px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 12px;">
                Tutup
            </button>
        </div>
    </div>

    <!-- Header Logo & School Name -->
    <table class="header-table">
        <tr>
            <td style="width: 220px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="{{ asset('images/logo.png') }}" alt="NTO Logo" class="logo-img" onerror="this.src='{{ asset('logo.png') }}'">
                    <div>
                        <strong style="font-size: 14px; color: #0f172a; display: block; font-weight: 800;">NTO National Plus</strong>
                        <span style="font-size: 10px; color: #475569; font-weight: 600;">Primary School</span>
                    </div>
                </div>
            </td>
            <td style="text-align: right; font-size: 9px; color: #64748b;">
                Tanggal Cetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Title Section -->
    <div class="title-section">
        <h1>REKAPITULASI NILAI TUGAS / PR SISWA</h1>
        <p>Kelas: <strong>{{ $homework->kelas }}</strong> | Mata Pelajaran: <strong>{{ $homework->mata_pelajaran }}</strong></p>
    </div>

    <!-- Metadata Grid -->
    <table class="meta-grid">
        <tr>
            <td style="width: 15%; font-weight: bold; color: #334155;">Judul Tugas</td>
            <td style="width: 35%;">: {{ $homework->judul }}</td>
            <td style="width: 15%; font-weight: bold; color: #334155;">Batas Pengumpulan</td>
            <td style="width: 35%;">: {{ $homework->deadline ? $homework->deadline->format('d/m/Y H:i') : '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; color: #334155;">Deskripsi Tugas</td>
            <td>: {{ $homework->deskripsi }}</td>
            <td style="font-weight: bold; color: #334155;">Target Penerima</td>
            <td>: {{ $homework->student ? $homework->student->nama . ' (Siswa Spesifik)' : 'Seluruh Siswa Kelas ' . $homework->kelas }}</td>
        </tr>
    </table>

    <!-- Statistics Summary Box -->
    <div class="summary-box">
        <div><strong>Total Siswa:</strong> {{ $recap['total_siswa'] }}</div>
        <div><strong>Sudah Kumpul:</strong> {{ $recap['sudah_kumpul'] }}</div>
        <div><strong>Belum Kumpul:</strong> {{ $recap['belum_kumpul'] }}</div>
        <div><strong>Sudah Dinilai:</strong> {{ $recap['sudah_dinilai'] }}</div>
        <div><strong>Rata-Rata Kelas:</strong> {{ $recap['rata_rata'] }}</div>
        <div><strong>Nilai Max:</strong> {{ $recap['nilai_tertinggi'] }}</div>
        <div><strong>Nilai Min:</strong> {{ $recap['nilai_terendah'] }}</div>
    </div>

    <!-- Rekap Grade Main Table -->
    <table class="rekap-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th style="width: 70px;">NIS</th>
                <th style="width: 180px;">Nama Siswa</th>
                <th style="width: 90px;">Status Kumpul</th>
                <th style="width: 110px;">Waktu Kumpul</th>
                <th style="width: 60px;">Nilai</th>
                <th>Catatan / Feedback Guru</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                @php
                    $sub = $submissions->get($student->id);
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: 600; color: #475569;">{{ $student->nis }}</td>
                    <td class="student-name">{{ $student->nama }}</td>
                    <td class="{{ $sub ? 'cell-kumpul' : 'cell-belum' }}">
                        {{ $sub ? 'Sudah Kumpul' : 'Belum Kumpul' }}
                    </td>
                    <td style="color: #475569;">
                        {{ $sub ? $sub->submitted_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="grade-cell">
                        @if($sub && $sub->nilai !== null)
                            <span style="color: {{ $sub->nilai >= 75 ? '#166534' : ($sub->nilai >= 60 ? '#854d0e' : '#9f1239') }};">
                                {{ $sub->nilai }}
                            </span>
                        @else
                            <span style="color: #94a3b8; font-weight: normal; font-size: 9px;">-</span>
                        @endif
                    </td>
                    <td style="text-align: left; padding-left: 6px; color: #334155; font-size: 9.5px;">
                        {{ $sub && $sub->catatan_guru ? $sub->catatan_guru : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data siswa untuk kelas {{ $homework->kelas }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
                    Kupang, {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}<br>
                    <strong>Wali Kelas / Guru Pengajar</strong>
                    <div class="signature-space"></div>
                    <strong>{{ $teacherName }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <script>
        // Auto trigger print if opened directly with autoprint=1
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('autoprint') === '1') {
                setTimeout(() => window.print(), 500);
            }
        });
    </script>
</body>
</html>
