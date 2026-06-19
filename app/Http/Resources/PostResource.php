<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Extract tag names as a flat array of strings
        $tags = $this->relationLoaded('tags')
            ? $this->tags->pluck('name')->all()
            : [];

        // Format publishDate to Y-m-d
        $publishDate = $this->published_at?->format('Y-m-d');

        // Extract and format readTime (e.g. "8 min read")
        $readTime = null;
        if ($this->read_time) {
            preg_match('/\d+/', $this->read_time, $matches);
            if (!empty($matches[0])) {
                $readTime = $matches[0] . ' min read';
            } else {
                $readTime = $this->read_time . ' min read';
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => $this->author?->name,
            'authorRole' => $this->author?->role,
            'authorInitials' => $this->author?->displayInitials(),
            'authorColor' => $this->author?->color,
            'publishDate' => $publishDate,
            'category' => $this->category?->name,
            'categoryColor' => $this->category?->color,
            'readTime' => $readTime,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image' => $this->image_path,
            'imageAlt' => $this->image_alt,
            'imageGradient' => $this->image_gradient,
            'imageIcon' => $this->image_icon,
            'tags' => $tags,
        ];
    }
}
