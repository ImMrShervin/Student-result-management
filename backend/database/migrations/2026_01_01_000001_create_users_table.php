<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('first_name');
            $t->string('last_name');
            $t->string('email')->unique();
            $t->string('phone', 32)->nullable();
            $t->string('national_id', 32)->nullable()->unique();
            $t->string('code', 32)->nullable()->unique()->comment('Student # / Employee code');
            $t->string('avatar_path')->nullable();
            $t->enum('gender', ['male', 'female'])->nullable();
            $t->date('birth_date')->nullable();
            $t->string('address')->nullable();
            $t->timestamp('email_verified_at')->nullable();
            $t->string('password');
            $t->boolean('is_active')->default(true);
            $t->string('locale', 5)->default('en');
            $t->rememberToken();
            $t->timestamp('last_login_at')->nullable();
            $t->softDeletes();
            $t->timestamps();

            $t->index(['is_active']);
            $t->index(['first_name', 'last_name']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $t) {
            $t->string('email')->primary();
            $t->string('token');
            $t->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->foreignId('user_id')->nullable()->index();
            $t->string('ip_address', 45)->nullable();
            $t->text('user_agent')->nullable();
            $t->longText('payload');
            $t->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
