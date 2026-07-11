<?php

namespace Tests\Feature\Manage;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Enums\SubscriptionStatus;
use App\Mail\NewPostPublished;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PostNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Mail::fake();
    }

    public function test_new_post_published_notifies_active_subscribers(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsCreate->value);
        $this->actingAs($user);

        // Active subscriber
        $activeSub = Subscription::factory()->create([
            'email' => 'active@example.com',
            'status' => SubscriptionStatus::Active,
        ]);

        // Inactive subscriber
        $inactiveSub = Subscription::factory()->create([
            'email' => 'inactive@example.com',
            'status' => SubscriptionStatus::Unsubscribed,
        ]);

        Livewire::test('manage.post-form')
            ->set('title', 'My Published Post')
            ->set('slug', 'my-published-post')
            ->set('status', 'published')
            ->call('save')
            ->assertHasNoErrors();

        Mail::assertQueued(NewPostPublished::class, function (NewPostPublished $mail) use ($activeSub) {
            $mail->assertSeeInHtml('https://sealtech.co.tz/my-published-post');
            return $mail->hasTo('active@example.com');
        });

        Mail::assertNotQueued(NewPostPublished::class, function ($mail) use ($inactiveSub) {
            return $mail->hasTo('inactive@example.com');
        });
    }

    public function test_new_post_draft_does_not_notify_subscribers(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsCreate->value);
        $this->actingAs($user);

        Subscription::factory()->create([
            'email' => 'active@example.com',
            'status' => SubscriptionStatus::Active,
        ]);

        Livewire::test('manage.post-form')
            ->set('title', 'My Draft Post')
            ->set('slug', 'my-draft-post')
            ->set('status', 'draft')
            ->call('save')
            ->assertHasNoErrors();

        Mail::assertNothingQueued();
    }

    public function test_transition_from_draft_to_published_notifies_subscribers(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsUpdateOwn->value);
        $this->actingAs($user);

        $post = Post::factory()->create([
            'author_id' => $user->id,
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'status' => PostStatus::Draft,
        ]);

        Subscription::factory()->create([
            'email' => 'active@example.com',
            'status' => SubscriptionStatus::Active,
        ]);

        Livewire::test('manage.post-form', ['post' => $post])
            ->set('status', 'published')
            ->call('save')
            ->assertHasNoErrors();

        Mail::assertQueued(NewPostPublished::class, 1);
        Mail::assertQueued(NewPostPublished::class, function (NewPostPublished $mail) {
            $mail->assertSeeInHtml('https://sealtech.co.tz/draft-post');
            return $mail->hasTo('active@example.com');
        });
    }

    public function test_updating_already_published_post_does_not_notify_subscribers(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsUpdateOwn->value);
        $this->actingAs($user);

        $post = Post::factory()->create([
            'author_id' => $user->id,
            'title' => 'Published Post',
            'slug' => 'published-post',
            'status' => PostStatus::Published,
            'published_at' => now(),
        ]);

        Subscription::factory()->create([
            'email' => 'active@example.com',
            'status' => SubscriptionStatus::Active,
        ]);

        Livewire::test('manage.post-form', ['post' => $post])
            ->set('title', 'Updated Title')
            ->call('save')
            ->assertHasNoErrors();

        Mail::assertNothingQueued();
    }
}
