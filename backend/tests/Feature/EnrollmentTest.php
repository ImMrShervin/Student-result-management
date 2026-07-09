<?php

declare(strict_types=1);

use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Services\EnrollmentService;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (UserRole::values() as $r) {
        Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
    }
});

function makeSection(): CourseSection
{
    $fac = Faculty::factory()->create();
    $dept = Department::factory()->create(['faculty_id' => $fac->id]);
    $course = Course::factory()->create(['department_id' => $dept->id]);
    $year = AcademicYear::create(['name' => '2026-2027', 'starts_on' => '2026-09-01', 'ends_on' => '2027-08-31', 'is_current' => true]);
    $sem = Semester::create(['academic_year_id' => $year->id, 'name' => 'Fall 2026', 'term' => 'fall', 'starts_on' => '2026-09-15', 'ends_on' => '2027-01-15', 'is_current' => true]);
    $teacherUser = User::factory()->create();
    $teacher = Teacher::factory()->create(['user_id' => $teacherUser->id, 'department_id' => $dept->id]);
    return CourseSection::create([
        'course_id' => $course->id, 'semester_id' => $sem->id,
        'teacher_id' => $teacher->id, 'section_code' => 'A', 'capacity' => 2,
    ]);
}

it('enrolls a student in an open section', function () {
    $section = makeSection();
    $u = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $u->id, 'department_id' => $section->course->department_id, 'faculty_id' => $section->course->department->faculty_id]);
    $e = app(EnrollmentService::class)->enroll($student, $section);
    expect($e->status)->toBe(EnrollmentStatus::PENDING);
    expect($section->fresh()->enrolled_count)->toBe(1);
});

it('rejects duplicate enrollment', function () {
    $section = makeSection();
    $u = User::factory()->create();
    $student = Student::factory()->create(['user_id' => $u->id, 'department_id' => $section->course->department_id, 'faculty_id' => $section->course->department->faculty_id]);
    app(EnrollmentService::class)->enroll($student, $section);
    app(EnrollmentService::class)->enroll($student, $section);
})->throws(\App\Exceptions\EnrollmentException::class);

it('rejects when capacity is full', function () {
    $section = makeSection();
    $section->update(['capacity' => 1]);
    for ($i = 0; $i < 2; $i++) {
        $u = User::factory()->create();
        $s = Student::factory()->create(['user_id' => $u->id, 'department_id' => $section->course->department_id, 'faculty_id' => $section->course->department->faculty_id]);
        app(EnrollmentService::class)->enroll($s, $section);
    }
})->throws(\App\Exceptions\EnrollmentException::class);
