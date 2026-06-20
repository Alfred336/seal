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

    public function test_api_posts_index_returns_correct_structure(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::query()->where('email', 'admin@sealtech.test')->firstOrFail();
        $token = $user->createToken('test-token')->plainTextToken;

        $category = \App\Models\Category::factory()->create(['name' => 'Engineering']);
        $tag = \App\Models\Tag::factory()->create(['name' => 'Architecture']);
        $post = \App\Models\Post::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => \App\Enums\PostStatus::Published,
            'published_at' => now(),
            'read_time' => '8 min',
            'image_path' => 'assets/images/blog/scalable-web-applications.webp',
        ]);
        $post->tags()->sync([$tag->id]);

        $response = $this->withToken($token)
            ->getJson(route('api.posts.index'))
            ->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'author',
                    'authorRole',
                    'authorInitials',
                    'authorColor',
                    'publishDate',
                    'category',
                    'categoryColor',
                    'readTime',
                    'excerpt',
                    'content',
                    'image_path',
                    'image_alt',
                    'image_gradient',
                    'image_icon',
                    'tags',
                ]
            ]
        ]);

        $firstPost = $response->json('data.0');
        $this->assertEquals($post->id, $firstPost['id']);
        $this->assertEquals($user->name, $firstPost['author']);
        $this->assertEquals('8 min read', $firstPost['readTime']);
        $this->assertEquals(['Architecture'], $firstPost['tags']);
        $this->assertEquals(asset('assets/images/blog/scalable-web-applications.webp'), $firstPost['image_path']);
        $this->assertEquals('linear-gradient(135deg, #7F1D1D 0%, #DC2626 60%, #F97316 100%)', $firstPost['image_gradient']);
        $this->assertEquals('<svg width="56" height="56" viewBox="0 0 56 56" fill="none"><path d="M20 24v-4a8 8 0 1 1 16 0v4" stroke="rgba(255,255,255,0.7)" stroke-width="2"/><rect x="16" y="24" width="24" height="18" rx="4" fill="rgba(255,255,255,0.22)"/></svg>', $firstPost['image_icon']);
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

    public function test_api_posts_index_supports_custom_pagination_page_size(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::query()->where('email', 'admin@sealtech.test')->firstOrFail();
        $token = $user->createToken('test-token')->plainTextToken;

        $category = \App\Models\Category::factory()->create();
        \App\Models\Post::factory(12)->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => \App\Enums\PostStatus::Published,
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->withToken($token)
            ->getJson(route('api.posts.index', ['per_page' => 5]))
            ->assertOk();

        $response->assertJsonCount(5, 'data');
        $this->assertEquals(5, $response->json('meta.per_page'));
        $this->assertEquals(12, $response->json('meta.total'));
    }

    public function test_api_posts_index_supports_disabling_pagination(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $user = User::query()->where('email', 'admin@sealtech.test')->firstOrFail();
        $token = $user->createToken('test-token')->plainTextToken;

        $category = \App\Models\Category::factory()->create();
        \App\Models\Post::factory(12)->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => \App\Enums\PostStatus::Published,
            'published_at' => now()->subDays(1),
        ]);

        $response = $this->withToken($token)
            ->getJson(route('api.posts.index', ['per_page' => 'all']))
            ->assertOk();

        $response->assertJsonCount(12, 'data');
        $this->assertArrayNotHasKey('meta', $response->json());
        $this->assertArrayNotHasKey('links', $response->json());
    }
}
