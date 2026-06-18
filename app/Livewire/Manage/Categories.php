<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Categories')]
#[Layout('layouts.app')]
class Categories extends Component
{
    public string $search = '';

    public ?int $editingId = null;

    public string $editName = '';

    public string $editSlug = '';

    public string $editColor = '';

    public string $newName = '';

    public string $newSlug = '';

    public string $newColor = '';

    public function create(): void
    {
        abort_unless(auth()->user()->can(Permission::CategoriesManage->value), 403);
        $this->validate([
            'newName' => ['required', 'string', 'max:50', 'unique:categories,name'],
            'newSlug' => ['required', 'string', 'max:100', 'unique:categories,slug'],
            'newColor' => ['nullable', 'string', 'max:7'],
        ]);
        Category::create(['name' => $this->newName, 'slug' => $this->newSlug, 'color' => $this->newColor ?: null]);
        $this->reset('newName', 'newSlug', 'newColor');
    }

    public function updatedNewName(string $value): void
    {
        $this->newSlug = Str::slug($value);
    }

    public function startEdit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $id;
        $this->editName = $category->name;
        $this->editSlug = $category->slug ?? '';
        $this->editColor = $category->color ?? '';
    }

    public function saveEdit(): void
    {
        abort_unless(auth()->user()->can(Permission::CategoriesManage->value), 403);
        $category = Category::findOrFail($this->editingId);
        $this->validate([
            'editName' => ['required', 'string', 'max:50', "unique:categories,name,{$this->editingId}"],
            'editSlug' => ['required', 'string', 'max:100', "unique:categories,slug,{$this->editingId}"],
            'editColor' => ['nullable', 'string', 'max:7'],
        ]);
        $category->update(['name' => $this->editName, 'slug' => $this->editSlug, 'color' => $this->editColor ?: null]);
        $this->editingId = null;
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::CategoriesManage->value), 403);
        $category = Category::findOrFail($id);
        $category->delete();
    }

    public function render(): View
    {
        $categories = Category::query()
            ->withCount('posts')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.manage.categories', compact('categories'));
    }
}
