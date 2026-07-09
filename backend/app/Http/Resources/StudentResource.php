<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'student_number' => $this->student_number,
            'entry_year' => $this->entry_year,
            'current_semester' => $this->current_semester,
            'current_gpa' => (float) $this->current_gpa,
            'cumulative_gpa' => (float) $this->cumulative_gpa,
            'credits_passed' => $this->credits_passed,
            'credits_required' => $this->credits_required,
            'credits_remaining' => $this->creditsRemaining(),
            'academic_status' => $this->academic_status?->value,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'faculty' => new FacultyResource($this->whenLoaded('faculty')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
