<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    public const OFFICIAL_CLASSES = [
        'Nursery',
        'Pre-K',
        'Kindergarten',
        'Primary Preparation',
        'Primary A',
        'Primary B',
        'Primary C',
        'Junior High',
        'Senior High',
    ];

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'jenis_kelamin',
        'alamat',
        'telepon',
        'foto',
        'is_abk',
    ];

    protected $casts = [
        'is_abk' => 'boolean',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Check if a user has permission to view this student's profile photo.
     * Allowed: Admin, Student's own Wali Kelas (class homeroom teacher), and Student's own Parent.
     */
    public function canViewPhoto(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // 1. Admin can view all photos
        if ($user->isAdmin()) {
            return true;
        }

        // 2. Student's own Parent can view
        if ($user->isParent() && $user->student_id == $this->id) {
            return true;
        }

        // 3. Wali Kelas of this specific student's class can view
        if ($user->isTeacher()) {
            $assignedClass = $user->getAssignedClass();
            return $assignedClass !== null && strtolower($assignedClass) === strtolower($this->kelas);
        }

        return false;
    }
}
