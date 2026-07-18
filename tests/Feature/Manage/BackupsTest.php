<?php

namespace Tests\Feature\Manage;

use App\Enums\Permission;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BackupsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_unauthorized_user_cannot_access_backups_page(): void
    {
        $user = User::factory()->create();
        // Do not give backup permissions
        $this->actingAs($user);

        $this->get(route('manage.backups.index'))
            ->assertStatus(403);
    }

    public function test_unauthorized_user_cannot_mount_backups_component(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('manage.backups')
            ->assertStatus(403);
    }

    public function test_authorized_user_can_access_backups_page(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::BackupsManage->value);
        $this->actingAs($user);

        $this->get(route('manage.backups.index'))
            ->assertStatus(200)
            ->assertSeeLivewire('manage.backups');
    }

    public function test_authorized_user_can_mount_backups_component(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::BackupsManage->value);
        $this->actingAs($user);

        Livewire::test('manage.backups')
            ->assertOk()
            ->assertViewHas('backupFiles');
    }

    public function test_backup_notifiable_routes_email_to_all_admin_users(): void
    {
        $admin1 = User::factory()->create();
        $admin1->assignRole(\App\Enums\Role::Admin->value);

        $admin2 = User::factory()->create();
        $admin2->assignRole(\App\Enums\Role::Admin->value);

        // A non-admin user
        User::factory()->create();

        $notifiable = new \App\Notifications\BackupNotifiable();
        $emails = $notifiable->routeNotificationForMail();

        $this->assertIsArray($emails);
        $this->assertContains($admin1->email, $emails);
        $this->assertContains($admin2->email, $emails);
        // Default seeded admin
        $this->assertContains('admin@sealtech.test', $emails);
        $this->assertCount(3, $emails);
    }

    public function test_backup_notification_contains_user_name_in_greeting(): void
    {
        $admin = User::factory()->create([
            'name' => 'John Doe Admin',
        ]);
        $admin->assignRole(\App\Enums\Role::Admin->value);

        $event = new \Spatie\Backup\Events\BackupWasSuccessful('local', 'sealCMS');
        $notification = new \App\Notifications\Backup\BackupWasSuccessfulNotification($event);

        $mailMessage = $notification->toMail($admin);

        $this->assertEquals('Hello, John Doe Admin!', $mailMessage->greeting);
    }
}
