<?php

namespace App\Livewire\Manage;

use App\Enums\ProjectRequestStatus;
use App\Models\ProjectRequest;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Project Requests')]
#[Layout('layouts.app')]
class ProjectRequests extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function updateStatus(int $id, string $status): void
    {
        $request = ProjectRequest::findOrFail($id);
        $this->authorize('update', $request);
        $request->update(['status' => $status]);
    }

    public function render(): View
    {
        $requests = ProjectRequest::query()
            ->when($this->search, fn ($q) => $q
                ->where('full_name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('project_type', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.manage.project-requests', [
            'requests' => $requests,
            'statuses' => ProjectRequestStatus::cases(),
        ]);
    }
}
