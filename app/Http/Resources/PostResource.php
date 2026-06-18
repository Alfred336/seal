<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'title' => $this->title,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'read_time' => $this->read_time,
            'featured' => $this->featured,
            'published_at' => $this->published_at?->toISOString(),
            'image_path' => $this->image_path,
            'image_alt' => $this->image_alt,
            'image_gradient' => $this->image_gradient,
            'image_icon' => $this->image_icon,
            'author' => $this->whenLoaded('author', fn () => [
                'name' => $this->author->name,
                'initials' => $this->author->displayInitials(),
                'color' => $this->author->color,
            ]),
            'category' => $this->whenLoaded('category', fn () => [
                'name' => $this->category->name,
                'slug' => $this->category->slug,
                'color' => $this->category->color,
            ]),
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->map(fn ($tag) => [
                'name' => $tag->name,
                'slug' => $tag->slug,
            ])),
        ];
    }
}
