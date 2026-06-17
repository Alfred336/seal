<?php

namespace Tests\Feature\Foundation;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_routes_are_registered(): void
    {
        $this->getJson(route('api.posts.index'))
            ->assertOk();

        $this->getJson(route('api.services.index'))
            ->assertOk();

        $this->getJson(route('api.projects.index'))
            ->assertOk();
    }

    public function test_admin_can_access_manage_placeholder_routes(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::query()->where('email', 'admin@sealtech.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('manage.posts.index'))
            ->assertOk()
            ->assertSee('Blog Posts');

        $this->actingAs($admin)
            ->get(route('manage.users.index'))
            ->assertOk()
            ->assertSee('Users');
    }
}
