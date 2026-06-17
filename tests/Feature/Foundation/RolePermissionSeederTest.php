<?php

namespace Tests\Feature\Foundation;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_roles_permissions_and_admin_user(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::query()->where('email', 'admin@sealtech.test')->first();

        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole(Role::Admin->value));
        $this->assertTrue($admin->can(Permission::UsersManage->value));

        $this->assertDatabaseHas('roles', ['name' => Role::Editor->value]);
        $this->assertDatabaseHas('roles', ['name' => Role::Author->value]);
        $this->assertDatabaseHas('roles', ['name' => Role::Support->value]);
        $this->assertDatabaseCount('permissions', count(Permission::cases()));
    }
}
