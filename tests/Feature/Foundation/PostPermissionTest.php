<?php

namespace Tests\Feature\Foundation;

use App\Enums\Permission;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_author_has_update_own_permission_for_own_post(): void
    {
        $author = User::factory()->create();
        $author->givePermissionTo(Permission::PostsUpdateOwn->value);

        $otherAuthor = User::factory()->create();
        $otherAuthor->givePermissionTo(Permission::PostsUpdateOwn->value);

        $ownPost = Post::factory()->for($author, 'author')->create();
        $otherPost = Post::factory()->for($otherAuthor, 'author')->create();

        $this->assertTrue($author->hasPermissionTo(Permission::PostsUpdateOwn->value));
        $this->assertTrue($ownPost->isOwnedBy($author));
        $this->assertFalse($otherPost->isOwnedBy($author));
    }

    public function test_manage_all_permission_allows_all_actions(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsManageAll->value);

        $this->assertTrue($user->hasPermissionTo(Permission::PostsManageAll->value));
    }

    public function test_author_cannot_publish_posts(): void
    {
        $author = User::factory()->create();
        $author->givePermissionTo(Permission::PostsUpdateOwn->value);

        $this->assertFalse($author->hasPermissionTo(Permission::PostsPublish->value));
    }
}
