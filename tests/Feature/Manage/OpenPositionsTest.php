<?php

namespace Tests\Feature\Manage;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Models\OpenPosition;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OpenPositionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_unauthorized_user_cannot_access_careers_index(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('manage.open-positions.index'))
            ->assertStatus(403);
    }

    public function test_authorized_user_can_access_careers_index(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::CareersManage->value);
        $this->actingAs($user);

        $this->get(route('manage.open-positions.index'))
            ->assertStatus(200);
    }

    public function test_can_create_open_position(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::CareersManage->value);
        $this->actingAs($user);

        Livewire::test('manage.open-positions')
            ->set('title', 'Backend Engineer')
            ->set('slug', 'backend-engineer')
            ->set('type', 'Full-time')
            ->set('location', 'Remote')
            ->set('tech_stack', 'Laravel · MySQL')
            ->set('description', 'Join our backend development team.')
            ->set('status', 'draft')
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('open_positions', [
            'title' => 'Backend Engineer',
            'slug' => 'backend-engineer',
            'status' => 'draft',
        ]);
    }

    public function test_can_edit_open_position(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::CareersManage->value);
        $this->actingAs($user);

        $job = OpenPosition::create([
            'title' => 'Flutter Developer',
            'slug' => 'flutter-developer',
            'type' => 'Full-time',
            'location' => 'Dar es Salaam',
            'description' => 'Flutter developer role.',
            'status' => PostStatus::Draft,
        ]);

        Livewire::test('manage.open-positions')
            ->call('startEdit', $job->id)
            ->set('title', 'Senior Flutter Developer')
            ->set('status', 'published')
            ->call('saveEdit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('open_positions', [
            'id' => $job->id,
            'title' => 'Senior Flutter Developer',
            'status' => 'published',
        ]);
    }

    public function test_can_delete_open_position(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::CareersManage->value);
        $this->actingAs($user);

        $job = OpenPosition::create([
            'title' => 'QA Engineer',
            'slug' => 'qa-engineer',
            'type' => 'Full-time',
            'location' => 'Remote',
            'description' => 'QA description.',
            'status' => PostStatus::Published,
        ]);

        Livewire::test('manage.open-positions')
            ->call('delete', $job->id);

        $this->assertDatabaseMissing('open_positions', [
            'id' => $job->id,
        ]);
    }

    public function test_api_returns_only_published_jobs(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = $admin->createToken('test-token')->plainTextToken;

        OpenPosition::create([
            'title' => 'Published Job',
            'slug' => 'published-job',
            'type' => 'Full-time',
            'location' => 'Remote',
            'description' => 'Description.',
            'status' => PostStatus::Published,
        ]);

        OpenPosition::create([
            'title' => 'Draft Job',
            'slug' => 'draft-job',
            'type' => 'Full-time',
            'location' => 'Remote',
            'description' => 'Description.',
            'status' => PostStatus::Draft,
        ]);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson(route('api.jobs.index'))
            ->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Published Job');
    }

    public function test_api_shows_single_published_job(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = $admin->createToken('test-token')->plainTextToken;

        $job = OpenPosition::create([
            'title' => 'Published Job',
            'slug' => 'published-job',
            'type' => 'Full-time',
            'location' => 'Remote',
            'description' => 'Description.',
            'status' => PostStatus::Published,
        ]);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson(route('api.jobs.show', 'published-job'))
            ->assertStatus(200)
            ->assertJsonPath('data.title', 'Published Job');
    }

    public function test_api_does_not_show_draft_job(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = $admin->createToken('test-token')->plainTextToken;

        OpenPosition::create([
            'title' => 'Draft Job',
            'slug' => 'draft-job',
            'type' => 'Full-time',
            'location' => 'Remote',
            'description' => 'Description.',
            'status' => PostStatus::Draft,
        ]);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson(route('api.jobs.show', 'draft-job'))
            ->assertStatus(404);
    }
}
