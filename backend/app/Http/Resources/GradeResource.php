<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'attendance' => (float) $this->attendance,
            'assignment' => (float) $this->assignment,
            'quiz' => (float) $this->quiz,
            'project' => (float) $this->project,
            'midterm' => (float) $this->midterm,
            'practical' => (float) $this->practical,
            'final_exam' => (float) $this->final_exam,
            'total_score' => $this->total_score !== null ? (float) $this->total_score : null,
            'letter_grade' => $this->letter_grade?->value,
            'gpa_points' => $this->gpa_points !== null ? (float) $this->gpa_points : null,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}
