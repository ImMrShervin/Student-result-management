<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('code', 16)->unique();
            $t->text('description')->nullable();
            $t->foreignId('dean_id')->nullable()->constrained('users')->nullOnDelete();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('departments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $t->foreignId('head_id')->nullable()->constrained('users')->nullOnDelete();
            $t->string('name');
            $t->string('code', 16)->unique();
            $t->text('description')->nullable();
            $t->softDeletes();
            $t->timestamps();
            $t->index('faculty_id');
        });

        Schema::create('academic_years', function (Blueprint $t) {
            $t->id();
            $t->string('name', 32)->unique()->comment('e.g. 2026-2027');
            $t->date('starts_on');
            $t->date('ends_on');
            $t->boolean('is_current')->default(false);
            $t->timestamps();
        });

        Schema::create('semesters', function (Blueprint $t) {
            $t->id();
            $t->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $t->string('name', 64)->comment('e.g. Fall 2026');
            $t->enum('term', ['fall', 'spring', 'summer']);
            $t->date('starts_on');
            $t->date('ends_on');
            $t->date('enrollment_starts_on')->nullable();
            $t->date('enrollment_ends_on')->nullable();
            $t->boolean('is_current')->default(false);
            $t->boolean('grades_published')->default(false);
            $t->softDeletes();
            $t->timestamps();
            $t->unique(['academic_year_id', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('faculties');
    }
};
