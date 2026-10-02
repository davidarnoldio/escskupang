<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Data Alumni - {{ $tahunLulus ? 'Angkatan ' . $tahunLulus : 'Seluruh Angkatan' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
            margin: 0;
            padding: 15px;
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
            margin-bottom: 15px;
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
        table.rekap-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.rekap-table th, table.rekap-table td {
            border: 1px solid #334155;
            padding: 5px 6px;
            font-size: 10px;
        }
        table.rekap-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        table.rekap-table td.center {
            text-align: center;
        }
        table.rekap-table td.name {
            font-weight: 600;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            page-break-inside: avoid;
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
    <div class="no-print" style="margin-bottom: 15px; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <strong style="color: #0f172a;">Buku Rekapitulasi Data Alumni Siswa (Lanskap A4)</strong>
            <span style="color: #64748b; margin-left: 8px;">
                Tahun Kelulusan: <strong>{{ $tahunLulus ?: 'Semua Angkatan' }}</strong> | Total: <strong>{{ $alumni->count() }} Alumni</strong>
            </span>
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

    <!-- Header Logo & School Identity -->
    <table class="header-table">
        <tr>
            <td style="width: 250px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="{{ asset('images/logo.png') }}" alt="NTO Logo" class="logo-img" onerror="this.src='{{ asset('logo.png') }}'">
                    <div>
                        <strong style="font-size: 13px; color: #0f172a; display: block;">NTO National Plus</strong>
                        <span style="font-size: 10px; color: #475569;">Excellent Spirit Christian School Kupang</span>
                    </div>
                </div>
            </td>
            <td style="text-align: right; font-size: 10px; color: #64748b;">
                Jl. Frans Seda, Kota Kupang, Nusa Tenggara Timur<br>
                Sistem Informasi & Presensi Terpadu Sekolah
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <!-- Title Section -->
    <div class="title-section">
        <h1>BUKU REKAPITULASI DATA ALUMNI SISWA</h1>
        <p>
            Tahun Kelulusan / Angkatan: <strong>{{ $tahunLulus ? 'Angkatan ' . $tahunLulus : 'Semua Angkatan' }}</strong>
            @if($kelas)
                | Kelas Terakhir: <strong>Kelas {{ $kelas }}</strong>
            @endif
            | Jumlah: <strong>{{ $alumni->count() }} Orang</strong>
        </p>
    </div>

    <!-- Main Table -->
    <table class="rekap-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">NISN</th>
                <th style="width: 150px; text-align: left; padding-left: 6px;">Nama Siswa Alumni</th>
                <th style="width: 30px;">L/P</th>
                <th style="width: 110px;">Tempat, Tanggal Lahir</th>
                <th style="width: 65px;">Kelas Terakhir</th>
                <th style="width: 70px;">Tahun Lulus</th>
                <th style="width: 110px;">No. Ijazah / SKL</th>
                <th style="width: 130px;">Sekolah Lanjutan</th>
                <th>Prestasi / Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($alumni as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center" style="font-family: monospace; font-weight: bold;">
                        {{ $item->nisn ?? $item->nis }}
                    </td>
                    <td class="name">
                        {{ $item->nama }}
                        @if($item->is_abk)
                            <span style="font-size: 8px; padding: 1px 3px; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 2px;">ABK</span>
                        @endif
                    </td>
                    <td class="center">{{ $item->jenis_kelamin == 'Laki-laki' ? 'L' : ($item->jenis_kelamin == 'Perempuan' ? 'P' : '-') }}</td>
                    <td>
                        {{ $item->tempat_lahir ? $item->tempat_lahir . ', ' : '' }}
                        {{ $item->tanggal_lahir ? $item->tanggal_lahir->format('d/m/Y') : '-' }}
                    </td>
                    <td class="center">{{ $item->kelas }}</td>
                    <td class="center" style="font-weight: bold;">{{ $item->tahun_lulus ?: '-' }}</td>
                    <td class="center">{{ $item->no_ijazah ?: '-' }}</td>
                    <td>{{ $item->sekolah_lanjutan ?: '-' }}</td>
                    <td style="font-size: 9px;">
                        @if($item->kategori_prestasi && $item->kategori_prestasi !== 'Tidak Ada')
                            <strong>[{{ $item->kategori_prestasi }}]</strong> {{ $item->keterangan_prestasi }}
                        @else
                            {{ $item->catatan_kelulusan ?: '-' }}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center" style="padding: 15px; font-style: italic; color: #64748b;">
                        Tidak ada data alumni untuk kriteria filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Section -->
    <table class="signature-table">
        <tr>
            <td></td>
            <td>
                Kupang, {{ $tanggalCetak }}<br>
                <strong>Mengetahui,<br>Kepala Sekolah NTO National Plus</strong>
                <div class="signature-space"></div>
                <strong style="text-decoration: underline;">( .................................................... )</strong><br>
                <span>NIP / NIY: .......................................</span>
            </td>
        </tr>
    </table>

</body>
</html>
