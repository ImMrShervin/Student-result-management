<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transcripts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->string('verification_code', 64)->unique();
            $t->string('pdf_path')->nullable();
            $t->decimal('cumulative_gpa', 4, 2)->default(0);
            $t->unsignedSmallInteger('credits_earned')->default(0);
            $t->json('payload')->nullable()->comment('Snapshot of all courses/grades');
            $t->timestamp('generated_at')->nullable();
            $t->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('announcements', function (Blueprint $t) {
            $t->id();
            $t->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $t->string('title');
            $t->longText('body');
            $t->enum('audience', ['all', 'students', 'teachers', 'department', 'faculty'])->default('all');
            $t->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('faculty_id')->nullable()->constrained()->nullOnDelete();
            $t->timestamp('published_at')->nullable();
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('notifications', function (Blueprint $t) {
            $t->uuid('id')->primary();
            $t->string('type');
            $t->morphs('notifiable');
            $t->text('data');
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
        });

        Schema::create('personal_access_tokens', function (Blueprint $t) {
            $t->id();
            $t->morphs('tokenable');
            $t->string('name');
            $t->string('token', 64)->unique();
            $t->text('abilities')->nullable();
            $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });

        Schema::create('activity_log', function (Blueprint $t) {
            $t->id();
            $t->string('log_name')->nullable()->index();
            $t->text('description');
            $t->nullableMorphs('subject', 'subject');
            $t->nullableMorphs('causer', 'causer');
            $t->json('properties')->nullable();
            $t->uuid('batch_uuid')->nullable();
            $t->string('event')->nullable();
            $t->timestamps();
        });

        Schema::create('jobs', function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('queue')->index();
            $t->longText('payload');
            $t->unsignedTinyInteger('attempts');
            $t->unsignedInteger('reserved_at')->nullable();
            $t->unsignedInteger('available_at');
            $t->unsignedInteger('created_at');
        });

        Schema::create('failed_jobs', function (Blueprint $t) {
            $t->id();
            $t->string('uuid')->unique();
            $t->text('connection');
            $t->text('queue');
            $t->longText('payload');
            $t->longText('exception');
            $t->timestamp('failed_at')->useCurrent();
        });

        Schema::create('cache', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->mediumText('value');
            $t->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->string('owner');
            $t->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('transcripts');
    }
};
