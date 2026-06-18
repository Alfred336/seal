<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Models\Post;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Blog Posts')]
#[Layout('layouts.app')]
class Posts extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        $user = auth()->user();
        abort_unless(
            $user->can(Permission::PostsManageAll->value) ||
            ($user->can(Permission::PostsDeleteOwn->value) && $post->isOwnedBy($user)),
            403
        );
        $post->delete();
        $this->dispatch('notify', message: 'Post deleted.');
    }

    public function render(): View
    {
        $posts = Post::query()
            ->with(['author', 'category'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.manage.posts', compact('posts'));
    }
}
