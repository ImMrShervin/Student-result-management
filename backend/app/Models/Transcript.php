<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transcript extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'verification_code', 'pdf_path',
        'cumulative_gpa', 'credits_earned', 'payload',
        'generated_at', 'generated_by',
    ];

    protected $casts = [
        'payload' => 'array',
        'generated_at' => 'datetime',
        'cumulative_gpa' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
