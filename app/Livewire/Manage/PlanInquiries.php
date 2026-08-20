<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use App\Enums\PlanInquiryStatus;
use App\Models\PlanInquiry;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

//#[Title('Plan Inquiries')]
//#[Layout('layouts.app')]
class PlanInquiries extends Component
{
    use WithPagination;

    public string $search = '';

    public string $plan = '';

    public string $status = '';

    public string $notes = '';

    public ?int $editingId = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPlan(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $id, string $status): void
    {
        abort_unless(auth()->user()->can(Permission::PlanInquiriesUpdate->value), 403);

        $request = PlanInquiry::findOrFail($id);
        $request->update(['status' => $status]);
    }

    public function editNotes(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::PlanInquiriesUpdate->value), 403);

        $request = PlanInquiry::findOrFail($id);
        $this->editingId = $id;
        $this->notes = $request->notes ?? '';
    }

    public function saveNotes(): void
    {
        abort_unless(auth()->user()->can(Permission::PlanInquiriesUpdate->value), 403);

        if (! $this->editingId) {
            return;
        }

        PlanInquiry::findOrFail($this->editingId)->update([
            'notes' => $this->notes,
        ]);

        $this->editingId = null;
        $this->notes = '';
    }

    public function cancelNotes(): void
    {
        $this->editingId = null;
        $this->notes = '';
    }

    public function render(): View
    {
        $inquiries = PlanInquiry::query()
            ->when($this->search, fn ($q) => $q
                ->where(function ($query): void {
                    $query
                        ->where('full_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('business_name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                }))
            ->when($this->plan, fn ($q) => $q->where('plan', $this->plan))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest()
            ->paginate(15);

        return view('livewire.manage.plan-inquiries', [
            'inquiries' => $inquiries,
            'statuses' => PlanInquiryStatus::cases(),
        ]);
    }
}