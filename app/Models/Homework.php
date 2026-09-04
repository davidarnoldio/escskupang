<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Homework extends Model
{
    use HasFactory;

    protected $table = 'homeworks';

    protected $fillable = [
        'teacher_id',
        'kelas',
        'mata_pelajaran',
        'judul',
        'deskripsi',
        'deadline',
        'lampiran_guru',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class, 'homework_id');
    }

    /**
     * Check if a submission exists for a specific student.
     */
    public function getSubmissionForStudent(int $studentId): ?HomeworkSubmission
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }
}
