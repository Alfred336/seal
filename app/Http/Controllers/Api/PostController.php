<?php

namespace App\Http\Controllers\Api;

use App\Enums\Permission;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostController extends ApiController
{
    public function index(Request $request): ResourceCollection
    {
        abort_unless($request->user()->can(Permission::PostsView->value), 403);

        $perPage = $request->input('per_page');

        $query = Post::query()
            ->published()
            ->with(['author', 'category', 'tags'])
            ->when($request->boolean('featured'), fn ($q) => $q->featured())
            ->when($request->input('category'), fn ($q, $slug) => $q->whereHas('category', fn ($c) => $c->where('slug', $slug)))
            ->when($request->input('tag'), fn ($q, $slug) => $q->whereHas('tags', fn ($t) => $t->where('slug', $slug)))
            ->orderByDesc('published_at');

        if ($perPage === 'all' || $perPage === '-1') {
            $posts = $query->get();
        } else {
            $posts = $query->paginate(is_numeric($perPage) && (int) $perPage > 0 ? (int) $perPage : 10);
        }

        return PostResource::collection($posts);
    }

    public function show(string $slug, Request $request): PostResource
    {
        abort_unless($request->user()->can(Permission::PostsView->value), 403);

        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->with(['author', 'category', 'tags'])
            ->firstOrFail();

        return new PostResource($post);
    }
}
