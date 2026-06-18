<div class="flex flex-col gap-4">

    {{-- ─────────────────────────────────────────────────────────────────────
         Page Header
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Tags') }}</flux:heading>

        {{-- Requires: tags.manage --}}
        @can('tags.manage')
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('New Tag') }}
            </flux:button>
        @endcan
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Create / Edit Tag Modal
         $editingId = null → create mode, $editingId = int → edit mode
    ─────────────────────────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showModal" class="md:w-sm">
        <div class="space-y-4 p-1">

            <flux:heading>
                {{ $editingId ? __('Edit Tag') : __('New Tag') }}
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

                <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="saveEdit" variant="primary">{{ __('Save Changes') }}</flux:button>
                    <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
                </div>

            @else
                {{-- ── Create form ───────────────────────────────────── --}}
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model.live.debounce.300ms="newName" placeholder="{{ __('Tag name') }}" />
                    <flux:error name="newName" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Slug') }}</flux:label>
                    <flux:input wire:model="newSlug" placeholder="tag-slug" />
                    <flux:error name="newSlug" />
                </flux:field>

                <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="create" variant="primary">{{ __('Add Tag') }}</flux:button>
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
        placeholder="{{ __('Search tags…') }}"
        icon="magnifying-glass"
        class="max-w-xs"
    />

    {{-- ─────────────────────────────────────────────────────────────────────
         Tags Table
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Slug') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Posts') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($tags as $tag)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $tag->name }}</td>
                        <td class="px-4 py-3 text-zinc-500 font-mono text-xs">{{ $tag->slug }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $tag->posts_count }}</td>

                        {{-- Edit + Delete — requires: tags.manage --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @can('tags.manage')
                                    <flux:button wire:click="startEdit({{ $tag->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="delete({{ $tag->id }})"
                                        wire:confirm="{{ __('Delete this tag?') }}"
                                        size="sm" variant="ghost" icon="trash"
                                    />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-zinc-400">{{ __('No tags yet.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
