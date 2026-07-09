<?php

declare(strict_types=1);

namespace App\Imports;

use App\Enums\UserRole;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToCollection, WithHeadingRow, WithValidation
{
    public int $imported = 0;
    public array $errors = [];

    public function collection($rows): void
    {
        foreach ($rows as $i => $row) {
            try {
                $dept = Department::where('code', $row['department_code'])->firstOrFail();
                $user = User::create([
                    'first_name' => $row['first_name'],
                    'last_name'  => $row['last_name'],
                    'email'      => $row['email'],
                    'phone'      => $row['phone'] ?? null,
                    'code'       => $row['student_number'],
                    'password'   => Hash::make($row['student_number']),
                    'is_active'  => true,
                ]);
                $user->assignRole(UserRole::STUDENT->value);
                Student::create([
                    'user_id' => $user->id,
                    'department_id' => $dept->id,
                    'faculty_id' => $dept->faculty_id,
                    'student_number' => $row['student_number'],
                    'entry_year' => $row['entry_year'],
                ]);
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = ['row' => $i + 2, 'error' => $e->getMessage()];
            }
        }
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'student_number' => 'required|string|unique:students,student_number',
            'entry_year' => 'required|integer',
            'department_code' => 'required|exists:departments,code',
        ];
    }
}
