<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'tanggal',
        'status',
        'keterangan',
        'surat_izin',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get clean resolved URL for permission letter.
     */
    public function getSuratIzinUrlAttribute(): ?string
    {
        if (!$this->surat_izin) {
            return null;
        }

        if (str_starts_with($this->surat_izin, 'http://') || str_starts_with($this->surat_izin, 'https://')) {
            return $this->surat_izin;
        }

        if (str_starts_with($this->surat_izin, 'uploads/')) {
            return asset($this->surat_izin);
        }

        $clean = ltrim(str_replace(['public/', 'storage/'], '', $this->surat_izin), '/');
        return url('storage/' . $clean);
    }
}
