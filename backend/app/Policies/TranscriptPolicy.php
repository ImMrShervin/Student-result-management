<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Transcript;
use App\Models\User;

class TranscriptPolicy
{
    public function view(User $u, Transcript $t): bool
    {
        return $u->id === $t->student->user_id || $u->hasAnyRole([
            UserRole::SUPER_ADMIN->value, UserRole::ADMIN->value,
            UserRole::DEAN->value, UserRole::DEPARTMENT_HEAD->value,
        ]);
    }
}
