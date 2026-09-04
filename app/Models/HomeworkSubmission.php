<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'homework_id',
        'student_id',
        'foto_pr',
        'catatan_siswa',
        'nilai',
        'catatan_guru',
        'submitted_at',
        'graded_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'nilai' => 'integer',
    ];

    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class, 'homework_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Helper to get grade badge styling.
     */
    public function getGradeBadgeClass(): string
    {
        if ($this->nilai === null) {
            return 'bg-amber-100 text-amber-800 border-amber-300';
        }

        if ($this->nilai >= 85) {
            return 'bg-emerald-100 text-emerald-800 border-emerald-300';
        }

        if ($this->nilai >= 70) {
            return 'bg-blue-100 text-blue-800 border-blue-300';
        }

        return 'bg-rose-100 text-rose-800 border-rose-300';
    }
}
