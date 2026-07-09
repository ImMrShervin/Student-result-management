<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature', 'Unit');

function actingAsAdmin(): \App\Models\User
{
    $u = \App\Models\User::factory()->create();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $u->assignRole('admin');
    return test()->actingAs($u, 'sanctum')->getUser() ?? $u;
}
