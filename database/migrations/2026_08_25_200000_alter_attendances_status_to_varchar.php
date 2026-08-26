<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE `attendances` MODIFY `status` VARCHAR(50) NOT NULL DEFAULT 'hadir'");
            }
        } catch (\Throwable $e) {
            // Ignore if already string or unsupported driver
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
