<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $u): bool { return true; }
    public function view(User $u, Department $d): bool { return true; }
    public function create(User $u): bool { return $u->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value, UserRole::DEAN->value]); }
    public function update(User $u, Department $d): bool { return $this->create($u); }
    public function delete(User $u, Department $d): bool { return $u->hasRole(UserRole::SUPER_ADMIN->value); }
}
