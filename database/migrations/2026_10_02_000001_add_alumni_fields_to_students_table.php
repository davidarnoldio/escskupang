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
            $table->string('status', 20)->default('aktif')->after('kelas'); // 'aktif' atau 'lulus'
            $table->string('tahun_lulus', 20)->nullable()->after('status'); // contoh: '2024/2025' atau '2025'
            $table->date('tanggal_lulus')->nullable()->after('tahun_lulus');
            $table->string('no_ijazah', 100)->nullable()->after('tanggal_lulus');
            $table->string('sekolah_lanjutan', 150)->nullable()->after('no_ijazah');
            $table->text('catatan_kelulusan')->nullable()->after('sekolah_lanjutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'tahun_lulus',
                'tanggal_lulus',
                'no_ijazah',
                'sekolah_lanjutan',
                'catatan_kelulusan',
            ]);
        });
    }
};
