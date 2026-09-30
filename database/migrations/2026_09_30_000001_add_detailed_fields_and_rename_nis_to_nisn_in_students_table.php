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
        Schema::table('students', function (Blueprint $table) {
            // 1. Rename 'nis' to 'nisn'
            if (Schema::hasColumn('students', 'nis') && !Schema::hasColumn('students', 'nisn')) {
                $table->renameColumn('nis', 'nisn');
            } elseif (!Schema::hasColumn('students', 'nisn')) {
                $table->string('nisn', 50)->nullable()->unique();
            }

            // 2. Data Dokumen Kependudukan & Kelahiran
            if (!Schema::hasColumn('students', 'nik')) {
                $table->string('nik', 20)->nullable()->after('nama');
            }
            if (!Schema::hasColumn('students', 'no_kk')) {
                $table->string('no_kk', 20)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('students', 'tempat_lahir')) {
                $table->string('tempat_lahir', 100)->nullable();
            }
            if (!Schema::hasColumn('students', 'tanggal_lahir')) {
                $table->date('tanggal_lahir')->nullable();
            }
            if (!Schema::hasColumn('students', 'no_akta_kelahiran')) {
                $table->string('no_akta_kelahiran', 100)->nullable();
            }

            // 3. Agama & Kewarganegaraan
            if (!Schema::hasColumn('students', 'agama')) {
                $table->string('agama', 30)->nullable();
            }
            if (!Schema::hasColumn('students', 'kewarganegaraan')) {
                $table->string('kewarganegaraan', 20)->default('WNI')->nullable();
            }

            // 4. Prestasi Siswa
            if (!Schema::hasColumn('students', 'kategori_prestasi')) {
                $table->string('kategori_prestasi', 50)->nullable();
            }
            if (!Schema::hasColumn('students', 'keterangan_prestasi')) {
                $table->text('keterangan_prestasi')->nullable();
            }

            // 5. Data Fisik Siswa (Periodik)
            if (!Schema::hasColumn('students', 'tinggi_badan')) {
                $table->unsignedSmallInteger('tinggi_badan')->nullable();
            }
            if (!Schema::hasColumn('students', 'berat_badan')) {
                $table->unsignedSmallInteger('berat_badan')->nullable();
            }
            if (!Schema::hasColumn('students', 'lingkar_kepala')) {
                $table->unsignedSmallInteger('lingkar_kepala')->nullable();
            }
            if (!Schema::hasColumn('students', 'jumlah_saudara_kandung')) {
                $table->unsignedTinyInteger('jumlah_saudara_kandung')->default(0)->nullable();
            }

            // 6. Data Ayah Kandung
            if (!Schema::hasColumn('students', 'nama_ayah')) {
                $table->string('nama_ayah', 255)->nullable();
            }
            if (!Schema::hasColumn('students', 'nik_ayah')) {
                $table->string('nik_ayah', 20)->nullable();
            }
            if (!Schema::hasColumn('students', 'tahun_lahir_ayah')) {
                $table->string('tahun_lahir_ayah', 10)->nullable();
            }
            if (!Schema::hasColumn('students', 'pendidikan_ayah')) {
                $table->string('pendidikan_ayah', 50)->nullable();
            }
            if (!Schema::hasColumn('students', 'penghasilan_ayah')) {
                $table->string('penghasilan_ayah', 50)->nullable();
            }

            // 7. Data Ibu Kandung
            if (!Schema::hasColumn('students', 'nama_ibu')) {
                $table->string('nama_ibu', 255)->nullable();
            }
            if (!Schema::hasColumn('students', 'nik_ibu')) {
                $table->string('nik_ibu', 20)->nullable();
            }
            if (!Schema::hasColumn('students', 'tahun_lahir_ibu')) {
                $table->string('tahun_lahir_ibu', 10)->nullable();
            }
            if (!Schema::hasColumn('students', 'pendidikan_ibu')) {
                $table->string('pendidikan_ibu', 50)->nullable();
            }
            if (!Schema::hasColumn('students', 'penghasilan_ibu')) {
                $table->string('penghasilan_ibu', 50)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'nisn') && !Schema::hasColumn('students', 'nis')) {
                $table->renameColumn('nisn', 'nis');
            }

            $columns = [
                'nik',
                'no_kk',
                'tempat_lahir',
                'tanggal_lahir',
                'no_akta_kelahiran',
                'agama',
                'kewarganegaraan',
                'kategori_prestasi',
                'keterangan_prestasi',
                'tinggi_badan',
                'berat_badan',
                'lingkar_kepala',
                'jumlah_saudara_kandung',
                'nama_ayah',
                'nik_ayah',
                'tahun_lahir_ayah',
                'pendidikan_ayah',
                'penghasilan_ayah',
                'nama_ibu',
                'nik_ibu',
                'tahun_lahir_ibu',
                'pendidikan_ibu',
                'penghasilan_ibu',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
