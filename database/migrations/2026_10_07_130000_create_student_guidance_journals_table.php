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
        Schema::create('student_guidance_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->string('kelas', 50);
            $table->date('tanggal');
            $table->text('perilaku_kejadian');
            $table->enum('kategori', ['A', 'S', 'D', 'P'])->default('A');
            $table->text('identifikasi_masalah')->nullable();
            $table->text('pendekatan_wali_kelas')->nullable();
            $table->text('komitmen_siswa')->nullable();
            $table->text('tindak_lanjut')->nullable();
            $table->boolean('paraf')->default(true);
            $table->timestamps();

            $table->index(['kelas', 'tanggal']);
            $table->index(['student_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_guidance_journals');
    }
};
