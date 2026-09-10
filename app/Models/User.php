<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'student_id', 'assigned_class'])]
#[Hidden(['password', 'remember_token', 'plain_password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the student record linked to parent user.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function homeworks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Homework::class, 'teacher_id');
    }

    /**
     * Role helper checks.
     */
    public function isParent(): bool
    {
        return $this->role === 'orang_tua';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'guru' || $this->role === 'wali_kelas';
    }

    /**
     * Get assigned class for homeroom teacher (returns null for admin).
     */
    public function getAssignedClass(): ?string
    {
        if ($this->isAdmin()) {
            return null;
        }

        if (!empty($this->assigned_class)) {
            return $this->assigned_class;
        }

        $classes = Student::OFFICIAL_CLASSES;
        usort($classes, fn($a, $b) => strlen($b) <=> strlen($a));
        $nameLower = strtolower($this->name);

        foreach ($classes as $class) {
            if (str_contains($nameLower, strtolower($class))) {
                return $class;
            }
        }

        return null;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
