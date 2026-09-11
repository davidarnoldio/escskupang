<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('surat_status', 50)->nullable()->default('menunggu')->after('surat_izin');
            $table->text('catatan_guru')->nullable()->after('surat_status');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['surat_status', 'catatan_guru']);
        });
    }
};
