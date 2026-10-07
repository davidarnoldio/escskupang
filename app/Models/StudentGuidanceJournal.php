<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentGuidanceJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'teacher_id',
        'kelas',
        'tanggal',
        'perilaku_kejadian',
        'kategori',
        'identifikasi_masalah',
        'pendekatan_wali_kelas',
        'komitmen_siswa',
        'tindak_lanjut',
        'paraf',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'paraf' => 'boolean',
    ];

    /**
     * Get student who was observed.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the homeroom teacher who created this entry.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Helper to get full category name.
     */
    public function getKategoriNamaAttribute(): string
    {
        return match($this->kategori) {
            'A' => 'Akademik',
            'S' => 'Sosial-Emosional',
            'D' => 'Kedisiplinan',
            'P' => 'Potensi / Prestasi',
            default => 'Lainnya',
        };
    }

    /**
     * Helper badge CSS classes.
     */
    public function getKategoriBadgeAttribute(): string
    {
        return match($this->kategori) {
            'A' => 'bg-blue-100 text-blue-800 border-blue-200',
            'S' => 'bg-purple-100 text-purple-800 border-purple-200',
            'D' => 'bg-amber-100 text-amber-800 border-amber-200',
            'P' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            default => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    /**
     * Alias for identifikasi_akar_masalah
     */
    public function getIdentifikasiAkarMasalahAttribute(): ?string
    {
        return $this->identifikasi_masalah;
    }
}
