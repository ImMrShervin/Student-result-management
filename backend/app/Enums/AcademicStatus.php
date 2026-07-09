<?php

declare(strict_types=1);

namespace App\Enums;

enum AcademicStatus: string
{
    case EXCELLENT = 'excellent'; 
    case PASSED = 'passed';   
    case CONDITIONAL = 'conditional';
    case PROBATION = 'probation';
    case FAILED = 'failed';
    case DISMISSED = 'dismissed';
    case GRADUATED = 'graduated';

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::EXCELLENT => 'emerald',
            self::PASSED => 'green',
            self::CONDITIONAL => 'yellow',
            self::PROBATION => 'orange',
            self::FAILED => 'red',
            self::DISMISSED => 'gray',
            self::GRADUATED => 'blue',
        };
    }
}
