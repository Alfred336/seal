<?php

namespace App\Livewire\Manage;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Title('Newsletter Subscribers')]
#[Layout('layouts.app')]
class Subscriptions extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }

    public function unsubscribe(int $id): void
    {
        $sub = Subscription::findOrFail($id);
        $this->authorize('update', $sub);
        $sub->update(['status' => SubscriptionStatus::Unsubscribed, 'unsubscribed_at' => now()]);
    }

    public function exportCsv(): StreamedResponse
    {
        $this->authorize('update', Subscription::class);

        $rows = Subscription::query()
            ->when($this->search, fn ($q) => $q->where('email', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('subscribed_at')
            ->get(['email', 'status', 'source', 'subscribed_at', 'unsubscribed_at']);

        return response()->streamDownload(function () use ($rows): void {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'status', 'source', 'subscribed_at', 'unsubscribed_at']);
            foreach ($rows as $row) {
                fputcsv($out, [
                    $row->email,
                    $row->status->value,
                    $row->source ?? '',
                    $row->subscribed_at->toDateTimeString(),
                    $row->unsubscribed_at?->toDateTimeString() ?? '',
                ]);
            }
            fclose($out);
        }, 'subscribers-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function render(): View
    {
        $subscriptions = Subscription::query()
            ->when($this->search, fn ($q) => $q->where('email', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('subscribed_at')
            ->paginate(20);

        return view('livewire.manage.subscriptions', [
            'subscriptions' => $subscriptions,
            'statuses'      => SubscriptionStatus::cases(),
        ]);
    }
}
