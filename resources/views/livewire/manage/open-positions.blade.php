<div class="flex flex-col gap-4">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Careers & Open Positions') }}</flux:heading>

        @can('careers.manage')
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('New Position') }}
            </flux:button>
        @endcan
    </div>

    {{-- Create / Edit Modal --}}
    <flux:modal wire:model="showModal" class="md:w-xl">
        <div class="space-y-4 p-1">
            <flux:heading>
                {{ $editingId ? __('Edit Position') : __('New Position') }}
            </flux:heading>

            {{-- Title --}}
            <flux:field>
                <flux:label>{{ __('Job Title') }}</flux:label>
                <flux:input wire:model.live.debounce.300ms="title" placeholder="{{ __('e.g. Senior Software Engineer') }}" />
                <flux:error name="title" />
            </flux:field>

            {{-- Slug --}}
            <flux:field>
                <flux:label>{{ __('Slug') }}</flux:label>
                <flux:input wire:model="slug" placeholder="{{ __('e.g. senior-software-engineer') }}" />
                <flux:error name="slug" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                {{-- Type --}}
                <flux:field>
                    <flux:label>{{ __('Job Type') }}</flux:label>
                    <flux:select wire:model="type">
                        <flux:select.option value="Full-time">{{ __('Full-time') }}</flux:select.option>
                        <flux:select.option value="Part-time">{{ __('Part-time') }}</flux:select.option>
                        <flux:select.option value="Contract">{{ __('Contract') }}</flux:select.option>
                        <flux:select.option value="Internship">{{ __('Internship') }}</flux:select.option>
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                {{-- Location --}}
                <flux:field>
                    <flux:label>{{ __('Location') }}</flux:label>
                    <flux:input wire:model="location" placeholder="{{ __('e.g. Dar es Salaam / Remote') }}" />
                    <flux:error name="location" />
                </flux:field>
            </div>

            {{-- Tech Stack --}}
            <flux:field>
                <flux:label>{{ __('Tech Stack') }}</flux:label>
                <flux:input wire:model="tech_stack" placeholder="{{ __('e.g. Laravel · React · AWS') }}" />
                <flux:error name="tech_stack" />
            </flux:field>

            {{-- Description --}}
            <flux:field>
                <flux:label>{{ __('Description & Requirements') }}</flux:label>
                <flux:textarea wire:model="description" rows="6" placeholder="{{ __('Describe the job role and requirements...') }}" />
                <flux:error name="description" />
            </flux:field>

            {{-- Status --}}
            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:select wire:model="status">
                    <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                    <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            {{-- Modal footer --}}
            <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                @if ($editingId)
                    <flux:button wire:click="saveEdit" variant="primary">{{ __('Save Changes') }}</flux:button>
                @else
                    <flux:button wire:click="create" variant="primary">{{ __('Create Position') }}</flux:button>
                @endif
                <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Search --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="{{ __('Search positions…') }}"
        icon="magnifying-glass"
        class="max-w-xs"
    />

    {{-- Table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Title') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Type') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Location') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Tech Stack') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Created') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($jobs as $job)
                    <tr @class(['opacity-60' => $job->status->value === 'draft'])>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            {{ $job->title }}
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $job->type }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $job->location }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $job->tech_stack ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$job->status->value === 'published' ? 'green' : 'zinc'" size="sm">
                                {{ ucfirst($job->status->value) }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-500 whitespace-nowrap">{{ $job->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @can('careers.manage')
                                    <flux:button wire:click="startEdit({{ $job->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="delete({{ $job->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this position?') }}"
                                        size="sm" variant="ghost" icon="trash"
                                    />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-zinc-400">{{ __('No open positions found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $jobs->links() }}</div>

</div>
