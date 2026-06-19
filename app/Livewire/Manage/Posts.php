<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Posts index — listing, search/filter, and delete only.
 * Create and edit live on their own dedicated routes via PostForm.
 */
#[Title('Blog Posts')]
#[Layout('layouts.app')]
class Posts extends Component
{
    use WithPagination;

    /** Full-text search applied to post title. */
    public string $search = '';

    /** Status filter: '' | 'draft' | 'published' */
    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Delete a post.
     * Requires: posts.manage-all OR (posts.delete-own AND owned by user).
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

    public function render(): View
    {
        $posts = Post::query()
            ->with(['author', 'category'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.manage.posts', [
            'posts'    => $posts,
            'statuses' => PostStatus::cases(),
        ]);
    }
}
