<div class="flex flex-col gap-4">
    <flux:heading size="lg">{{ __('Contact Submissions') }}</flux:heading>

    <div class="flex gap-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search…') }}" icon="magnifying-glass" class="max-w-xs" />
        <flux:select wire:model.live="status" class="max-w-40">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Email') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Company') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Type') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Submitted') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($submissions as $submission)
                    <tr x-data="{ open: false }">
                        <td class="px-4 py-3">
                            <button @click="open = !open" class="font-medium text-zinc-900 dark:text-white hover:underline text-left">
                                {{ $submission->name }}
                            </button>
                            <div x-show="open" x-collapse class="mt-2 text-xs text-zinc-500 bg-zinc-50 dark:bg-zinc-800 rounded p-2 max-w-sm">
                                {{ $submission->message }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $submission->email }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $submission->company ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $submission->project_type ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500 whitespace-nowrap">{{ $submission->submitted_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            @can('update', $submission)
                                <flux:select wire:change="updateStatus({{ $submission->id }}, $event.target.value)" class="text-xs py-1">
                                    @foreach ($statuses as $s)
                                        <flux:select.option :value="$s->value" :selected="$submission->status === $s">
                                            {{ ucfirst($s->value) }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            @else
                                <flux:badge :color="$submission->status->value === 'new' ? 'blue' : ($submission->status->value === 'reviewed' ? 'yellow' : 'zinc')" size="sm">
                                    {{ $submission->status->value }}
                                </flux:badge>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('No submissions found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $submissions->links() }}</div>
</div>
