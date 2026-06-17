<?php

namespace Tests\Feature\Foundation;

use App\Enums\PostStatus;
use App\Enums\Role;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_author_can_update_own_post_but_not_others(): void
    {
        $author = User::factory()->create();
        $author->assignRole(Role::Author->value);

        $otherAuthor = User::factory()->create();
        $otherAuthor->assignRole(Role::Author->value);

        $ownPost = Post::factory()->for($author, 'author')->create();
        $otherPost = Post::factory()->for($otherAuthor, 'author')->create();

        $this->assertTrue($author->can('update', $ownPost));
        $this->assertFalse($author->can('update', $otherPost));
    }

    public function test_editor_can_manage_all_posts(): void
    {
        $editor = User::factory()->create();
        $editor->assignRole(Role::Editor->value);

        $post = Post::factory()->create();

        $this->assertTrue($editor->can('update', $post));
        $this->assertTrue($editor->can('delete', $post));
        $this->assertTrue($editor->can('publish', $post));
    }

    public function test_author_cannot_publish_posts(): void
    {
        $author = User::factory()->create();
        $author->assignRole(Role::Author->value);

        $post = Post::factory()->for($author, 'author')->create([
            'status' => PostStatus::Draft,
        ]);

        $this->assertFalse($author->can('publish', $post));
    }
}
