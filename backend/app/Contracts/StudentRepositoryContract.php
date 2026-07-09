<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StudentRepositoryContract
{
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function findByNumber(string $number): ?Student;

    public function topStudents(int $limit = 10): \Illuminate\Support\Collection;

    public function averageGpaByDepartment(): \Illuminate\Support\Collection;
}
