<?php

namespace Tests\Feature\Manage;

use App\Enums\Permission;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_unauthorized_user_cannot_delete_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user);

        Livewire::test('manage.users')
            ->call('delete', $otherUser->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $otherUser->id]);
    }

    public function test_authorized_user_can_delete_user(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::UsersManage->value);
        $this->actingAs($user);

        $otherUser = User::factory()->create();

        Livewire::test('manage.users')
            ->call('delete', $otherUser->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);
    }

    public function test_user_cannot_delete_themselves(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::UsersManage->value);
        $this->actingAs($user);

        Livewire::test('manage.users')
            ->call('delete', $user->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
