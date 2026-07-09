<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $u): bool
    {
        return $u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEAN->value, UserRole::DEPARTMENT_HEAD->value, UserRole::TEACHER->value]);
    }

    public function publish(User $u, Grade $g): bool
    {
        if ($u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEPARTMENT_HEAD->value])) {
            return true;
        }
        return $u->teacher && $u->teacher->id === $g->enrollment->courseSection->teacher_id;
    }
}
