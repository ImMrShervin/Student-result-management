<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Enums\EnrollmentStatus;
use App\Enums\LetterGrade;
use App\Services\GpaCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (UserRole::values() as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $super = User::firstOrCreate(
            ['email' => 'admin@srms.local'],
            ['first_name' => 'Super', 'last_name' => 'Admin', 'password' => Hash::make('password'), 'is_active' => true, 'email_verified_at' => now()]
        );
        $super->syncRoles([UserRole::SUPER_ADMIN->value]);

        $adminUser = User::firstOrCreate(
            ['email' => 'staff@srms.local'],
            ['first_name' => 'Staff', 'last_name' => 'Admin', 'password' => Hash::make('password'), 'is_active' => true, 'email_verified_at' => now()]
        );
        $adminUser->syncRoles([UserRole::ADMIN->value]);

        $facultyNames = ['Engineering', 'Science', 'Medical', 'Business'];
        $faculties = collect($facultyNames)->map(fn ($n) => Faculty::firstOrCreate(
            ['code' => strtoupper(substr($n, 0, 3))],
            ['name' => "Faculty of {$n}", 'description' => "The Faculty of {$n}"]
        ));

        $deptSpecs = [
            'ENG' => ['Computer Engineering', 'Electrical Engineering', 'Civil Engineering', 'Mechanical Engineering'],
            'SCI' => ['Mathematics', 'Physics', 'Chemistry'],
            'MED' => ['Medicine', 'Nursing', 'Pharmacy', 'Dentistry'],
            'BUS' => ['Business Administration', 'Accounting', 'Economics', 'Marketing'],
        ];

        $depts = collect();
        foreach ($deptSpecs as $facCode => $names) {
            $fac = $faculties->firstWhere('code', $facCode);
            foreach ($names as $i => $name) {
                $depts->push(Department::firstOrCreate(
                    ['code' => $facCode . '-' . ($i + 1)],
                    ['faculty_id' => $fac->id, 'name' => $name, 'description' => "Department of {$name}"]
                ));
            }
        }

        $year = AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            ['starts_on' => '2026-09-01', 'ends_on' => '2027-08-31', 'is_current' => true]
        );
        $fall = Semester::firstOrCreate(
            ['academic_year_id' => $year->id, 'term' => 'fall'],
            ['name' => 'Fall 2026', 'starts_on' => '2026-09-15', 'ends_on' => '2027-01-15', 'is_current' => true]
        );
        $spring = Semester::firstOrCreate(
            ['academic_year_id' => $year->id, 'term' => 'spring'],
            ['name' => 'Spring 2027', 'starts_on' => '2027-02-01', 'ends_on' => '2027-06-15']
        );

        $teachers = collect();
        for ($i = 0; $i < 40; $i++) {
            $dept = $depts->random();
            $u = User::factory()->create();
            $u->syncRoles([UserRole::TEACHER->value]);
            $teachers->push(Teacher::factory()->create(['user_id' => $u->id, 'department_id' => $dept->id]));
        }

        $courses = collect();
        for ($i = 0; $i < 80; $i++) {
            $courses->push(Course::factory()->create(['department_id' => $depts->random()->id]));
        }

        $sections = collect();
        foreach ($courses as $c) {
            foreach ([$fall, $spring] as $sem) {
                $sections->push(CourseSection::create([
                    'course_id' => $c->id,
                    'semester_id' => $sem->id,
                    'teacher_id' => $teachers->random()->id,
                    'section_code' => 'A',
                    'capacity' => 40,
                    'schedule' => 'Mon/Wed 10:00-11:30',
                    'room' => 'R' . random_int(100, 599),
                ]));
            }
        }

        $students = collect();
        for ($i = 0; $i < 300; $i++) {
            $dept = $depts->random();
            $u = User::factory()->create();
            $u->syncRoles([UserRole::STUDENT->value]);
            $students->push(Student::factory()->create([
                'user_id' => $u->id,
                'department_id' => $dept->id,
                'faculty_id' => $dept->faculty_id,
            ]));
        }

        $enrollments = collect();
        for ($i = 0; $i < 500; $i++) {
            $student = $students->random();
            $section = $sections->random();
            $exists = Enrollment::where('student_id', $student->id)->where('course_section_id', $section->id)->exists();
            if ($exists) continue;
            $enrollments->push(Enrollment::create([
                'student_id' => $student->id,
                'course_section_id' => $section->id,
                'semester_id' => $section->semester_id,
                'status' => EnrollmentStatus::APPROVED->value,
                'enrolled_at' => now(),
                'decided_at' => now(),
            ]));
            $section->increment('enrolled_count');
        }

        $targetGrades = min(3000, $enrollments->count() * 6);
        $gradeCount = 0;
        foreach ($enrollments as $e) {
            $rounds = intdiv($targetGrades, $enrollments->count());
            for ($k = 0; $k <= $rounds && $gradeCount < $targetGrades; $k++) {
                if (Grade::where('enrollment_id', $e->id)->exists()) break;
                $components = [
                    'attendance' => random_int(60, 100),
                    'assignment' => random_int(50, 100),
                    'quiz' => random_int(40, 100),
                    'project' => random_int(50, 100),
                    'midterm' => random_int(40, 100),
                    'practical' => random_int(50, 100),
                    'final_exam' => random_int(35, 100),
                ];
                $weights = ['attendance'=>5,'assignment'=>10,'quiz'=>10,'project'=>10,'midterm'=>20,'practical'=>10,'final_exam'=>35];
                $sum = 0; $w = 0;
                foreach ($weights as $k2=>$v2) { $sum += $components[$k2]*$v2; $w += $v2; }
                $score = round($sum / $w, 2);
                $letter = LetterGrade::fromScore($score);
                Grade::create($components + [
                    'enrollment_id' => $e->id,
                    'total_score' => $score,
                    'letter_grade' => $letter->value,
                    'gpa_points' => $letter->point(),
                    'is_published' => true,
                    'published_at' => now(),
                    'graded_by' => $super->id,
                ]);
                $gradeCount++;
                break;
            }
        }

        $gpa = app(GpaCalculator::class);
        foreach ($students as $s) {
            foreach ([$fall, $spring] as $sem) {
                $gpa->snapshot($s, $sem);
            }
        }

        $demoDept = $depts->first();
        $this->makeDemoTeacher($demoDept);
        $this->makeDemoStudent($demoDept);

        $this->command->info("Seed complete: {$students->count()} students, {$teachers->count()} teachers, {$courses->count()} courses, {$enrollments->count()} enrollments, {$gradeCount} grades.");
    }

    private function makeDemoTeacher($dept): void
    {
        $u = User::firstOrCreate(
            ['email' => 'teacher@srms.local'],
            ['first_name' => 'Demo', 'last_name' => 'Teacher', 'password' => Hash::make('password'), 'is_active' => true, 'email_verified_at' => now()]
        );
        $u->syncRoles([UserRole::TEACHER->value]);
        Teacher::firstOrCreate(['user_id' => $u->id], [
            'department_id' => $dept->id,
            'employee_code' => 'E00001',
            'academic_rank' => 'associate_professor',
            'office' => 'Room 101',
        ]);
    }

    private function makeDemoStudent($dept): void
    {
        $u = User::firstOrCreate(
            ['email' => 'student@srms.local'],
            ['first_name' => 'Demo', 'last_name' => 'Student', 'password' => Hash::make('password'), 'is_active' => true, 'email_verified_at' => now()]
        );
        $u->syncRoles([UserRole::STUDENT->value]);
        Student::firstOrCreate(['user_id' => $u->id], [
            'department_id' => $dept->id,
            'faculty_id' => $dept->faculty_id,
            'student_number' => 'S2026000001',
            'entry_year' => 2026,
        ]);
    }
}
