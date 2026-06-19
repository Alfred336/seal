<div class="flex flex-col gap-6">

    {{-- Page header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="lg">{{ __('Blog Posts') }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 mt-0.5">
                {{ __('Manage and publish your blog content.') }}
            </flux:text>
        </div>

        @can('posts.create')
            <flux:button :href="route('manage.posts.create')" wire:navigate variant="primary" icon="plus">
                {{ __('New Post') }}
            </flux:button>
        @endcan
    </div>

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search posts…') }}"
            icon="magnifying-glass"
            class="max-w-xs"
        />
        <flux:select wire:model.live="status" class="max-w-44">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- Posts table --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-left border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Title') }}</th>
                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Author') }}</th>
                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Category') }}</th>
                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</th>
                    <th class="px-4 py-3 font-semibold text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('Published') }}</th>
                    <th class="px-4 py-3 w-px"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($posts as $post)
                    <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors duration-100">

                        {{-- Title + featured star --}}
                        <td class="px-4 py-3.5 font-medium text-zinc-900 dark:text-white max-w-xs">
                            <div class="flex items-center gap-2">
                                <span class="truncate">{{ $post->title }}</span>
                                @if ($post->featured)
                                    <flux:badge color="yellow" size="sm">★ Featured</flux:badge>
                                @endif
                            </div>
                        </td>

                        <td class="px-4 py-3.5 text-zinc-500 dark:text-zinc-400">
                            {{ $post->author?->name ?? '—' }}
                        </td>

                        <td class="px-4 py-3.5 text-zinc-500 dark:text-zinc-400">
                            {{ $post->category?->name ?? '—' }}
                        </td>

                        <td class="px-4 py-3.5">
                            @if ($post->status->value === 'published')
                                <flux:badge color="green" size="sm">Published</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">{{ ucfirst($post->status->value) }}</flux:badge>
                            @endif
                        </td>

                        <td class="px-4 py-3.5 text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $post->published_at?->format('M j, Y') ?? '—' }}
                        </td>

                        {{-- Row actions --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                @if (auth()->user()->can('posts.manage-all') || (auth()->user()->can('posts.update-own') && $post->isOwnedBy(auth()->user())))
                                    <flux:button
                                        :href="route('manage.posts.edit', $post)"
                                        wire:navigate
                                        size="sm" variant="ghost" icon="pencil"
                                    />
                                @endif
                                @if (auth()->user()->can('posts.manage-all') || (auth()->user()->can('posts.delete-own') && $post->isOwnedBy(auth()->user())))
                                    <flux:button
                                        wire:click="delete({{ $post->id }})"
                                        wire:confirm="{{ __('Delete this post?') }}"
                                        size="sm" variant="ghost" icon="trash"
                                    />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center gap-2 text-zinc-400 dark:text-zinc-500">
                                <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                <p class="text-sm">{{ __('No posts found.') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div>{{ $posts->links() }}</div>

</div>