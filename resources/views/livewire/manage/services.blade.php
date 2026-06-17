<div class="flex flex-col gap-4 max-w-3xl">
    <flux:heading size="lg">{{ __('Services') }}</flux:heading>

    @can('create', App\Models\Service::class)
        <form wire:submit="create" class="rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900 flex flex-col gap-3">
            <flux:heading size="sm">{{ __('Add Service') }}</flux:heading>
            <div class="flex gap-3">
                <flux:field class="flex-1">
                    <flux:label>{{ __('Title') }}</flux:label>
                    <flux:input wire:model="newTitle" placeholder="{{ __('Service title') }}" />
                    <flux:error name="newTitle" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Icon') }}</flux:label>
                    <flux:input wire:model="newIcon" placeholder="code-bracket" />
                    <flux:error name="newIcon" />
                </flux:field>
            </div>
            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="newDescription" rows="2" />
                <flux:error name="newDescription" />
            </flux:field>
            <div class="flex justify-end">
                <flux:button type="submit" variant="primary" icon="plus">{{ __('Add Service') }}</flux:button>
            </div>
        </form>
    @endcan

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-3 py-3 w-16 font-medium text-zinc-500">{{ __('Order') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Title') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Icon') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($services as $service)
                    @if ($editingId === $service->id)
                        <tr>
                            <td colspan="5" class="px-4 py-3">
                                <div class="flex flex-col gap-3">
                                    <div class="flex gap-3">
                                        <flux:field class="flex-1">
                                            <flux:input wire:model="editTitle" placeholder="{{ __('Title') }}" />
                                            <flux:error name="editTitle" />
                                        </flux:field>
                                        <flux:field>
                                            <flux:input wire:model="editIcon" placeholder="code-bracket" />
                                            <flux:error name="editIcon" />
                                        </flux:field>
                                    </div>
                                    <flux:field>
                                        <flux:textarea wire:model="editDescription" rows="2" />
                                        <flux:error name="editDescription" />
                                    </flux:field>
                                    <div class="flex gap-2 justify-end">
                                        <flux:button wire:click="saveEdit" variant="primary" size="sm">{{ __('Save') }}</flux:button>
                                        <flux:button wire:click="$set('editingId', null)" variant="ghost" size="sm">{{ __('Cancel') }}</flux:button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="px-3 py-3">
                                <div class="flex flex-col gap-0.5">
                                    @can('update', $service)
                                        <flux:button wire:click="moveUp({{ $service->id }})" size="sm" variant="ghost" icon="chevron-up" class="h-5" />
                                        <flux:button wire:click="moveDown({{ $service->id }})" size="sm" variant="ghost" icon="chevron-down" class="h-5" />
                                    @endcan
                                </div>
                            </td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                {{ $service->title }}
                                @if($service->description)
                                    <p class="text-xs text-zinc-400 font-normal truncate max-w-xs">{{ $service->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-500">{{ $service->icon ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @can('update', $service)
                                    <flux:button wire:click="toggleActive({{ $service->id }})" size="sm" variant="ghost">
                                        <flux:badge :color="$service->active ? 'green' : 'zinc'" size="sm">
                                            {{ $service->active ? __('Active') : __('Inactive') }}
                                        </flux:badge>
                                    </flux:button>
                                @else
                                    <flux:badge :color="$service->active ? 'green' : 'zinc'" size="sm">
                                        {{ $service->active ? __('Active') : __('Inactive') }}
                                    </flux:badge>
                                @endcan
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    @can('update', $service)
                                        <flux:button wire:click="startEdit({{ $service->id }})" size="sm" variant="ghost" icon="pencil" />
                                    @endcan
                                    @can('delete', $service)
                                        <flux:button wire:click="delete({{ $service->id }})" wire:confirm="{{ __('Delete this service?') }}" size="sm" variant="ghost" icon="trash" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400">{{ __('No services yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
