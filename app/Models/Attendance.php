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
        'surat_status',
        'catatan_guru',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get badge CSS classes for surat status display.
     */
    public function getSuratStatusBadgeClass(): string
    {
        return match ($this->surat_status) {
            'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-amber-100 text-amber-800 border-amber-300',
        };
    }

    /**
     * Get human readable label for surat status.
     */
    public function getSuratStatusLabel(): string
    {
        return match ($this->surat_status) {
            'disetujui' => 'Diterima / Disetujui',
            'ditolak' => 'Ditolak',
            default => 'Menunggu Konfirmasi',
        };
    }

    /**
     * Check if the attached letter file is a PDF.
     */
    public function isPdfSurat(): bool
    {
        if (!$this->surat_izin) {
            return false;
        }

        return str_ends_with(strtolower($this->surat_izin), '.pdf');
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
