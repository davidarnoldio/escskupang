<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'judul',
        'jumlah',
        'jatuh_tempo',
        'keterangan',
        'status',
        'bukti_pembayaran',
        'catatan_admin',
        'paid_at',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'jatuh_tempo' => 'date',
        'paid_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get badge CSS classes for status display.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'lunas' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'menunggu_konfirmasi' => 'bg-amber-100 text-amber-800 border-amber-300',
            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
            default => 'bg-red-100 text-red-800 border-red-300',
        };
    }

    /**
     * Get human readable status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'lunas' => 'Lunas',
            'menunggu_konfirmasi' => 'Menunggu Verifikasi Admin',
            'ditolak' => 'Ditolak',
            default => 'Belum Lunas',
        };
    }

    /**
     * Get clean resolved URL for payment proof.
     */
    public function getBuktiUrlAttribute(): ?string
    {
        if (!$this->bukti_pembayaran) {
            return null;
        }

        if (str_starts_with($this->bukti_pembayaran, 'http://') || str_starts_with($this->bukti_pembayaran, 'https://')) {
            return $this->bukti_pembayaran;
        }

        $clean = ltrim(str_replace(['public/', 'storage/'], '', $this->bukti_pembayaran), '/');
        return url('storage/' . $clean);
    }

    /**
     * Check if payment proof is a PDF document.
     */
    public function isPdfBukti(): bool
    {
        if (!$this->bukti_pembayaran) {
            return false;
        }

        return str_ends_with(strtolower($this->bukti_pembayaran), '.pdf');
    }
}
