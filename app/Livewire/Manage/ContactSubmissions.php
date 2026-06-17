<?php

namespace App\Livewire\Manage;

use App\Enums\ContactSubmissionStatus;
use App\Models\ContactSubmission;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Contact Submissions')]
#[Layout('layouts.app')]
class ContactSubmissions extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function updateStatus(int $id, string $status): void
    {
        $submission = ContactSubmission::findOrFail($id);
        $this->authorize('update', $submission);
        $submission->update(['status' => $status]);
    }

    public function render(): View
    {
        $submissions = ContactSubmission::query()
            ->when($this->search, fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('company', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('submitted_at')
            ->paginate(15);

        return view('livewire.manage.contact-submissions', [
            'submissions' => $submissions,
            'statuses'    => ContactSubmissionStatus::cases(),
        ]);
    }
}
