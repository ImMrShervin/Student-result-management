<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'description' => $this->description,
            'theory_credit' => $this->theory_credit,
            'practical_credit' => $this->practical_credit,
            'total_credit' => $this->total_credit,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'prerequisites' => CourseResource::collection($this->whenLoaded('prerequisites')),
        ];
    }
}
