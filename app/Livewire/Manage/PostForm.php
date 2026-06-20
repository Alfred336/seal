<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class PostForm extends Component
{
    use WithFileUploads;

    public ?Post $post = null;

    public string $title       = '';
    public string $slug        = '';
    public ?string $excerpt     = '';
    public ?string $content     = '';
    public string $status      = 'draft';
    public ?int   $category_id = null;
    public ?int   $author_id   = null;
    public ?string $read_time   = '';
    public bool   $featured    = false;
    public ?string $image_path  = '';
    public ?string $image_alt   = '';
    public ?string $published_at = '';
    public $imageFile = null;

    /** @var array<int> */
    public array $tag_ids = [];

    public function mount(?Post $post = null): void
    {
        if ($post?->exists) {
            $user = auth()->user();
            abort_unless(
                $user->can(Permission::PostsManageAll->value)
                || ($user->can(Permission::PostsUpdateOwn->value) && $post->isOwnedBy($user)),
                403
            );

            $this->post = $post->load('tags');
            $this->fill($post->only([
                'title', 'slug', 'excerpt', 'content',
                'category_id', 'author_id', 'read_time', 'featured',
                'image_path', 'image_alt',
            ]));
            $this->status       = $post->status->value;
            $this->published_at = $post->published_at?->timezone('Africa/Dar_es_Salaam')->format('Y-m-d\TH:i') ?? '';
            $this->tag_ids      = $post->tags->pluck('id')->toArray();
        } else {
            $this->author_id = auth()->id();
        }
    }

    /** Auto-generate slug from title on create. */
    public function updatedTitle(string $value): void
    {
        if (! $this->post?->exists) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate($this->rules());

        $user = auth()->user();

        $data = [
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt ?: null,
            'content'      => $this->content ?: null,
            'status'       => $this->status,
            'category_id'  => $this->category_id ?: null,
            'author_id'    => $this->author_id ?: null,
            'read_time'    => $this->read_time ?: null,
            'featured'     => $this->featured,
            'image_path'   => $this->image_path ?: null,
            'image_alt'    => $this->image_alt ?: null,
            'published_at' => $this->status === 'published'
                ? ($this->published_at ? Carbon::parse($this->published_at, 'Africa/Dar_es_Salaam')->timezone('UTC') : now())
                : null,
        ];

        if ($this->imageFile) {
            $path = $this->imageFile->store('posts', 'public');
            $data['image_path'] = $path;
            $this->image_path = $path;
            $this->imageFile = null;
        }

        if ($this->post?->exists) {
            // ── Update ────────────────────────────────────────────────
            abort_unless(
                $user->can(Permission::PostsManageAll->value)
                || ($user->can(Permission::PostsUpdateOwn->value) && $this->post->isOwnedBy($user)),
                403
            );

            $this->post->update($data);
            $this->post->tags()->sync($this->tag_ids);

            $this->dispatch('notify', message: __('Post saved successfully.'));
        } else {
            // ── Create ────────────────────────────────────────────────
            abort_unless(
                $user->can(Permission::PostsCreate->value)
                || $user->can(Permission::PostsManageAll->value),
                403
            );

            $data['author_id'] = auth()->id();
            $post = Post::create($data);
            $post->tags()->sync($this->tag_ids);

            session()->flash('notify', __('Post created successfully.'));

            // After creating, go straight to the edit page so the user
            // can continue editing with the post now persisted.
            $this->redirect(route('manage.posts.edit', $post), navigate: true);
        }
    }

    public function render(): View
    {
        return view('livewire.manage.post-form', [
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
            'statuses'   => PostStatus::cases(),
        ]);
    }

    /** Page title — used by the #[Title] computed attribute. */
    #[Computed]
    public function title(): string
    {
        return $this->post?->exists ? 'Edit Post' : 'New Post';
    }

    /** @return array<string, mixed> */
    private function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:300'],
            'slug'         => ['required', 'string', 'max:200', Rule::unique('posts', 'slug')->ignore($this->post?->id)],
            'excerpt'      => ['nullable', 'string', 'max:1000'],
            'content'      => ['nullable', 'string'],
            'status'       => ['required', 'in:draft,published'],
            'category_id'  => ['nullable', 'exists:categories,id'],
            'author_id'    => ['nullable', 'exists:users,id'],
            'read_time'    => ['nullable', 'string', 'max:20'],
            'featured'     => ['boolean'],
            'image_path'   => ['nullable', 'string', 'max:500'],
            'image_alt'    => ['nullable', 'string', 'max:300'],
            'published_at' => ['nullable', 'date'],
            'tag_ids'      => ['array'],
            'tag_ids.*'    => ['exists:tags,id'],
            'imageFile'    => ['nullable', 'image', 'max:10240'],
        ];
    }
}
