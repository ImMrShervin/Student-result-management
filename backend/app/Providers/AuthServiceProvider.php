<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Transcript;
use App\Policies\CoursePolicy;
use App\Policies\DepartmentPolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\FacultyPolicy;
use App\Policies\GradePolicy;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use App\Policies\TranscriptPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Student::class => StudentPolicy::class,
        Teacher::class => TeacherPolicy::class,
        Faculty::class => FacultyPolicy::class,
        Department::class => DepartmentPolicy::class,
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Grade::class => GradePolicy::class,
        Transcript::class => TranscriptPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
