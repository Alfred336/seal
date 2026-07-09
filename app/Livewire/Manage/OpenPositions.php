<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Models\OpenPosition;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Careers & Jobs')]
#[Layout('layouts.app')]
class OpenPositions extends Component
{
    use WithPagination;

    public string $search = '';

    /** Controls flux:modal visibility via wire:model. */
    public bool $showModal = false;

    public ?int $editingId = null;

    // Form fields
    public string $title = '';
    public string $slug = '';
    public string $type = 'Full-time';
    public string $location = 'Dar es Salaam';
    public string $tech_stack = '';
    public string $description = '';
    public string $status = 'draft';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        abort_unless(auth()->user()->can(Permission::CareersManage->value), 403);
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function create(): void
    {
        abort_unless(auth()->user()->can(Permission::CareersManage->value), 403);
        $this->validate($this->rules());

        OpenPosition::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'location' => $this->location,
            'tech_stack' => $this->tech_stack ?: null,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('notify', message: __('Position created successfully.'));
    }

    public function updatedTitle(string $value): void
    {
        if (!$this->editingId) {
            $this->slug = Str::slug($value);
        }
    }

    public function startEdit(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::CareersManage->value), 403);
        $job = OpenPosition::findOrFail($id);
        $this->resetValidation();
        $this->editingId = $id;
        $this->title = $job->title;
        $this->slug = $job->slug;
        $this->type = $job->type;
        $this->location = $job->location;
        $this->tech_stack = $job->tech_stack ?? '';
        $this->description = $job->description;
        $this->status = $job->status->value;
        $this->showModal = true;
    }

    public function saveEdit(): void
    {
        abort_unless(auth()->user()->can(Permission::CareersManage->value), 403);
        $job = OpenPosition::findOrFail($this->editingId);
        $this->validate($this->rules());

        $job->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'location' => $this->location,
            'tech_stack' => $this->tech_stack ?: null,
            'description' => $this->description,
            'status' => $this->status,
        ]);

        $this->resetForm();
        $this->editingId = null;
        $this->showModal = false;
        $this->dispatch('notify', message: __('Position updated successfully.'));
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::CareersManage->value), 403);
        $job = OpenPosition::findOrFail($id);
        $job->delete();
        $this->dispatch('notify', message: __('Position deleted successfully.'));
    }

    public function render(): View
    {
        $jobs = OpenPosition::query()
            ->when($this->search, fn ($q) => $q
                ->where('title', 'like', "%{$this->search}%")
                ->orWhere('tech_stack', 'like', "%{$this->search}%")
                ->orWhere('location', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        return view('livewire.manage.open-positions', compact('jobs'));
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:250', 'unique:open_positions,slug,' . ($this->editingId ?? 'NULL')],
            'type' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:150'],
            'tech_stack' => ['nullable', 'string', 'max:200'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:draft,published'],
        ];
    }

    private function resetForm(): void
    {
        $this->reset('title', 'slug', 'tech_stack', 'description');
        $this->type = 'Full-time';
        $this->location = 'Dar es Salaam';
        $this->status = 'draft';
        $this->resetValidation();
    }
}
