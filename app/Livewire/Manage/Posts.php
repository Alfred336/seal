<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

/**
 * Posts Management Component
 *
 * Handles the posts listing page AND the create/edit post form via a modal.
 * Previously the form lived on a separate route (PostForm component); it
 * is now embedded here so the UI never leaves the posts index page.
 *
 * Modal lifecycle:
 *   openCreateModal() → showModal = true, editingId = null (blank form)
 *   openEditModal($id) → showModal = true, editingId = $id (pre-filled form)
 *   save()            → creates or updates, then calls closeModal()
 *   closeModal()      → showModal = false, resets form fields
 */
#[Title('Blog Posts')]
#[Layout('layouts.app')]
class Posts extends Component
{
    use WithPagination;

    // ── List / filter state ────────────────────────────────────────────

    /** Full-text search filter applied to post title. */
    public string $search = '';

    /** Status filter: '' | 'draft' | 'published' */
    public string $status = '';

    // ── Modal state ────────────────────────────────────────────────────

    /** Controls flux:modal visibility via wire:model. */
    public bool $showModal = false;

    /** ID of the post being edited; null means create-new mode. */
    public ?int $editingId = null;

    // ── Form fields ────────────────────────────────────────────────────

    public string $title       = '';
    public string $slug        = '';
    public string $excerpt     = '';
    public string $content     = '';

    /**
     * Post status for the form (renamed to $postStatus to avoid collision
     * with the $status filter property).
     */
    public string $postStatus  = 'draft';

    public ?int   $category_id  = null;
    public string $read_time    = '';
    public bool   $featured     = false;
    public string $image_path   = '';
    public string $image_alt    = '';
    public string $published_at = '';

    /** @var array<int> Tag IDs selected via checkboxes. */
    public array $tag_ids = [];

    // ── Lifecycle hooks ────────────────────────────────────────────────

    /** Reset pagination when the search term changes. */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /** Reset pagination when the status filter changes. */
    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Auto-generate a slug from the title while in create mode.
     * In edit mode the slug is left unchanged to preserve existing URLs.
     */
    public function updatedTitle(string $value): void
    {
        if ($this->editingId === null) {
            $this->slug = Str::slug($value);
        }
    }

    // ── Modal open/close ───────────────────────────────────────────────

    /**
     * Open the modal in create mode (blank form).
     *
     * Requires: posts.create OR posts.manage-all
     */
    public function openCreateModal(): void
    {
        abort_unless(
            auth()->user()->can(Permission::PostsCreate->value)
            || auth()->user()->can(Permission::PostsManageAll->value),
            403
        );

        $this->resetForm();
        $this->resetValidation();
        $this->editingId = null;
        $this->showModal = true;
    }

    /**
     * Open the modal in edit mode, pre-filled with the post's current data.
     *
     * Requires: posts.manage-all OR (posts.update-own AND the post is owned
     * by the current user).
     *
     * @param int $id Post primary key.
     */
    public function openEditModal(int $id): void
    {
        $post = Post::with('tags')->findOrFail($id);
        $user = auth()->user();

        abort_unless(
            $user->can(Permission::PostsManageAll->value)
            || ($user->can(Permission::PostsUpdateOwn->value) && $post->isOwnedBy($user)),
            403
        );

        // Populate form fields from the existing post.
        $this->resetValidation();
        $this->editingId    = $id;
        $this->title        = $post->title;
        $this->slug         = $post->slug;
        $this->excerpt      = $post->excerpt ?? '';
        $this->content      = $post->content ?? '';
        $this->postStatus   = $post->status->value;
        $this->category_id  = $post->category_id;
        $this->read_time    = $post->read_time ?? '';
        $this->featured     = $post->featured;
        $this->image_path   = $post->image_path ?? '';
        $this->image_alt    = $post->image_alt ?? '';
        $this->published_at = $post->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->tag_ids      = $post->tags->pluck('id')->toArray();
        $this->showModal    = true;
    }

