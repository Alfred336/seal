<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Projects') }}</flux:heading>
        @can('create', App\Models\Project::class)
            @if (!$showForm)
                <flux:button wire:click="showCreateForm" variant="primary" icon="plus">{{ __('New Project') }}</flux:button>
            @endif
        @endcan
    </div>

    {{-- Create / Edit Form --}}
    @if ($showForm)
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm" class="mb-4">{{ $editingId ? __('Edit Project') : __('New Project') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Title') }} <flux:badge size="sm" color="red">required</flux:badge></flux:label>
                    <flux:input wire:model="title" />
                    <flux:error name="title" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Industry') }}</flux:label>
                    <flux:input wire:model="industry" />
                    <flux:error name="industry" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Tech Stack') }}</flux:label>
                    <flux:input wire:model="tech_stack" placeholder="Laravel, React, Tailwind" />
                    <flux:error name="tech_stack" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Client Name') }}</flux:label>
                    <flux:input wire:model="client_name" />
                    <flux:error name="client_name" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Live URL') }}</flux:label>
                    <flux:input wire:model="live_url" type="url" placeholder="https://" />
                    <flux:error name="live_url" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Completed At') }}</flux:label>
                    <flux:input wire:model="completed_at" type="date" />
                    <flux:error name="completed_at" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Image Path') }}</flux:label>
                    <flux:input wire:model="image_path" placeholder="/images/project.jpg" />
                    <flux:error name="image_path" />
                </flux:field>
                <div class="flex items-end gap-6 pb-1">
                    <flux:checkbox wire:model="featured" :label="__('Featured')" />
                    <flux:checkbox wire:model="active" :label="__('Active')" />
                </div>
            </div>
            <flux:field class="mt-3">
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" rows="3" />
                <flux:error name="description" />
            </flux:field>
            <flux:field class="mt-3">
                <flux:label>{{ __('Outcome') }}</flux:label>
                <flux:textarea wire:model="outcome" rows="2" />
                <flux:error name="outcome" />
            </flux:field>
            <div class="mt-4 flex justify-end gap-2">
                @if ($editingId)
                    <flux:button wire:click="saveEdit" variant="primary">{{ __('Save Changes') }}</flux:button>
                @else
                    <flux:button wire:click="create" variant="primary">{{ __('Create Project') }}</flux:button>
                @endif
                <flux:button wire:click="cancelEdit" variant="ghost">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    @endif

    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search projects…') }}" icon="magnifying-glass" class="max-w-xs" />

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Title') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Industry') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Client') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Featured') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($projects as $project)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white max-w-xs">
                            {{ $project->title }}
                            @if($project->tech_stack)
                                <p class="text-xs text-zinc-400 font-normal">{{ $project->tech_stack }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $project->industry ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $project->client_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @can('update', $project)
                                <flux:button wire:click="toggleFeatured({{ $project->id }})" size="sm" variant="ghost">
                                    <flux:badge :color="$project->featured ? 'yellow' : 'zinc'" size="sm">
                                        {{ $project->featured ? __('Yes') : __('No') }}
                                    </flux:badge>
                                </flux:button>
                            @else
                                <flux:badge :color="$project->featured ? 'yellow' : 'zinc'" size="sm">
                                    {{ $project->featured ? __('Yes') : __('No') }}
                                </flux:badge>
                            @endcan
                        </td>
                        <td class="px-4 py-3">
                            @can('update', $project)
                                <flux:button wire:click="toggleActive({{ $project->id }})" size="sm" variant="ghost">
                                    <flux:badge :color="$project->active ? 'green' : 'zinc'" size="sm">
                                        {{ $project->active ? __('Active') : __('Inactive') }}
                                    </flux:badge>
                                </flux:button>
                            @else
                                <flux:badge :color="$project->active ? 'green' : 'zinc'" size="sm">
                                    {{ $project->active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            @endcan
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @can('update', $project)
                                    <flux:button wire:click="startEdit({{ $project->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button wire:click="delete({{ $project->id }})" wire:confirm="{{ __('Delete this project?') }}" size="sm" variant="ghost" icon="trash" />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('No projects found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $projects->links() }}</div>
</div>
