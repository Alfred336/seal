<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $editor = User::query()->where('email', 'admin@sealtech.test')->first();

        if (! $editor) {
            return;
        }

        Category::factory(5)->create();
        Tag::factory(10)->create();
        Service::factory(6)->create();
        Project::factory(8)->create();
        User::factory(3)->create(['email_verified_at' => now()]);

        $categories = Category::all();
        $tags = Tag::all();

        Post::factory(15)->create([
            'author_id' => $editor->id,
            'category_id' => $categories->random()->id,
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ])->each(function (Post $post) use ($tags) {
            $post->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
        });
    }
}
