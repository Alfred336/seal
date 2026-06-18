<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Models\Service;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Services')]
#[Layout('layouts.app')]
class Services extends Component
{
    /** Controls flux:modal visibility via wire:model. */
    public bool $showModal = false;

    public ?int $editingId = null;

    public string $editTitle = '';

    public string $editDescription = '';

    public string $editIcon = '';

    public string $newTitle = '';

    public string $newDescription = '';

    public string $newIcon = '';

    /**
     * Open the modal in create mode.
     * Requires: services.manage
     */
    public function openCreateModal(): void
    {
        abort_unless(auth()->user()->can(Permission::ServicesManage->value), 403);
        $this->reset('newTitle', 'newDescription', 'newIcon');
        $this->resetValidation();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function create(): void
    {
        abort_unless(auth()->user()->can(Permission::ServicesManage->value), 403);
        $this->validate([
            'newTitle'       => ['required', 'string', 'max:100'],
            'newDescription' => ['nullable', 'string'],
            'newIcon'        => ['nullable', 'string', 'max:100'],
        ]);

        $maxOrder = Service::max('sort_order') ?? -1;
        Service::create([
            'title'       => $this->newTitle,
            'description' => $this->newDescription ?: null,
            'icon'        => $this->newIcon ?: null,
            'sort_order'  => $maxOrder + 1,
        ]);

        $this->reset('newTitle', 'newDescription', 'newIcon');
        $this->showModal = false;
    }

    public function startEdit(int $id): void
    {
        $service = Service::findOrFail($id);
        $this->resetValidation();
        $this->editingId      = $id;
        $this->editTitle      = $service->title;
        $this->editDescription = $service->description ?? '';
        $this->editIcon       = $service->icon ?? '';
        $this->showModal      = true;
    }

    public function saveEdit(): void
    {
        abort_unless(auth()->user()->can(Permission::ServicesManage->value), 403);
        $service = Service::findOrFail($this->editingId);
        $this->validate([
            'editTitle'       => ['required', 'string', 'max:100'],
            'editDescription' => ['nullable', 'string'],
            'editIcon'        => ['nullable', 'string', 'max:100'],
        ]);
        $service->update([
            'title'       => $this->editTitle,
            'description' => $this->editDescription ?: null,
            'icon'        => $this->editIcon ?: null,
        ]);
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

    public function toggleActive(int $id): void
    {
        $service = Service::findOrFail($id);
        abort_unless(auth()->user()->can(Permission::ServicesManage->value), 403);
        $service->update(['active' => ! $service->active]);
    }

    public function moveUp(int $id): void
    {
        $this->swap($id, 'up');
    }

    public function moveDown(int $id): void
    {
        $this->swap($id, 'down');
    }

    public function delete(int $id): void
    {
        $service = Service::findOrFail($id);
        abort_unless(auth()->user()->can(Permission::ServicesManage->value), 403);
        $service->delete();
        $this->reindex();
    }

    public function render(): View
    {
        return view('livewire.manage.services', [
            'services' => Service::ordered()->get(),
        ]);
    }

    private function swap(int $id, string $direction): void
    {
        abort_unless(auth()->user()->can(Permission::ServicesManage->value), 403);
        $services = Service::ordered()->get();
        $index = $services->search(fn ($s) => $s->id === $id);

        if ($index === false) {
            return;
        }

        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if ($swapIndex < 0 || $swapIndex >= $services->count()) {
            return;
        }

        [$a, $b] = [$services[$index], $services[$swapIndex]];
        [$a->sort_order, $b->sort_order] = [$b->sort_order, $a->sort_order];
        $a->save();
        $b->save();
    }

    private function reindex(): void
    {
        Service::ordered()->get()->each(function (Service $s, int $i): void {
            $s->update(['sort_order' => $i]);
        });
    }
}
