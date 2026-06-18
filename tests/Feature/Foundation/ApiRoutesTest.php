<?php

namespace Tests\Feature\Foundation;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_endpoint_returns_token(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::query()->where('email', 'admin@sealtech.test')->firstOrFail();

        $this->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
    }

    public function test_login_endpoint_rejects_invalid_credentials(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::query()->where('email', 'admin@sealtech.test')->firstOrFail();

        $this->postJson(route('api.login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertJson(['message' => 'Invalid credentials.']);
    }

    public function test_authenticated_api_routes_are_accessible(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::query()->where('email', 'admin@sealtech.test')->firstOrFail();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withToken($token)
            ->getJson(route('api.posts.index'))
            ->assertOk();

        $this->withToken($token)
            ->getJson(route('api.services.index'))
            ->assertOk();

        $this->withToken($token)
            ->getJson(route('api.projects.index'))
            ->assertOk();
    }

    public function test_unauthenticated_api_routes_return_401(): void
    {
        $this->getJson(route('api.posts.index'))
            ->assertUnauthorized();

        $this->getJson(route('api.services.index'))
            ->assertUnauthorized();

        $this->getJson(route('api.projects.index'))
            ->assertUnauthorized();
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
