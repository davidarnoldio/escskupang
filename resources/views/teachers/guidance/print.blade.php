<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Bimbingan & Jurnal Observasi Siswa - Kelas {{ $selectedClass }} ({{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('id')->isoFormat('MMMM Y') }})</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 15mm 15mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #000000;
            background: #ffffff;
            padding: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .logo-img {
            height: 65px;
            width: auto;
        }
        .school-title {
            text-align: center;
        }
        .school-title h2 {
            margin: 0;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-title h1 {
            margin: 1px 0;
            font-size: 15pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #991b1b;
        }
        .school-title p {
            margin: 0;
            font-size: 8.5pt;
            font-family: Arial, sans-serif;
            color: #334155;
        }
        .double-divider {
            border-top: 2.5px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin: 6px 0 14px 0;
        }
        .document-title {
            text-align: center;
            margin-bottom: 12px;
        }
        .document-title h3 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }
        .document-meta {
            margin-top: 4px;
            font-size: 10pt;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #666;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .section-heading {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 14px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            border-left: 4px solid #000;
            padding-left: 6px;
        }
        table.formal-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 10px;
        }
        table.formal-table th, table.formal-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            vertical-align: top;
        }
        table.formal-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .cat-badge {
            font-weight: bold;
            display: inline-block;
            text-align: center;
            width: 100%;
        }
        .cat-pedoman {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 12px;
        }
        .cat-pedoman td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }
        .cat-pedoman td.code {
            width: 32px;
            text-align: center;
            font-weight: bold;
            background: #f2f2f2;
        }
        .cat-pedoman td.title {
            width: 140px;
            font-weight: bold;
        }
        .cat-pedoman td.desc {
            color: #222;
        }
        .cat-notes-box {
            border: 1px solid #000;
            padding: 8px 10px;
            font-size: 9pt;
            margin-bottom: 12px;
            background-color: #ffffff;
        }
        .cat-notes-item {
            margin-bottom: 8px;
        }
        .cat-notes-item:last-child {
            margin-bottom: 0;
        }
        .cat-notes-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
            display: block;
        }
        .cat-notes-content {
            padding-left: 14px;
            white-space: pre-wrap;
            color: #111;
        }
        .signature-section {
            margin-top: 18px;
            width: 100%;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 10pt;
        }
        .signature-space {
            height: 60px;
        }

        /* Non-print action bar */
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

    <!-- Non-Print Toolbar -->
    <div class="no-print" style="margin-bottom: 18px; padding: 10px 16px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; font-family: Arial, sans-serif; font-size: 13px;">
        <div>
            <strong style="color: #0f172a;">Pratinjau Format Cetak A4: Buku Bimbingan Siswa</strong>
            <span style="color: #64748b; margin-left: 8px;">Kelas: <strong>{{ $selectedClass }}</strong> | Bulan: <strong>{{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('id')->isoFormat('MMMM Y') }}</strong></span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" style="padding: 7px 18px; background: #0f172a; color: #fbbf24; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                🖨️ Cetak Dokumen (PDF)
            </button>
            <button onclick="window.close()" style="padding: 7px 14px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>

    <!-- Kop Surat Sekolah Resmi -->
    <table class="header-table">
        <tr>
            <td style="width: 80px; text-align: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo NTO" class="logo-img" onerror="this.src='{{ asset('logo.png') }}'">
            </td>
            <td class="school-title">
                <h2>YAYASAN EXCELLENT SPIRIT CHRISTIAN</h2>
                <h1>EXCELLENT SPIRIT CHRISTIAN SCHOOL (NTO)</h1>
                <p>NTO NATIONAL PLUS PRIMARY SCHOOL KUPANG</p>
                <p>Jl. Frans Seda, Kota Kupang, Nusa Tenggara Timur &bull; Telp: (0380) 823456</p>
            </td>
        </tr>
    </table>

    <div class="double-divider"></div>

    <!-- Judul Dokumen & Metadata -->
    <div class="document-title">
        <h3>BUKU BIMBINGAN & JURNAL OBSERVASI SISWA</h3>
    </div>

    <div class="document-meta">
        <div>KELAS : <strong>KELAS {{ $selectedClass }}</strong></div>
        <div>BULAN / TAHUN : <strong>{{ \Carbon\Carbon::parse($selectedMonth . '-01')->locale('id')->isoFormat('MMMM Y') }}</strong></div>
        <div>WALI KELAS : <strong>{{ $waliKelas->name ?? '-' }}</strong></div>
    </div>

    <!-- BAGIAN A: JURNAL OBSERVASI HARIAN -->
    <div class="section-heading">BAGIAN A: JURNAL OBSERVASI HARIAN</div>
    <table class="formal-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 60px;">Tanggal</th>
                <th style="width: 105px;">Nama Siswa</th>
                <th style="width: 125px;">Perilaku / Kejadian</th>
                <th style="width: 35px;">Kat.</th>
                <th style="width: 120px;">Identifikasi Akar Masalah</th>
                <th style="width: 120px;">Pendekatan Wali Kelas</th>
                <th style="width: 110px;">Komitmen Siswa</th>
                <th style="width: 110px;">Tindak Lanjut</th>
                <th style="width: 35px;">Paraf</th>
            </tr>
        </thead>
        <tbody>
            @forelse($journals as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td style="text-align: center; white-space: nowrap;">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                    <td style="font-weight: bold;">{{ $item->student->nama ?? '-' }}</td>
                    <td>{{ $item->perilaku_kejadian }}</td>
                    <td style="text-align: center; font-weight: bold;">
                        <span class="cat-badge">{{ $item->kategori }}</span>
                    </td>
                    <td>{{ $item->identifikasi_akar_masalah ?? '-' }}</td>
                    <td>{{ $item->pendekatan_wali_kelas ?? '-' }}</td>
                    <td>{{ $item->komitmen_siswa ?? '-' }}</td>
                    <td>{{ $item->tindak_lanjut ?? '-' }}</td>
                    <td style="text-align: center; font-size: 7.5pt; color: #555;">
                        {{ $item->paraf ?: '✓' }}
                    </td>
                </tr>
            @empty
                @for($k = 1; $k <= 4; $k++)
                    <tr style="height: 28px;">
                        <td style="text-align: center; color: #999;">{{ $k }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            @endforelse
        </tbody>
    </table>

    <!-- BAGIAN B: KETERANGAN KATEGORI PERILAKU -->
    <div class="section-heading">BAGIAN B: KETERANGAN KATEGORI PERILAKU (STANDAR PEDOMAN)</div>
    <table class="cat-pedoman">
        <tr>
            <td class="code">A</td>
            <td class="title">Akademik</td>
            <td class="desc">Tugas tidak selesai, penurunan nilai drastis, lambat memahami materi, butuh remedi, dll.</td>
        </tr>
        <tr>
            <td class="code">S</td>
            <td class="title">Sosial-Emosional</td>
            <td class="desc">Murung/menarik diri, konflik dengan teman, cemas, tantrum/marah berlebihan, emosi labil.</td>
        </tr>
        <tr>
            <td class="code">D</td>
            <td class="title">Kedisiplinan</td>
            <td class="desc">Sering terlambat, tidak memakai atribut seragam lengkap, bermain HP/gadget di kelas, tidak tertib.</td>
        </tr>
        <tr>
            <td class="code">P</td>
            <td class="title">Potensi / Prestasi</td>
            <td class="desc">Menunjukkan bakat istimewa, inisiatif kepemimpinan, aktif membantu teman, konsisten berprestasi.</td>
        </tr>
    </table>

    <!-- BAGIAN C: CATATAN UMUM WALI KELAS -->
    <div class="section-heading">BAGIAN C: CATATAN UMUM WALI KELAS</div>
    <div class="cat-notes-box">
        <div class="cat-notes-item">
            <span class="cat-notes-title">1. Kondisi Umum Kelas :</span>
            <div class="cat-notes-content">{{ $monthlyRecap->kondisi_umum_kelas ?? '(Belum ada catatan kondisi umum kelas)' }}</div>
        </div>
        <div class="cat-notes-item">
            <span class="cat-notes-title">2. Siswa yang Memerlukan Perhatian Khusus :</span>
            <div class="cat-notes-content">{{ $monthlyRecap->siswa_perhatian_khusus ?? '(Tidak ada siswa yang memerlukan perhatian khusus)' }}</div>
        </div>
        <div class="cat-notes-item">
            <span class="cat-notes-title">3. Tindak Lanjut dengan Guru Mata Pelajaran / Orang Tua :</span>
            <div class="cat-notes-content">{{ $monthlyRecap->tindak_lanjut_ortu_guru ?? '(Tidak ada rencana tindak lanjut khusus)' }}</div>
        </div>
        <div class="cat-notes-item">
            <span class="cat-notes-title">4. Rekomendasi Pembinaan Berikutnya :</span>
            <div class="cat-notes-content">{{ $monthlyRecap->rekomendasi_berikutnya ?? '(Pertahankan ritme pembinaan dan kedisiplinan)' }}</div>
        </div>
    </div>

    <!-- BAGIAN D: REKAP SINGKAT BULANAN & PENGESAHAN -->
    <div class="section-heading">BAGIAN D: REKAP SINGKAT BULANAN & PENGESAHAN</div>
    <table class="formal-table">
        <thead>
            <tr>
                <th style="width: 200px;">Kategori Perilaku</th>
                <th style="width: 140px;">Jumlah Kasus / Temuan</th>
                <th>Keterangan Singkat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>A - Akademik</strong></td>
                <td style="text-align: center; font-weight: bold;">{{ $countA }} Kasus</td>
                <td>{{ $monthlyRecap->ket_akademik ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>S - Sosial-Emosional</strong></td>
                <td style="text-align: center; font-weight: bold;">{{ $countS }} Kasus</td>
                <td>{{ $monthlyRecap->ket_sosial_emosional ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>D - Kedisiplinan</strong></td>
                <td style="text-align: center; font-weight: bold;">{{ $countD }} Kasus</td>
                <td>{{ $monthlyRecap->ket_kedisiplinan ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>P - Potensi / Prestasi</strong></td>
                <td style="text-align: center; font-weight: bold;">{{ $countP }} Kasus</td>
                <td>{{ $monthlyRecap->ket_potensi_prestasi ?? '-' }}</td>
            </tr>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td style="text-align: right; padding-right: 10px;">TOTAL OBSERVASI</td>
                <td style="text-align: center;">{{ $countA + $countS + $countD + $countP }} Temuan</td>
                <td style="font-size: 8pt; color: #555;">Dokumentasi observasi pembinaan siswa aktif</td>
            </tr>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN (TANDA TANGAN) -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="text-align: left; padding-left: 20px;">
                    <p>Mengetahui,</p>
                    <p style="font-weight: bold; margin-top: 2px;">Kepala Sekolah ESCS / NTO Kupang</p>
                    <div class="signature-space"></div>
                    <p style="font-weight: bold; text-decoration: underline;">( .................................................... )</p>
                    <p style="font-size: 9pt; margin-top: 2px;">NIP. -</p>
                </td>
                <td style="text-align: left; padding-left: 60px;">
                    <p>{{ $tanggalCetak }}</p>
                    <p style="font-weight: bold; margin-top: 2px;">Wali Kelas {{ $selectedClass }}</p>
                    <div class="signature-space"></div>
                    <p style="font-weight: bold; text-decoration: underline;">{{ $waliKelas->name ?? 'Wali Kelas' }}</p>
                    <p style="font-size: 9pt; margin-top: 2px;">NIP/NUPTK. {{ $waliKelas->nip ?? '-' }}</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
