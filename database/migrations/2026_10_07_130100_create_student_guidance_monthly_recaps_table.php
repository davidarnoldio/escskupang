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
        Schema::create('student_guidance_monthly_recaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('kelas', 50);
            $table->string('bulan', 7); // Format: YYYY-MM
            $table->text('kondisi_umum_kelas')->nullable();
            $table->text('siswa_perhatian_khusus')->nullable();
            $table->text('tindak_lanjut_ortu_guru')->nullable();
            $table->text('rekomendasi_berikutnya')->nullable();
            $table->text('ket_akademik')->nullable();
            $table->text('ket_sosial_emosional')->nullable();
            $table->text('ket_kedisiplinan')->nullable();
            $table->text('ket_potensi_prestasi')->nullable();
            $table->timestamps();

            $table->unique(['kelas', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_guidance_monthly_recaps');
    }
};
