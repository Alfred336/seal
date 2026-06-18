<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Models\Project;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Projects')]
#[Layout('layouts.app')]
class Projects extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    /** Controls flux:modal visibility via wire:model. */
    public bool $showModal = false;

    // form fields
    public string $title = '';

    public string $industry = '';

    public string $tech_stack = '';

    public string $description = '';

    public string $client_name = '';

    public string $outcome = '';

    public string $image_path = '';

    public string $live_url = '';

    public bool $featured = false;

    public bool $active = true;

    public string $completed_at = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingId  = null;
        $this->showModal  = true;
    }

    public function create(): void
    {
        abort_unless(auth()->user()->can(Permission::ProjectsManage->value), 403);
        $this->validateForm();
        $max = Project::max('sort_order') ?? -1;
        Project::create([...$this->formData(), 'sort_order' => $max + 1]);
        $this->resetForm();
        $this->showModal = false;
    }

    public function startEdit(int $id): void
    {
        $project = Project::findOrFail($id);
        $this->showModal = true;
        $this->editingId = $id;
        $this->title = $project->title;
        $this->industry = $project->industry ?? '';
        $this->tech_stack = $project->tech_stack ?? '';
        $this->description = $project->description ?? '';
        $this->client_name = $project->client_name ?? '';
        $this->outcome = $project->outcome ?? '';
        $this->image_path = $project->image_path ?? '';
        $this->live_url = $project->live_url ?? '';
        $this->featured = $project->featured;
        $this->active = $project->active;
        $this->completed_at = $project->completed_at?->format('Y-m-d') ?? '';
    }

    public function saveEdit(): void
    {
        $project = Project::findOrFail($this->editingId);
        abort_unless(auth()->user()->can(Permission::ProjectsManage->value), 403);
        $this->validateForm();
        $project->update($this->formData());
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = false;
    }

    public function toggleActive(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::ProjectsManage->value), 403);
        $project = Project::findOrFail($id);
        $project->update(['active' => ! $project->active]);
    }

    public function toggleFeatured(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::ProjectsManage->value), 403);
        $project = Project::findOrFail($id);
        $project->update(['featured' => ! $project->featured]);
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::ProjectsManage->value), 403);
        $project = Project::findOrFail($id);
        $project->delete();
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->editingId  = null;
        $this->showModal  = false;
    }

    public function render(): View
    {
        $projects = Project::query()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->ordered()
            ->paginate(12);

        return view('livewire.manage.projects', compact('projects'));
    }

    private function validateForm(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:150'],
            'industry' => ['nullable', 'string', 'max:100'],
            'tech_stack' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'client_name' => ['nullable', 'string', 'max:150'],
            'outcome' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'live_url' => ['nullable', 'url', 'max:500'],
            'featured' => ['boolean'],
            'active' => ['boolean'],
            'completed_at' => ['nullable', 'date'],
        ]);
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        return [
            'title' => $this->title,
            'industry' => $this->industry ?: null,
            'tech_stack' => $this->tech_stack ?: null,
            'description' => $this->description ?: null,
            'client_name' => $this->client_name ?: null,
            'outcome' => $this->outcome ?: null,
            'image_path' => $this->image_path ?: null,
            'live_url' => $this->live_url ?: null,
            'featured' => $this->featured,
            'active' => $this->active,
            'completed_at' => $this->completed_at ?: null,
        ];
    }

    private function resetForm(): void
    {
        $this->reset('title', 'industry', 'tech_stack', 'description', 'client_name', 'outcome', 'image_path', 'live_url', 'completed_at');
        $this->featured = false;
        $this->active = true;
    }
}
