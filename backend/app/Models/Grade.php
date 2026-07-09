<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LetterGrade;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'enrollment_id', 'attendance', 'assignment', 'quiz', 'project',
        'midterm', 'practical', 'final_exam', 'total_score',
        'letter_grade', 'gpa_points', 'is_published', 'published_at', 'graded_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'letter_grade' => LetterGrade::class,
    ];

    public const WEIGHTS = [
        'attendance' => 5,
        'assignment' => 10,
        'quiz'       => 10,
        'project'    => 10,
        'midterm'    => 20,
        'practical'  => 10,
        'final_exam' => 35,
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
