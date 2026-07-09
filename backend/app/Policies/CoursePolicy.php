<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $u): bool { return true; }
    public function view(User $u, Course $c): bool { return true; }
    public function create(User $u): bool { return $u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEPARTMENT_HEAD->value]); }
    public function update(User $u, Course $c): bool { return $this->create($u); }
    public function delete(User $u, Course $c): bool { return $u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value]); }
}
