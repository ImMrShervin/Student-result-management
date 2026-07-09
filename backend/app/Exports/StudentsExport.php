<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private readonly array $filters = []) {}

    public function collection()
    {
        return Student::with(['user', 'department.faculty'])
            ->when($this->filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->get();
    }

    public function headings(): array
    {
        return ['ID', 'Student #', 'Name', 'Email', 'Department', 'Faculty', 'Entry Year', 'GPA', 'Credits', 'Status'];
    }

    public function map($s): array
    {
        return [
            $s->id, $s->student_number, $s->user->full_name, $s->user->email,
            $s->department->name, $s->department->faculty->name,
            $s->entry_year, $s->cumulative_gpa, $s->credits_passed,
            $s->academic_status?->value,
        ];
    }
}
