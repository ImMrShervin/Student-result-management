<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'employee_code' => $this->employee_code,
            'office' => $this->office,
            'academic_rank' => $this->academic_rank,
            'hired_on' => $this->hired_on?->toDateString(),
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
