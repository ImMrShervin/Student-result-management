<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('department_id')->constrained()->cascadeOnDelete();
            $t->string('employee_code', 32)->unique();
            $t->string('office', 64)->nullable();
            $t->enum('academic_rank', [
                'assistant_professor', 'associate_professor', 'professor',
                'lecturer', 'instructor', 'adjunct',
            ])->default('lecturer');
            $t->date('hired_on')->nullable();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('students', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $t->foreignId('department_id')->constrained()->cascadeOnDelete();
            $t->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $t->string('student_number', 32)->unique();
            $t->unsignedSmallInteger('entry_year');
            $t->unsignedTinyInteger('current_semester')->default(1);
            $t->decimal('current_gpa', 4, 2)->default(0);
            $t->decimal('cumulative_gpa', 4, 2)->default(0);
            $t->unsignedSmallInteger('credits_passed')->default(0);
            $t->unsignedSmallInteger('credits_required')->default(140);
            $t->string('academic_status', 32)->default('passed');
            $t->softDeletes();
            $t->timestamps();

            $t->index(['department_id', 'academic_status']);
            $t->index('entry_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('teachers');
    }
};
