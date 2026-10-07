<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGuidanceMonthlyRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'kelas',
        'bulan',
        'kondisi_umum_kelas',
        'siswa_perhatian_khusus',
        'tindak_lanjut_ortu_guru',
        'rekomendasi_berikutnya',
        'ket_akademik',
        'ket_sosial_emosional',
        'ket_kedisiplinan',
        'ket_potensi_prestasi',
    ];

    /**
     * Get the homeroom teacher who wrote the monthly recap.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
