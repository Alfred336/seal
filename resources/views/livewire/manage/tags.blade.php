<div class="flex flex-col gap-4 max-w-xl">
    <flux:heading size="lg">{{ __('Tags') }}</flux:heading>

    @can('create', App\Models\Tag::class)
        <form wire:submit="create" class="flex items-end gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:field class="flex-1">
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model.live.debounce.300ms="newName" placeholder="{{ __('Tag name') }}" />
                <flux:error name="newName" />
            </flux:field>
            <flux:field class="flex-1">
                <flux:label>{{ __('Slug') }}</flux:label>
                <flux:input wire:model="newSlug" placeholder="tag-slug" />
                <flux:error name="newSlug" />
            </flux:field>
            <flux:button type="submit" variant="primary" icon="plus">{{ __('Add') }}</flux:button>
        </form>
    @endcan

    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search…') }}" icon="magnifying-glass" class="max-w-xs" />

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
                        @if ($editingId === $tag->id)
                            <td class="px-4 py-2">
                                <flux:input wire:model="editName" />
                                <flux:error name="editName" />
                            </td>
                            <td class="px-4 py-2">
                                <flux:input wire:model="editSlug" />
                                <flux:error name="editSlug" />
                            </td>
                            <td></td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2 justify-end">
                                    <flux:button wire:click="saveEdit" size="sm" variant="primary">{{ __('Save') }}</flux:button>
                                    <flux:button wire:click="$set('editingId', null)" size="sm" variant="ghost">{{ __('Cancel') }}</flux:button>
                                </div>
                            </td>
                        @else
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $tag->name }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $tag->slug }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ $tag->posts_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    @can('update', $tag)
                                        <flux:button wire:click="startEdit({{ $tag->id }})" size="sm" variant="ghost" icon="pencil" />
                                    @endcan
                                    @can('delete', $tag)
                                        <flux:button wire:click="delete({{ $tag->id }})" wire:confirm="{{ __('Delete this tag?') }}" size="sm" variant="ghost" icon="trash" />
                                    @endcan
                                </div>
                            </td>
                        @endif
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
