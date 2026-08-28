<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Attendance;
use App\Models\Setting;

$count = 0;
foreach (Attendance::all() as $att) {
    $ket = $att->keterangan ?? '';
    if (str_contains(strtolower($ket), 'terlambat') && preg_match('/\[(\d{2}:\d{2}:\d{2})\]/', $ket, $m)) {
        $student = $att->student;
        $jamTerlambatConfig = ($student && $student->is_abk)
            ? Setting::get('jam_terlambat_abk', '08:30')
            : Setting::get('jam_terlambat', '07:30');

        $scanCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $m[1], 'Asia/Makassar');
        $thresholdCarbon = \Carbon\Carbon::parse($att->tanggal . ' ' . $jamTerlambatConfig . ':00', 'Asia/Makassar');

        if ($scanCarbon->greaterThan($thresholdCarbon)) {
            $mins = (int) $scanCarbon->diffInMinutes($thresholdCarbon);
            $abkLabel = ($student && $student->is_abk) ? ' (ABK)' : '';
            $newKet = "Scan QR [{$m[1]}] - Terlambat {$mins} menit{$abkLabel}";
            $att->update(['keterangan' => $newKet]);
            $count++;
        }
    }
}

echo "Updated {$count} attendance records successfully.\n";
