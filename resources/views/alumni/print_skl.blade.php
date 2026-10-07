<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Lulus - {{ $student->nama }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000000;
            background: #ffffff;
            margin: 0;
            padding: 20px;
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
            height: 75px;
            width: auto;
        }
        .school-title {
            text-align: center;
        }
        .school-title h2 {
            margin: 0;
            font-size: 15pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .school-title h1 {
            margin: 2px 0;
            font-size: 17pt;
            font-weight: 900;
            text-transform: uppercase;
            color: #991b1b;
        }
        .school-title p {
            margin: 0;
            font-size: 9.5pt;
            font-family: Arial, sans-serif;
            color: #334155;
        }
        .double-divider {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin: 8px 0 20px 0;
        }
        .letter-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .letter-title h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .letter-title p {
            margin: 2px 0 0 0;
            font-size: 11pt;
        }
        .content {
            text-align: justify;
        }
        .content p {
            margin: 0 0 10px 0;
            text-indent: 30px;
        }
        .bio-table {
            width: 100%;
            margin: 12px 0 16px 25px;
            border-collapse: collapse;
        }
        .bio-table td {
            padding: 3px 0;
            font-size: 11.5pt;
            vertical-align: top;
        }
        .bio-table td.label {
            width: 200px;
        }
        .bio-table td.colon {
            width: 15px;
            text-align: center;
        }
        .bio-table td.value {
            font-weight: bold;
        }
        .decision-box {
            text-align: center;
            margin: 18px 0;
            padding: 10px;
            border: 2px solid #000;
            background-color: #fafafa;
        }
        .decision-box span {
            font-size: 16pt;
            font-weight: 900;
            letter-spacing: 4px;
            display: block;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            vertical-align: top;
            font-size: 11.5pt;
        }
        .signature-table td.sign-right {
            text-align: left;
            padding-left: 50px;
        }
        .sign-space {
            height: 70px;
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

    <!-- Non-print action bar -->
    <div class="no-print" style="margin-bottom: 20px; padding: 10px 14px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; font-family: Arial, sans-serif; font-size: 12px;">
        <div>
            <strong style="color: #0f172a;">Surat Keterangan Lulus (SKL) Resmi</strong>
            <span style="color: #64748b; margin-left: 8px;">Siswa: <strong>{{ $student->nama }}</strong> ({{ $student->nisn ?? $student->nis }})</span>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" style="padding: 6px 16px; background: #0f172a; color: #fbbf24; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                🖨️ Cetak Dokumen SKL (PDF)
            </button>
            <button onclick="window.close()" style="padding: 6px 12px; background: #e2e8f0; color: #334155; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                Tutup
            </button>
        </div>
    </div>

    <!-- Letterhead -->
    <table class="header-table">
        <tr>
            <td style="width: 90px; text-align: center;">
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

    <!-- Title & Reference Number -->
    <div class="letter-title">
        <h3>SURAT KETERANGAN LULUS</h3>
        <p>Nomor: 421/{{ date('Y') }}/SKL-NTO/{{ str_pad($student->id, 3, '0', STR_PAD_LEFT) }}</p>
    </div>

    <!-- Body -->
    <div class="content">
        <p>
            Yang bertanda tangan di bawah ini, Kepala Sekolah Excellent Spirit Christian School (NTO) National Plus Kupang, menerangkan dengan sesungguhnya bahwa:
        </p>

        <table class="bio-table">
            <tr>
                <td class="label">Nama Lengkap Siswa</td>
                <td class="colon">:</td>
                <td class="value">{{ strtoupper($student->nama) }}</td>
            </tr>
            <tr>
                <td class="label">Nomor Induk Siswa Nasional (NISN)</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->nisn ?? $student->nis }}</td>
            </tr>
            @if($student->nik)
            <tr>
                <td class="label">Nomor Induk Kependudukan (NIK)</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->nik }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="colon">:</td>
                <td class="value">
                    {{ $student->tempat_lahir ? $student->tempat_lahir . ', ' : '' }}
                    {{ $student->tanggal_lahir ? $student->tanggal_lahir->translatedFormat('d F Y') : '-' }}
                </td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td class="label">Nama Orang Tua / Wali</td>
                <td class="colon">:</td>
                <td class="value">
                    {{ $student->nama_ayah ?: ($student->nama_ibu ?: ($student->user ? $student->user->name : '-')) }}
                </td>
            </tr>
            <tr>
                <td class="label">Asal Kelas</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->formatted_kelas }}</td>
            </tr>
            @if($student->no_ijazah)
            <tr>
                <td class="label">Nomor Seri Ijazah</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->no_ijazah }}</td>
            </tr>
            @endif
        </table>

        <p>
            Berdasarkan kriteria kelulusan dan hasil evaluasi pembelajaran yang telah ditetapkan oleh satuan pendidikan, peserta didik yang bersangkutan dinyatakan:
        </p>

        <div class="decision-box">
            <span>L U L U S</span>
            <small style="font-size: 10pt; font-family: Arial, sans-serif; font-weight: normal; color: #475569;">
                Tahun Ajaran {{ $tahunAjaran }}
            </small>
        </div>

        @if($student->kategori_prestasi && $student->kategori_prestasi !== 'Tidak Ada')
            <p style="text-indent: 30px; font-size: 11pt; font-style: italic;">
                Catatan Prestasi: Peserta didik memiliki capaian prestasi bidang {{ $student->kategori_prestasi }} ({{ $student->keterangan_prestasi }}).
            </p>
        @endif

        <p>
            Surat Keterangan Lulus ini diterbitkan untuk dapat dipergunakan sebagaimana mestinya sampai dengan diterbitkannya Ijazah asli dari Kementerian Pendidikan Dasar dan Menengah Republik Indonesia.
        </p>
    </div>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center; vertical-align: middle;">
                <!-- Pas Foto Siswa (jika ada) -->
                @if($student->foto_url)
                    <div style="display: inline-block; width: 30mm; height: 40mm; border: 1px solid #000; padding: 2px;">
                        <img src="{{ $student->foto_url }}" alt="Pas Foto" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                @else
                    <div style="display: inline-block; width: 30mm; height: 40mm; border: 1px dashed #94a3b8; text-align: center; line-height: 40mm; font-size: 9pt; color: #64748b;">
                        Pasfoto 3x4
                    </div>
                @endif
            </td>
            <td class="sign-right">
                Kupang, {{ $tanggalCetak }}<br>
                Kepala Sekolah NTO National Plus,<br>
                <div class="sign-space"></div>
                <strong style="text-decoration: underline; font-size: 12pt;">( .................................................... )</strong><br>
                <span>NIP / NIY: .......................................</span>
            </td>
        </tr>
    </table>

</body>
</html>
