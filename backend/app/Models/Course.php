<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id', 'code', 'title', 'description',
        'theory_credit', 'practical_credit',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class);
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Course::class, 'course_prerequisites',
            'course_id', 'prerequisite_id'
        );
    }

    public function getTotalCreditAttribute(): int
    {
        return (int) $this->theory_credit + (int) $this->practical_credit;
    }
}
