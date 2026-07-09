<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status?->value,
            'enrolled_at' => $this->enrolled_at?->toIso8601String(),
            'decided_at' => $this->decided_at?->toIso8601String(),
            'student' => new StudentResource($this->whenLoaded('student')),
            'course_section' => [
                'id' => $this->courseSection?->id,
                'section_code' => $this->courseSection?->section_code,
                'schedule' => $this->courseSection?->schedule,
                'room' => $this->courseSection?->room,
                'course' => new CourseResource($this->whenLoaded('courseSection')?->course ? $this->courseSection->course : null),
            ],
            'grade' => new GradeResource($this->whenLoaded('grade')),
        ];
    }
}