    /** Close the modal and reset all form fields + validation state. */
    public function closeModal(): void
    {
        $this->showModal  = false;
        $this->editingId  = null;
        $this->resetForm();
        $this->resetValidation();
    }

    // ── CRUD actions ───────────────────────────────────────────────────

    /**
     * Persist the form — creates a new post or updates an existing one
     * depending on whether $editingId is set.
     */
    public function save(): void
    {
        $this->validate($this->formRules());

        $user = auth()->user();

        // Build the common data array.
        $data = [
            'title'        => $this->title,
            'slug'         => $this->slug,
            'excerpt'      => $this->excerpt ?: null,
            'content'      => $this->content ?: null,
            'status'       => $this->postStatus,
            'category_id'  => $this->category_id ?: null,
            'read_time'    => $this->read_time ?: null,
            'featured'     => $this->featured,
            'image_path'   => $this->image_path ?: null,
            'image_alt'    => $this->image_alt ?: null,
            'published_at' => $this->postStatus === 'published'
                ? ($this->published_at ? Carbon::parse($this->published_at) : now())
                : null,
        ];

        if ($this->editingId) {
            // ── Update existing post ─────────────────────────────────
            $post = Post::findOrFail($this->editingId);

            abort_unless(
                $user->can(Permission::PostsManageAll->value)
                || ($user->can(Permission::PostsUpdateOwn->value) && $post->isOwnedBy($user)),
                403
            );

            $post->update($data);
            $post->tags()->sync($this->tag_ids);
        } else {
            // ── Create new post ──────────────────────────────────────
            abort_unless(
                $user->can(Permission::PostsCreate->value)
                || $user->can(Permission::PostsManageAll->value),
                403
            );

            $data['author_id'] = auth()->id();
            $post = Post::create($data);
            $post->tags()->sync($this->tag_ids);
        }

        $this->closeModal();
        $this->dispatch('notify', message: __('Post saved successfully.'));
    }

    /**
     * Delete a post.
     *
     * Requires: posts.manage-all OR (posts.delete-own AND owned by user).
     *
     * @param int $id Post primary key.
     */
    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        $user = auth()->user();

        abort_unless(
            $user->can(Permission::PostsManageAll->value)
            || ($user->can(Permission::PostsDeleteOwn->value) && $post->isOwnedBy($user)),
            403
        );

        $post->delete();
    }

    // ── Render ─────────────────────────────────────────────────────────

    public function render(): View
    {
        $posts = Post::query()
            ->with(['author', 'category'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.manage.posts', [
            'posts'      => $posts,
            'categories' => Category::orderBy('name')->get(),
            'tags'       => Tag::orderBy('name')->get(),
            'statuses'   => PostStatus::cases(),
        ]);
    }

    // ── Private helpers ────────────────────────────────────────────────

    /**
     * Validation rules for the post form.
     * The slug uniqueness rule excludes the post being edited.
     *
     * @return array<string, mixed>
     */
    private function formRules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:300'],
            'slug'        => [
                'required',
                'string',
                'max:200',
                Rule::unique('posts', 'slug')->ignore($this->editingId),
            ],
            'excerpt'     => ['nullable', 'string', 'max:1000'],
            'content'     => ['nullable', 'string'],
            'postStatus'  => ['required', 'in:draft,published'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'read_time'   => ['nullable', 'string', 'max:20'],
            'featured'    => ['boolean'],
            'image_path'  => ['nullable', 'string', 'max:500'],
            'image_alt'   => ['nullable', 'string', 'max:300'],
            'published_at'=> ['nullable', 'date'],
            'tag_ids'     => ['array'],
            'tag_ids.*'   => ['exists:tags,id'],
        ];
    }

    /** Reset all form fields to their default empty / false values. */
    private function resetForm(): void
    {
        $this->reset(
            'title', 'slug', 'excerpt', 'content',
            'category_id', 'read_time', 'image_path',
            'image_alt', 'published_at', 'tag_ids'
        );
        $this->postStatus = 'draft';
        $this->featured   = false;
    }
}
