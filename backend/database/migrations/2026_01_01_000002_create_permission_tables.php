<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        Schema::create($tableNames['permissions'], function (Blueprint $t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('guard_name');
            $t->timestamps();
            $t->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $t) use ($teams, $columnNames) {
            $t->bigIncrements('id');
            if ($teams) {
                $t->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $t->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $t->string('name');
            $t->string('guard_name');
            $t->timestamps();
            if ($teams) {
                $t->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $t->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $t) use ($tableNames, $columnNames) {
            $t->unsignedBigInteger('permission_id');
            $t->string('model_type');
            $t->unsignedBigInteger($columnNames['model_morph_key']);
            $t->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');
            $t->foreign('permission_id')->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
            $t->primary(['permission_id', $columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_permission_model_type_primary');
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $t) use ($tableNames, $columnNames) {
            $t->unsignedBigInteger('role_id');
            $t->string('model_type');
            $t->unsignedBigInteger($columnNames['model_morph_key']);
            $t->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');
            $t->foreign('role_id')->references('id')->on($tableNames['roles'])->cascadeOnDelete();
            $t->primary(['role_id', $columnNames['model_morph_key'], 'model_type'], 'model_has_roles_role_model_type_primary');
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $t) use ($tableNames) {
            $t->unsignedBigInteger('permission_id');
            $t->unsignedBigInteger('role_id');
            $t->foreign('permission_id')->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
            $t->foreign('role_id')->references('id')->on($tableNames['roles'])->cascadeOnDelete();
            $t->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');
        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
