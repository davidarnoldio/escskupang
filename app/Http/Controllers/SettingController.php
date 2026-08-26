<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display school operating hours settings page.
     */
    public function index()
    {
        $jamMasuk = Setting::get('jam_masuk', '07:00');
        $jamTerlambat = Setting::get('jam_terlambat', '07:30');
        $jamPulang = Setting::get('jam_pulang', '14:00');

        $jamMasukAbk = Setting::get('jam_masuk_abk', '08:00');
        $jamTerlambatAbk = Setting::get('jam_terlambat_abk', '08:30');
        $jamPulangAbk = Setting::get('jam_pulang_abk', '13:00');

        return view('settings.index', compact(
            'jamMasuk',
            'jamTerlambat',
            'jamPulang',
            'jamMasukAbk',
            'jamTerlambatAbk',
            'jamPulangAbk'
        ));
    }

    /**
     * Update school operating hours settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk' => ['required', 'string'],
            'jam_terlambat' => ['required', 'string'],
            'jam_pulang' => ['required', 'string'],
            'jam_masuk_abk' => ['nullable', 'string'],
            'jam_terlambat_abk' => ['nullable', 'string'],
            'jam_pulang_abk' => ['nullable', 'string'],
        ]);

        Setting::set('jam_masuk', $request->input('jam_masuk'));
        Setting::set('jam_terlambat', $request->input('jam_terlambat'));
        Setting::set('jam_pulang', $request->input('jam_pulang'));

        if ($request->filled('jam_masuk_abk')) {
            Setting::set('jam_masuk_abk', $request->input('jam_masuk_abk'));
        }
        if ($request->filled('jam_terlambat_abk')) {
            Setting::set('jam_terlambat_abk', $request->input('jam_terlambat_abk'));
        }
        if ($request->filled('jam_pulang_abk')) {
            Setting::set('jam_pulang_abk', $request->input('jam_pulang_abk'));
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan Jam Presensi Siswa Regular & ABK ESCS Kupang berhasil diperbarui!');
    }
}
