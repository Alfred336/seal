<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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

        $fullImageUrl = $this->image_path
            ? (filter_var($this->image_path, FILTER_VALIDATE_URL)
                ? $this->image_path
                : (str_starts_with($this->image_path, 'assets/')
                    ? asset($this->image_path)
                    : (str_starts_with($this->image_path, 'storage/')
                        ? asset($this->image_path)
                        : asset('storage/' . $this->image_path))))
            : null;

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => $this->author?->name,
            'authorRole' => $this->author?->role,
            'authorInitials' => $this->author?->displayInitials(),
            'authorColor' => $this->author?->color,
            'publishDate' => $publishDate,
            'category' => $this->category ? [
                'id'    => $this->category->id,
                'name'  => $this->category->name,
                'slug'  => $this->category->slug,
                'color' => $this->category->color,
            ] : null,
            'readTime' => $readTime,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'image_path' => $fullImageUrl,
            'image_alt' => $this->image_alt,
            'image_gradient' => $this->image_gradient,
            'image_icon' => $this->image_icon,
            'tags' => $tags,
            'categoryColor' => $this->category?->color,
        ];
    }
}
