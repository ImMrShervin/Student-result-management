<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value,
            UserRole::DEAN->value, UserRole::DEPARTMENT_HEAD->value,
            UserRole::TEACHER->value,
        ]);
    }

    public function view(User $user, Student $student): bool
    {
        return $user->id === $student->user_id || $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEPARTMENT_HEAD->value]);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value]);
    }

    public function enroll(User $user, Student $student): bool
    {
        return $user->id === $student->user_id || $user->hasAnyRole([
            UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEPARTMENT_HEAD->value,
        ]);
    }

    public function generateTranscript(User $user, Student $student): bool
    {
        return $user->id === $student->user_id || $user->hasAnyRole([
            UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEAN->value, UserRole::DEPARTMENT_HEAD->value,
        ]);
    }
}
