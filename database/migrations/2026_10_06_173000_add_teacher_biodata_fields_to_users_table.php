<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Data Pribadi & Kependudukan Guru
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 25)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 10)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'tempat_lahir')) {
                $table->string('tempat_lahir', 100)->nullable()->after('jenis_kelamin');
            }
            if (!Schema::hasColumn('users', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            }
            if (!Schema::hasColumn('users', 'nama_ibu_kandung')) {
                $table->string('nama_ibu_kandung', 100)->nullable()->after('tanggal_lahir');
            }

            // Alamat Domisili & Wilayah
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable();
            }
            if (!Schema::hasColumn('users', 'rt')) {
                $table->string('rt', 10)->nullable();
            }
            if (!Schema::hasColumn('users', 'rw')) {
                $table->string('rw', 10)->nullable();
            }
            if (!Schema::hasColumn('users', 'dusun')) {
                $table->string('dusun', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'desa_kelurahan')) {
                $table->string('desa_kelurahan', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'lintang')) {
                $table->string('lintang', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'bujur')) {
                $table->string('bujur', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'kode_pos')) {
                $table->string('kode_pos', 10)->nullable();
            }

            // Status Kepegawaian & Penugasan
            if (!Schema::hasColumn('users', 'status_kepegawaian')) {
                $table->string('status_kepegawaian', 60)->nullable();
            }
            if (!Schema::hasColumn('users', 'niy_nigk')) {
                $table->string('niy_nigk', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'nuptk')) {
                $table->string('nuptk', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'sk_pengangkatan')) {
                $table->string('sk_pengangkatan', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'tmt_pengangkatan')) {
                $table->date('tmt_pengangkatan')->nullable();
            }
            if (!Schema::hasColumn('users', 'lembaga_pengangkat')) {
                $table->string('lembaga_pengangkat', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'sumber_gaji')) {
                $table->string('sumber_gaji', 60)->nullable();
            }
            if (!Schema::hasColumn('users', 'keahlian_laboratorium')) {
                $table->string('keahlian_laboratorium', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'mampu_menangani_kebutuhan_khusus')) {
                $table->string('mampu_menangani_kebutuhan_khusus', 20)->default('Tidak')->nullable();
            }
            if (!Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp', 30)->nullable();
            }
            if (!Schema::hasColumn('users', 'alasan_keluar_kerja')) {
                $table->text('alasan_keluar_kerja')->nullable();
            }

            // Riwayat Sertifikasi & Prestasi
            if (!Schema::hasColumn('users', 'jenis_sertifikasi')) {
                $table->string('jenis_sertifikasi', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'nomor_sertifikasi')) {
                $table->string('nomor_sertifikasi', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'tahun_sertifikasi')) {
                $table->string('tahun_sertifikasi', 10)->nullable();
            }
            if (!Schema::hasColumn('users', 'bidang_studi_sertifikasi')) {
                $table->string('bidang_studi_sertifikasi', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'nrg')) {
                $table->string('nrg', 50)->nullable();
            }
            if (!Schema::hasColumn('users', 'nomor_peserta')) {
                $table->string('nomor_peserta', 100)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nik',
                'jenis_kelamin',
                'tempat_lahir',
                'tanggal_lahir',
                'nama_ibu_kandung',
                'alamat',
                'rt',
                'rw',
                'dusun',
                'desa_kelurahan',
                'kecamatan',
                'lintang',
                'bujur',
                'kode_pos',
                'status_kepegawaian',
                'niy_nigk',
                'nuptk',
                'sk_pengangkatan',
                'tmt_pengangkatan',
                'lembaga_pengangkat',
                'sumber_gaji',
                'keahlian_laboratorium',
                'mampu_menangani_kebutuhan_khusus',
                'no_hp',
                'alasan_keluar_kerja',
                'jenis_sertifikasi',
                'nomor_sertifikasi',
                'tahun_sertifikasi',
                'bidang_studi_sertifikasi',
                'nrg',
                'nomor_peserta',
            ]);
        });
    }
};
