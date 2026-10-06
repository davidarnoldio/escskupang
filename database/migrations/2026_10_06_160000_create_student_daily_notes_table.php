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
        Schema::create('student_daily_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('kategori', 50)->default('Umum'); // Akademik, Sikap & Perilaku, Kemandirian, Kesehatan, Apresiasi & Prestasi, Khusus, dll.
            $table->string('judul', 150);
            $table->text('catatan');
            $table->text('pesan_untuk_orangtua')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'tanggal']);
            $table->index('teacher_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_daily_notes');
    }
};
