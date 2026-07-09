<?php

declare(strict_types=1);

namespace App\Enums;

enum LetterGrade: string
{
    case A_PLUS = 'A+';
    case A = 'A';
    case A_MINUS = 'A-';
    case B_PLUS = 'B+';
    case B = 'B';
    case B_MINUS = 'B-';
    case C_PLUS = 'C+';
    case C = 'C';
    case C_MINUS = 'C-';
    case D_PLUS = 'D+';
    case D = 'D';
    case F = 'F';

    public function point(): float
    {
        return match ($this) {
            self::A_PLUS => 4.00,
            self::A => 4.00,
            self::A_MINUS => 3.70,
            self::B_PLUS => 3.30,
            self::B => 3.00,
            self::B_MINUS => 2.70,
            self::C_PLUS => 2.30,
            self::C => 2.00,
            self::C_MINUS => 1.70,
            self::D_PLUS => 1.30,
            self::D => 1.00,
            self::F => 0.00,
        };
    }

    public static function fromScore(float $score): self
    {
        return match (true) {
            $score >= 97 => self::A_PLUS,
            $score >= 93 => self::A,
            $score >= 90 => self::A_MINUS,
            $score >= 87 => self::B_PLUS,
            $score >= 83 => self::B,
            $score >= 80 => self::B_MINUS,
            $score >= 77 => self::C_PLUS,
            $score >= 73 => self::C,
            $score >= 70 => self::C_MINUS,
            $score >= 67 => self::D_PLUS,
            $score >= 60 => self::D,
            default => self::F,
        };
    }

    public function isPassing(): bool
    {
        return $this !== self::F;
    }
}
