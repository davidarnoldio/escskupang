<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDailyNote extends Model
{
    use HasFactory;

    public const KATEGORI_OPTIONS = [
        'Akademik & Pembelajaran',
        'Sikap & Perilaku',
        'Kemandirian & Kebiasaan',
        'Kesehatan & Emosi',
        'Apresiasi & Prestasi',
        'Pemberitahuan Khusus',
        'Lainnya',
    ];

    protected $fillable = [
        'student_id',
        'teacher_id',
        'tanggal',
        'kategori',
        'judul',
        'catatan',
        'pesan_untuk_orangtua',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * Get badge styling class according to category.
     */
    public function getKategoriBadgeClass(): string
    {
        return match ($this->kategori) {
            'Akademik & Pembelajaran' => 'bg-blue-50 text-blue-800 border-blue-200',
            'Sikap & Perilaku'        => 'bg-purple-50 text-purple-800 border-purple-200',
            'Kemandirian & Kebiasaan' => 'bg-teal-50 text-teal-800 border-teal-200',
            'Kesehatan & Emosi'       => 'bg-rose-50 text-rose-800 border-rose-200',
            'Apresiasi & Prestasi'    => 'bg-amber-50 text-amber-800 border-amber-200',
            'Pemberitahuan Khusus'    => 'bg-indigo-50 text-indigo-800 border-indigo-200',
            default                   => 'bg-slate-100 text-slate-800 border-slate-200',
        };
    }

    /**
     * Get category icon badge.
     */
    public function getKategoriIcon(): string
    {
        return match ($this->kategori) {
            'Akademik & Pembelajaran' => '📚',
            'Sikap & Perilaku'        => '🌟',
            'Kemandirian & Kebiasaan' => '🌱',
            'Kesehatan & Emosi'       => '❤️',
            'Apresiasi & Prestasi'    => '🏆',
            'Pemberitahuan Khusus'    => '📢',
            default                   => '📝',
        };
    }
}
