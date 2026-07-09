<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Enrollment;
use App\Models\User;

class EnrollmentPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEAN->value, UserRole::DEPARTMENT_HEAD->value, UserRole::TEACHER->value]);
    }

    public function decide(User $u, Enrollment $e): bool
    {
        return $u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEPARTMENT_HEAD->value]);
    }

    public function withdraw(User $u, Enrollment $e): bool
    {
        return $u->id === $e->student->user_id || $this->decide($u);
    }

    public function grade(User $u, Enrollment $e): bool
    {
        if ($u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value])) {
            return true;
        }
        return $u->teacher && $u->teacher->id === $e->courseSection->teacher_id;
    }
}
