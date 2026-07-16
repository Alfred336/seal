<div class="flex flex-col gap-4">

    {{-- ─────────────────────────────────────────────────────────────────────
         Page Header
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Services') }}</flux:heading>

        {{-- Requires: services.manage --}}
        @can('services.manage')
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('Add Service') }}
            </flux:button>
        @endcan
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Create / Edit Service Modal
         $editingId = null → create mode, $editingId = int → edit mode
    ─────────────────────────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showModal" class="md:w-lg">
        <div class="space-y-4 p-1">

            <flux:heading>
                {{ $editingId ? __('Edit Service') : __('New Service') }}
            </flux:heading>

            @if ($editingId)
                {{-- ── Edit form ─────────────────────────────────────── --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <flux:field class="sm:col-span-2">
                        <flux:label>{{ __('Title') }}</flux:label>
                        <flux:input wire:model="editTitle" />
                        <flux:error name="editTitle" />
                    </flux:field>
                <flux:field>
                    <flux:label>{{ __('Icon') }}</flux:label>
                    <flux:input wire:model="editIcon" placeholder="code-bracket" />
                    <flux:error name="editIcon" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Blog Category') }}</flux:label>

                    <select wire:model="editCategoryId"
                        class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 px-3 py-2">
                        <option value="">Select Category</option>

                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <flux:error name="editCategoryId" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="editDescription" rows="3" />
                    <flux:error name="editDescription" />
                </flux:field>
                <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="saveEdit" variant="primary">{{ __('Save Changes') }}</flux:button>
                    <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>

            @else
                {{-- ── Create form ───────────────────────────────────── --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <flux:field class="sm:col-span-2">
                        <flux:label>{{ __('Title') }} <flux:badge size="sm" color="red">required</flux:badge></flux:label>
                        <flux:input wire:model="newTitle" placeholder="{{ __('Service title') }}" />
                        <flux:error name="newTitle" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Icon') }}</flux:label>
                        <flux:input wire:model="newIcon" placeholder="code-bracket" />
                        <flux:error name="newIcon" />
                    </flux:field>
                    <flux:field>
                        <flux:label>{{ __('Blog Category') }}</flux:label>

                        <select wire:model="newCategoryId"
                            class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 px-3 py-2">
                            <option value="">Select Category</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <flux:error name="newCategoryId" />
                    </flux:field>
                </div>
                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="newDescription" rows="3" />
                    <flux:error name="newDescription" />
                </flux:field>
                <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="create" variant="primary">{{ __('Add Service') }}</flux:button>
                    <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    {{-- ─────────────────────────────────────────────────────────────────────
         Services Table
    ─────────────────────────────────────────────────────────────────────── --}}
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
                    <tr>
                        {{-- Reorder arrows — requires: services.manage --}}
                        <td class="px-3 py-3">
                            <div class="flex flex-col gap-0.5">
                                @can('services.manage')
                                    <flux:button wire:click="moveUp({{ $service->id }})"   size="sm" variant="ghost" icon="chevron-up"   class="h-5" />
                                    <flux:button wire:click="moveDown({{ $service->id }})" size="sm" variant="ghost" icon="chevron-down" class="h-5" />
                                @endcan
                            </div>
                        </td>

                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            {{ $service->title }}
                            @if ($service->description)
                                <p class="text-xs text-zinc-400 font-normal truncate max-w-xs">{{ $service->description }}</p>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-zinc-500">{{ $service->icon ?? '—' }}</td>

                        {{-- Active toggle — requires: services.manage; else read-only badge --}}
                        <td class="px-4 py-3">
                            @can('services.manage')
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

                        {{-- Edit opens modal, delete confirms inline --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @can('services.manage')
                                    <flux:button wire:click="startEdit({{ $service->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="delete({{ $service->id }})"
                                        wire:confirm="{{ __('Delete this service?') }}"
                                        size="sm" variant="ghost" icon="trash"
                                    />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400">{{ __('No services yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
