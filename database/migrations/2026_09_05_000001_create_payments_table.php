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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('judul');
            $table->decimal('jumlah', 12, 2);
            $table->date('jatuh_tempo');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['belum_lunas', 'menunggu_konfirmasi', 'lunas', 'ditolak'])->default('belum_lunas');
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
