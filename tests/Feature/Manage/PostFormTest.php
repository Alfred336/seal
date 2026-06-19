<?php

namespace Tests\Feature\Manage;

use App\Enums\Permission;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_unauthenticated_user_cannot_access_post_form(): void
    {
        $this->get(route('manage.posts.create'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_post_form(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('manage.posts.create'))->assertStatus(403);
    }

    public function test_authorized_user_can_render_create_post_form(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsCreate->value);
        $this->actingAs($user);

        $this->get(route('manage.posts.create'))
            ->assertOk()
            ->assertSeeLivewire('manage.post-form');
    }

    public function test_authorized_user_can_create_post(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsCreate->value);
        $this->actingAs($user);

        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        Livewire::test('manage.post-form')
            ->set('title', 'My New Post')
            ->set('slug', 'my-new-post')
            ->set('content', '<p>Some awesome content</p>')
            ->set('category_id', $category->id)
            ->set('tag_ids', [$tag->id])
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('manage.posts.edit', Post::first()));

        $this->assertDatabaseHas('posts', [
            'title' => 'My New Post',
            'slug' => 'my-new-post',
            'category_id' => $category->id,
            'author_id' => $user->id,
        ]);

        $this->assertDatabaseHas('post_tags', [
            'tag_id' => $tag->id,
        ]);
    }

    public function test_user_can_update_own_post(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::PostsUpdateOwn->value);
        $this->actingAs($user);

        $post = Post::factory()->for($user, 'author')->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        Livewire::test('manage.post-form', ['post' => $post])
            ->set('title', 'Updated Title')
            ->set('slug', 'updated-title')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
        ]);
    }
}
