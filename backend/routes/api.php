<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\CourseSectionController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\EnrollmentController;
use App\Http\Controllers\Api\V1\FacultyController;
use App\Http\Controllers\Api\V1\GradeController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\SemesterController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\TeacherController;
use App\Http\Controllers\Api\V1\TranscriptController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {

    Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('auth/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('auth/forgot-password', [AuthController::class, 'forgot'])->name('auth.forgot');
    Route::post('auth/reset-password', [AuthController::class, 'reset'])->name('auth.reset');
    Route::get('health', fn () => response()->json(['status' => 'ok', 'time' => now()->toIso8601String()]));

    Route::get('transcripts/verify/{code}', [TranscriptController::class, 'verify'])->name('transcripts.verify');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/change-password', [AuthController::class, 'changePassword']);

        Route::get('dashboard', [DashboardController::class, 'index']);

        Route::apiResource('users', UserController::class)->only(['index']);
        Route::patch('profile', [UserController::class, 'updateProfile']);
        Route::post('profile/avatar', [UserController::class, 'uploadAvatar']);
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive']);
        Route::post('users/{user}/role', [UserController::class, 'assignRole']);

        Route::apiResource('faculties', FacultyController::class);
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('students', StudentController::class);
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('course-sections', CourseSectionController::class)->except('show');

        Route::get('semesters', [SemesterController::class, 'index']);
        Route::get('semesters/current', [SemesterController::class, 'current']);
        Route::post('semesters', [SemesterController::class, 'store']);
        Route::get('academic-years', [SemesterController::class, 'years']);

        Route::get('enrollments', [EnrollmentController::class, 'index']);
        Route::post('enrollments', [EnrollmentController::class, 'store']);
        Route::post('enrollments/{enrollment}/approve', [EnrollmentController::class, 'approve']);
        Route::post('enrollments/{enrollment}/reject', [EnrollmentController::class, 'reject']);
        Route::post('enrollments/{enrollment}/withdraw', [EnrollmentController::class, 'withdraw']);

        Route::get('grades', [GradeController::class, 'index']);
        Route::put('enrollments/{enrollment}/grade', [GradeController::class, 'upsert']);
        Route::post('grades/{grade}/publish', [GradeController::class, 'publish']);

        Route::post('students/{student}/transcript', [TranscriptController::class, 'generate']);
        Route::get('transcripts/{transcript}/download', [TranscriptController::class, 'download'])->name('transcripts.download');

        Route::prefix('reports')->group(function () {
            Route::get('top-students', [ReportController::class, 'topStudents']);
            Route::get('failed-students', [ReportController::class, 'failedStudents']);
            Route::get('excellent-students', [ReportController::class, 'excellentStudents']);
            Route::get('average-gpa', [ReportController::class, 'averageGpa']);
            Route::get('grade-distribution', [ReportController::class, 'gradeDistribution']);
            Route::get('course-stats', [ReportController::class, 'courseStats']);
            Route::get('department-stats', [ReportController::class, 'departmentStats']);
            Route::get('enrollment-trend', [ReportController::class, 'enrollmentTrend']);
            Route::get('pass-vs-fail', [ReportController::class, 'passVsFail']);
        });

        Route::apiResource('announcements', AnnouncementController::class)->only(['index', 'store', 'destroy']);
    });
});
