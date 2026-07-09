<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

it('registers a new student and returns token', function () {
    Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
    $res = $this->postJson('/api/v1/auth/register', [
        'first_name' => 'Alice', 'last_name' => 'Doe',
        'email' => 'alice@test.local',
        'password' => 'password123', 'password_confirmation' => 'password123',
    ]);
    $res->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'email']]);
});

it('logs in with valid credentials', function () {
    $u = User::factory()->create(['email' => 'bob@test.local', 'password' => Hash::make('secret1234')]);
    $res = $this->postJson('/api/v1/auth/login', ['email' => 'bob@test.local', 'password' => 'secret1234']);
    $res->assertOk()->assertJsonStructure(['token', 'user']);
});

it('rejects wrong credentials', function () {
    User::factory()->create(['email' => 'c@test.local', 'password' => Hash::make('right')]);
    $this->postJson('/api/v1/auth/login', ['email' => 'c@test.local', 'password' => 'wrong'])
        ->assertUnprocessable();
});
