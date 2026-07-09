<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $t->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $t->string('status', 16)->default('pending');
            $t->timestamp('enrolled_at')->useCurrent();
            $t->timestamp('decided_at')->nullable();
            $t->softDeletes();
            $t->timestamps();
            $t->unique(['student_id', 'course_section_id']);
            $t->index(['semester_id', 'status']);
        });

        Schema::create('grades', function (Blueprint $t) {
            $t->id();
            $t->foreignId('enrollment_id')->unique()->constrained()->cascadeOnDelete();
            $t->decimal('attendance', 5, 2)->default(0);
            $t->decimal('assignment', 5, 2)->default(0);
            $t->decimal('quiz', 5, 2)->default(0);
            $t->decimal('project', 5, 2)->default(0);
            $t->decimal('midterm', 5, 2)->default(0);
            $t->decimal('practical', 5, 2)->default(0);
            $t->decimal('final_exam', 5, 2)->default(0);
            $t->decimal('total_score', 5, 2)->nullable();
            $t->string('letter_grade', 4)->nullable();
            $t->decimal('gpa_points', 3, 2)->nullable();
            $t->boolean('is_published')->default(false);
            $t->timestamp('published_at')->nullable();
            $t->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('semester_gpas', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $t->decimal('semester_gpa', 4, 2)->default(0);
            $t->decimal('cumulative_gpa', 4, 2)->default(0);
            $t->unsignedSmallInteger('credits_attempted')->default(0);
            $t->unsignedSmallInteger('credits_earned')->default(0);
            $t->string('academic_status', 32)->default('passed');
            $t->timestamps();
            $t->unique(['student_id', 'semester_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semester_gpas');
        Schema::dropIfExists('grades');
        Schema::dropIfExists('enrollments');
    }
};
