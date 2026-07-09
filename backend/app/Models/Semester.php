<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Semester extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_id', 'name', 'term', 'starts_on', 'ends_on',
        'enrollment_starts_on', 'enrollment_ends_on', 'is_current', 'grades_published',
    ];

    protected $casts = [
        'starts_on' => 'date',
        'ends_on' => 'date',
        'enrollment_starts_on' => 'date',
        'enrollment_ends_on' => 'date',
        'is_current' => 'boolean',
        'grades_published' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }

    public function scopeCurrent($q)
    {
        return $q->where('is_current', true);
    }
}
