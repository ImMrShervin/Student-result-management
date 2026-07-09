<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AcademicStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterGpa extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'semester_id', 'semester_gpa',
        'cumulative_gpa', 'credits_attempted', 'credits_earned', 'academic_status',
    ];

    protected $casts = [
        'semester_gpa' => 'decimal:2',
        'cumulative_gpa' => 'decimal:2',
        'academic_status' => AcademicStatus::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
