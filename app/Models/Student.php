<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    public const OFFICIAL_CLASSES = [
        'TK',
        'Kelas 1',
        'Kelas 2',
        'Kelas 3',
        'Kelas 4',
        'Kelas 5',
        'Kelas 6',
    ];

    /**
     * Get all active class options combining official levels and distinct database values.
     */
    public static function getAllClasses(): array
    {
        $dbClasses = static::whereNotNull('kelas')->distinct()->pluck('kelas')->toArray();
        $merged = array_unique(array_merge(static::OFFICIAL_CLASSES, $dbClasses));
        natcasesort($merged);
        return array_values($merged);
    }

    protected $fillable = [
        'nisn',
        'nis',
        'nama',
        'nik',
        'no_kk',
        'kelas',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'no_akta_kelahiran',
        'agama',
        'kewarganegaraan',
        'alamat',
        'telepon',
        'foto',
        'is_abk',
        'kategori_prestasi',
        'keterangan_prestasi',
        'tinggi_badan',
        'berat_badan',
        'lingkar_kepala',
        'jumlah_saudara_kandung',
        'nama_ayah',
        'nik_ayah',
        'tahun_lahir_ayah',
        'pendidikan_ayah',
        'penghasilan_ayah',
        'nama_ibu',
        'nik_ibu',
        'tahun_lahir_ibu',
        'pendidikan_ibu',
        'penghasilan_ibu',
        'status',
        'tahun_lulus',
        'tanggal_lulus',
        'no_ijazah',
        'sekolah_lanjutan',
        'catatan_kelulusan',
    ];

    protected $casts = [
        'is_abk' => 'boolean',
        'tanggal_lahir' => 'date',
        'tanggal_lulus' => 'date',
        'tinggi_badan' => 'integer',
        'berat_badan' => 'integer',
        'lingkar_kepala' => 'integer',
        'jumlah_saudara_kandung' => 'integer',
    ];

    /**
     * Scope to only query active students.
     */
    public function scopeAktif($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'aktif')->orWhereNull('status');
        });
    }

    /**
     * Scope to only query graduated students (alumni).
     */
    public function scopeAlumni($query)
    {
        return $query->where('status', 'lulus');
    }

    /**
     * Check if student is an alumnus/alumna.
     */
    public function isAlumni(): bool
    {
        return $this->status === 'lulus';
    }


    /**
     * Backward-compatibility accessor for NIS -> NISN.
     */
    public function getNisAttribute(): ?string
    {
        return $this->attributes['nisn'] ?? $this->attributes['nis'] ?? null;
    }

    /**
     * Backward-compatibility mutator for NIS -> NISN.
     */
    public function setNisAttribute(?string $value): void
    {
        $this->attributes['nisn'] = $value;
    }

    /**
     * Get accessible photo URL or null.
     * Automatically handles relative path 'students/xxx.jpg' -> '/storage/students/xxx.jpg'.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        if (str_starts_with($this->foto, 'storage/')) {
            return asset($this->foto);
        }

        return asset('storage/' . $this->foto);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function homeworkSubmissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
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
