<?php

namespace App\Livewire\Manage;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
class PostForm extends Component
{
    public ?Post $post = null;

    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';
    public string $status = 'draft';
    public ?int $category_id = null;
    public ?int $author_id = null;
    public string $read_time = '';
    public bool $featured = false;
    public string $image_path = '';
    public string $image_alt = '';
    public string $published_at = '';
    /** @var array<int> */
    public array $tag_ids = [];

    public function mount(?Post $post = null): void
    {
        if ($post?->exists) {
            $this->post = $post;
            $this->fill($post->only([
                'title', 'slug', 'excerpt', 'content', 'status',
                'category_id', 'author_id', 'read_time', 'featured',
                'image_path', 'image_alt',
            ]));
            $this->status = $post->status->value;
            $this->published_at = $post->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->tag_ids = $post->tags->pluck('id')->toArray();
        } else {
            $this->author_id = auth()->id();
        }
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->post?->exists) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate($this->rules());

        $data = [
            'title'       => $this->title,
            'slug'        => $this->slug,
            'excerpt'     => $this->excerpt ?: null,
            'content'     => $this->content ?: null,
            'status'      => $this->status,
            'category_id' => $this->category_id ?: null,
            'author_id'   => $this->author_id ?: null,
            'read_time'   => $this->read_time ?: null,
            'featured'    => $this->featured,
            'image_path'  => $this->image_path ?: null,
            'image_alt'   => $this->image_alt ?: null,
            'published_at' => $this->status === 'published'
                ? ($this->published_at ? \Carbon\Carbon::parse($this->published_at) : now())
                : null,
        ];

        if ($this->post?->exists) {
            $this->authorize('update', $this->post);
            $this->post->update($data);
            $this->post->tags()->sync($this->tag_ids);
        } else {
            $this->authorize('create', Post::class);
            $post = Post::create($data);
            $post->tags()->sync($this->tag_ids);
            $this->redirect(route('manage.posts.edit', $post), navigate: true);
            return;
        }

        $this->dispatch('notify', message: 'Post saved.');
    }

    public function render(): View
    {
        return view('livewire.manage.post-form', [
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
            'statuses'   => PostStatus::cases(),
        ]);
    }

    /** @return array<string, mixed> */
    private function rules(): array
    {
        $postId = $this->post?->id;
        return [
            'title'       => ['required', 'string', 'max:300'],
            'slug'        => ['required', 'string', 'max:200', "unique:posts,slug,{$postId}"],
            'excerpt'     => ['nullable', 'string', 'max:1000'],
            'content'     => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,published'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'author_id'   => ['nullable', 'exists:users,id'],
            'read_time'   => ['nullable', 'string', 'max:20'],
            'featured'    => ['boolean'],
            'image_path'  => ['nullable', 'string', 'max:500'],
            'image_alt'   => ['nullable', 'string', 'max:300'],
            'published_at' => ['nullable', 'date'],
            'tag_ids'     => ['array'],
            'tag_ids.*'   => ['exists:tags,id'],
        ];
    }

    #[\Livewire\Attributes\Computed]
    public function title(): string
    {
        return $this->post?->exists ? 'Edit Post' : 'New Post';
    }
}
