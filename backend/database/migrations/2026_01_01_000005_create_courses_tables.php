<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('department_id')->constrained()->cascadeOnDelete();
            $t->string('code', 16)->unique();
            $t->string('title');
            $t->text('description')->nullable();
            $t->unsignedTinyInteger('theory_credit')->default(3);
            $t->unsignedTinyInteger('practical_credit')->default(0);
            $t->unsignedTinyInteger('total_credit')->virtualAs('theory_credit + practical_credit');
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('course_prerequisites', function (Blueprint $t) {
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('prerequisite_id')->constrained('courses')->cascadeOnDelete();
            $t->primary(['course_id', 'prerequisite_id']);
        });

        Schema::create('course_sections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('course_id')->constrained()->cascadeOnDelete();
            $t->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $t->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $t->string('section_code', 8)->default('A');
            $t->unsignedSmallInteger('capacity')->default(40);
            $t->unsignedSmallInteger('enrolled_count')->default(0);
            $t->string('schedule')->nullable()->comment('e.g. Mon/Wed 10:00-11:30');
            $t->string('room', 32)->nullable();
            $t->softDeletes();
            $t->timestamps();
            $t->unique(['course_id', 'semester_id', 'section_code']);
            $t->index('teacher_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_sections');
        Schema::dropIfExists('course_prerequisites');
        Schema::dropIfExists('courses');
    }
};
