<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AcademicStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'department_id', 'faculty_id', 'student_number', 'entry_year',
        'current_semester', 'current_gpa', 'cumulative_gpa', 'credits_passed',
        'credits_required', 'academic_status',
    ];

    protected $casts = [
        'current_gpa' => 'decimal:2',
        'cumulative_gpa' => 'decimal:2',
        'academic_status' => AcademicStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function semesterGpas(): HasMany
    {
        return $this->hasMany(SemesterGpa::class);
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(Transcript::class);
    }

    public function creditsRemaining(): int
    {
        return max(0, $this->credits_required - $this->credits_passed);
    }
}
