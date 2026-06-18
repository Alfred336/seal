<div class="flex flex-col gap-4">

    {{-- ─────────────────────────────────────────────────────────────────────
         Page Header
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Categories') }}</flux:heading>

        {{-- Requires: categories.manage --}}
        @can('categories.manage')
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('New Category') }}
            </flux:button>
        @endcan
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Create / Edit Category Modal
         $editingId = null  → create mode (uses newName/newSlug/newColor fields)
         $editingId = int   → edit mode   (uses editName/editSlug/editColor fields)
    ─────────────────────────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showModal" class="md:w-md">
        <div class="space-y-4 p-1">

            <flux:heading>
                {{ $editingId ? __('Edit Category') : __('New Category') }}
            </flux:heading>

            @if ($editingId)
                {{-- ── Edit form ─────────────────────────────────────── --}}
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model="editName" />
                    <flux:error name="editName" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Slug') }}</flux:label>
                    <flux:input wire:model="editSlug" />
                    <flux:error name="editSlug" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Colour') }}</flux:label>
                    <div class="flex items-center gap-3">
                        <flux:input wire:model="editColor" type="color" class="w-12 h-9 p-1 cursor-pointer" />
                        <flux:input wire:model="editColor" placeholder="#3b82f6" class="flex-1" />
                    </div>
                    <flux:error name="editColor" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="saveEdit" variant="primary">{{ __('Save Changes') }}</flux:button>
                    <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>

            @else
                {{-- ── Create form ───────────────────────────────────── --}}
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model.live.debounce.300ms="newName" placeholder="{{ __('Category name') }}" />
                    <flux:error name="newName" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Slug') }}</flux:label>
                    <flux:input wire:model="newSlug" placeholder="category-slug" />
                    <flux:error name="newSlug" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Colour') }}</flux:label>
                    <div class="flex items-center gap-3">
                        <flux:input wire:model="newColor" type="color" class="w-12 h-9 p-1 cursor-pointer" />
                        <flux:input wire:model="newColor" placeholder="#3b82f6" class="flex-1" />
                    </div>
                    <flux:error name="newColor" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="create" variant="primary">{{ __('Add Category') }}</flux:button>
                    <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    {{-- ─────────────────────────────────────────────────────────────────────
         Search
    ─────────────────────────────────────────────────────────────────────── --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="{{ __('Search categories…') }}"
        icon="magnifying-glass"
        class="max-w-xs"
    />

    {{-- ─────────────────────────────────────────────────────────────────────
         Categories Table
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Slug') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Colour') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Posts') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-zinc-500 font-mono text-xs">{{ $category->slug }}</td>
                        <td class="px-4 py-3">
                            @if ($category->color)
                                <span class="inline-flex items-center gap-1.5">
                                    <span class="inline-block w-4 h-4 rounded-full border border-zinc-200" style="background:{{ $category->color }}"></span>
                                    <span class="text-xs text-zinc-500 font-mono">{{ $category->color }}</span>
                                </span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $category->posts_count }}</td>

                        {{-- Edit + Delete — requires: categories.manage --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @can('categories.manage')
                                    <flux:button wire:click="startEdit({{ $category->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="delete({{ $category->id }})"
                                        wire:confirm="{{ __('Delete this category?') }}"
                                        size="sm" variant="ghost" icon="trash"
                                    />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400">{{ __('No categories yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
