<div class="flex flex-col gap-4">

    {{-- ─────────────────────────────────────────────────────────────────────
         Page Heading
    ─────────────────────────────────────────────────────────────────────── --}}
    <flux:heading size="lg">{{ __('Project Requests') }}</flux:heading>

    {{-- ─────────────────────────────────────────────────────────────────────
         Filters: search + status dropdown
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="flex gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search…') }}"
            icon="magnifying-glass"
            class="max-w-xs"
        />
        <flux:select wire:model.live="status" class="max-w-44">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ ucfirst(str_replace('_', ' ', $s->value)) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Project Requests Table
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Email') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Project Type') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Submitted') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($requests as $request)
                    {{-- Alpine.js "open" state drives the expandable details panel --}}
                    <tr x-data="{ open: false }">

                        {{-- ─────────────────────────────────────────────────
                             Requester name — click to expand the brief details
                        ───────────────────────────────────────────────────── --}}
                        <td class="px-4 py-3">
                            <button
                                @click="open = !open"
                                class="font-medium text-zinc-900 dark:text-white hover:underline text-left"
                            >
                                {{ $request->full_name }}
                            </button>
                            {{-- Expandable project details --}}
                            <div
                                x-show="open"
                                x-collapse
                                class="mt-2 text-xs text-zinc-500 bg-zinc-50 dark:bg-zinc-800 rounded p-2 max-w-sm"
                            >
                                {{ $request->details }}
                            </div>
                        </td>

                        <td class="px-4 py-3 text-zinc-500">{{ $request->email }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $request->project_type }}</td>
                        <td class="px-4 py-3 text-zinc-500 whitespace-nowrap">{{ $request->created_at->format('M j, Y') }}</td>

                        {{-- ─────────────────────────────────────────────────
                             Status cell
                             Requires: project-requests.update
                             Users with this permission see an editable select
                             dropdown. View-only users see a read-only badge.
                        ───────────────────────────────────────────────────── --}}
                        <td class="px-4 py-3">
                            @can('project-requests.update')
                                {{-- Editable status dropdown --}}
                                <flux:select wire:change="updateStatus({{ $request->id }}, $event.target.value)" class="text-xs py-1">
                                    @foreach ($statuses as $s)
                                        <flux:select.option :value="$s->value" :selected="$request->status === $s">
                                            {{ ucfirst(str_replace('_', ' ', $s->value)) }}
                                        </flux:select.option>
                                    @endforeach
                                </flux:select>
                            @else
                                {{-- Read-only badge for view-only users --}}
                                <flux:badge size="sm">{{ str_replace('_', ' ', $request->status->value) }}</flux:badge>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400">{{ __('No project requests found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination links --}}
    <div>{{ $requests->links() }}</div>

</div>
