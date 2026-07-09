<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case DEAN = 'dean';
    case DEPARTMENT_HEAD = 'department_head';
    case TEACHER = 'teacher';
    case STUDENT = 'student';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::DEAN => 'Dean',
            self::DEPARTMENT_HEAD => 'Department Head',
            self::TEACHER => 'Teacher',
            self::STUDENT => 'Student',
        };
    }

    /** @return array<int,string> */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
