<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bug #5 Fix: Normalize surat_status consistency.
 *
 * Problem: Attendance rows uploaded BEFORE the surat_status column was added
 * have surat_status = NULL even though a surat_izin file exists. The system
 * treats NULL as 'menunggu' in some places and as 'no-letter' in others,
 * causing display and filter inconsistencies.
 *
 * Fix:
 *  - Backfill rows that have a surat_izin but surat_status = NULL → 'menunggu'
 *  - Remove the column-level DEFAULT 'menunggu' (it was masking the bug by
 *    auto-filling even rows that have NO surat, which is semantically wrong).
 *    Rows without a surat should always have surat_status = NULL.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Backfill: rows with an attached letter but no status → set to 'menunggu'
        DB::table('attendances')
            ->whereNotNull('surat_izin')
            ->whereNull('surat_status')
            ->update(['surat_status' => 'menunggu']);

        // 2. Remove the misleading column-level default so new rows without
        //    a letter correctly remain NULL (not auto-filled with 'menunggu').
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('surat_status', 50)->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        // Restore the original default
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('surat_status', 50)->nullable()->default('menunggu')->change();
        });
    }
};
