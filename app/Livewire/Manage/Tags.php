<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tags')]
#[Layout('layouts.app')]
class Tags extends Component
{
    public string $search = '';

    /** Controls flux:modal visibility via wire:model. */
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $editName = '';

    public string $editSlug = '';

    public string $newName = '';

    public string $newSlug = '';

    /**
     * Open the modal in create mode.
     * Requires: tags.manage
     */
    public function openCreateModal(): void
    {
        abort_unless(auth()->user()->can(Permission::TagsManage->value), 403);
        $this->reset('newName', 'newSlug');
        $this->resetValidation();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function create(): void
    {
        abort_unless(auth()->user()->can(Permission::TagsManage->value), 403);
        $this->validate([
            'newName' => ['required', 'string', 'max:50', 'unique:tags,name'],
            'newSlug' => ['required', 'string', 'max:100', 'unique:tags,slug'],
        ]);
        Tag::create(['name' => $this->newName, 'slug' => $this->newSlug]);
        $this->reset('newName', 'newSlug');
        $this->showModal = false;
    }

    public function updatedNewName(string $value): void
    {
        $this->newSlug = Str::slug($value);
    }

    public function startEdit(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->resetValidation();
        $this->editingId = $id;
        $this->editName  = $tag->name;
        $this->editSlug  = $tag->slug ?? '';
        $this->showModal = true;
    }

    public function saveEdit(): void
    {
        abort_unless(auth()->user()->can(Permission::TagsManage->value), 403);
        $tag = Tag::findOrFail($this->editingId);
        $this->validate([
            'editName' => ['required', 'string', 'max:50', "unique:tags,name,{$this->editingId}"],
            'editSlug' => ['required', 'string', 'max:100', "unique:tags,slug,{$this->editingId}"],
        ]);
        $tag->update(['name' => $this->editName, 'slug' => $this->editSlug]);
        $this->editingId = null;
        $this->showModal = false;
    }

    /** Close modal without saving. */
    public function closeModal(): void
    {
        $this->showModal  = false;
        $this->editingId  = null;
        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::TagsManage->value), 403);
        $tag = Tag::findOrFail($id);
        $tag->delete();
    }

    public function render(): View
    {
        $tags = Tag::query()
            ->withCount('posts')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.manage.tags', compact('tags'));
    }
}
