<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Blog Posts') }}</flux:heading>
        @can('create', App\Models\Post::class)
            <flux:button :href="route('manage.posts.create')" wire:navigate icon="plus" variant="primary">
                {{ __('New Post') }}
            </flux:button>
        @endcan
    </div>

    <div class="flex gap-3">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search posts…') }}" icon="magnifying-glass" class="max-w-xs" />
        <flux:select wire:model.live="status" class="max-w-40">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
            <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
        </flux:select>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Title') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Author') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Category') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Date') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white max-w-xs truncate">
                            {{ $post->title }}
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $post->author?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $post->category?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$post->status->value === 'published' ? 'green' : 'zinc'" size="sm">
                                {{ $post->status->value }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3 text-zinc-500 whitespace-nowrap">
                            {{ $post->published_at?->formatLocalized('%b %d, %Y') ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $post)
                                    <flux:button :href="route('manage.posts.edit', $post)" wire:navigate size="sm" variant="ghost" icon="pencil" />
                                @endcan
                                @can('delete', $post)
                                    <flux:button wire:click="delete({{ $post->id }})" wire:confirm="{{ __('Delete this post?') }}" size="sm" variant="ghost" icon="trash" />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('No posts found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $posts->links() }}</div>
</div>
