<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Newsletter Subscribers') }}</flux:heading>
        @can('update', App\Models\Subscription::class)
            <flux:button wire:click="exportCsv" variant="ghost" icon="arrow-down-tray">{{ __('Export CSV') }}</flux:button>
        @endcan
    </div>

    <div class="flex gap-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search email…') }}" icon="magnifying-glass" class="max-w-xs" />
        <flux:select wire:model.live="status" class="max-w-44">
            <flux:select.option value="">{{ __('All') }}</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Email') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Source') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Subscribed') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($subscriptions as $sub)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $sub->email }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $sub->source ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500 whitespace-nowrap">{{ $sub->subscribed_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$sub->status->value === 'active' ? 'green' : 'zinc'" size="sm">
                                {{ $sub->status->value }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($sub->status->value === 'active')
                                @can('delete', $sub)
                                    <flux:button wire:click="unsubscribe({{ $sub->id }})" wire:confirm="{{ __('Unsubscribe this email?') }}" size="sm" variant="ghost">
                                        {{ __('Unsubscribe') }}
                                    </flux:button>
                                @endcan
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-zinc-400">{{ __('No subscribers found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $subscriptions->links() }}</div>
</div>
