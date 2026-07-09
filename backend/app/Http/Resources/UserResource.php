<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'code' => $this->code,
            'avatar_url' => $this->avatar_path ? asset('storage/' . $this->avatar_path) : null,
            'gender' => $this->gender?->value,
            'birth_date' => $this->birth_date?->toDateString(),
            'address' => $this->address,
            'is_active' => $this->is_active,
            'locale' => $this->locale,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'permissions' => $this->when($this->relationLoaded('roles'), fn () => $this->getAllPermissions()->pluck('name')),
            'student' => new StudentResource($this->whenLoaded('student')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
