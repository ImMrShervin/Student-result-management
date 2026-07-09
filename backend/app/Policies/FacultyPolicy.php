<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Faculty;
use App\Models\User;

class FacultyPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Faculty $f): bool { return true; }
    public function create(User $user): bool { return $user->hasAnyRole([UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value]); }
    public function update(User $user, Faculty $f): bool { return $this->create($user); }
    public function delete(User $user, Faculty $f): bool { return $user->hasRole(UserRole::SUPER_ADMIN->value); }
}
