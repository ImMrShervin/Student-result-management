<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEAN->value, UserRole::DEPARTMENT_HEAD->value]);
    }

    public function view(User $user, Teacher $teacher): bool
    {
        return $user->id === $teacher->user_id || $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value]);
    }

    public function update(User $user, Teacher $teacher): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Teacher $teacher): bool
    {
        return $user->hasRole(UserRole::SUPER_ADMIN->value);
    }
}
