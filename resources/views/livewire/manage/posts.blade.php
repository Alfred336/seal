<div class="flex flex-col gap-4">

    {{-- ─────────────────────────────────────────────────────────────────────
         Page Header
         "New Post" opens a floating modal instead of navigating to a
         separate page.  Requires: posts.create OR posts.manage-all
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Blog Posts') }}</flux:heading>

        @can('posts.create')
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('New Post') }}
            </flux:button>
        @endcan
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Create / Edit Post Modal
         Controlled by $showModal via wire:model.
         The same modal serves both create (editingId = null) and
         edit (editingId = post ID) modes.
    ─────────────────────────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showModal" class="md:w-3xl">
        <div class="space-y-5 p-1">

            {{-- Modal heading --}}
            <flux:heading size="lg">
                {{ $editingId ? __('Edit Post') : __('New Post') }}
            </flux:heading>

            {{-- ── Row 1: Title + Slug ──────────────────────────────── --}}
            <div class="grid sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Title') }} <flux:badge size="sm" color="red">required</flux:badge></flux:label>
                    <flux:input wire:model.live.debounce.400ms="title" placeholder="{{ __('Post title') }}" />
                    <flux:error name="title" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Slug') }}</flux:label>
                    <flux:input wire:model="slug" placeholder="post-slug" />
                    <flux:error name="slug" />
                </flux:field>
            </div>

            {{-- ── Row 2: Category + Status + Read time ────────────── --}}
            <div class="grid sm:grid-cols-3 gap-4">
                <flux:field>
                    <flux:label>{{ __('Category') }}</flux:label>
                    <flux:select wire:model="category_id">
                        <flux:select.option value="">{{ __('None') }}</flux:select.option>
                        @foreach ($categories as $category)
                            <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="category_id" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Status') }}</flux:label>
                    <flux:select wire:model="postStatus">
                        @foreach ($statuses as $s)
                            <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="postStatus" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Read Time') }}</flux:label>
                    <flux:input wire:model="read_time" placeholder="5 min" />
                    <flux:error name="read_time" />
                </flux:field>
            </div>

            {{-- ── Publish date (only shown when status = published) ── --}}
            <div x-show="{{ json_encode($postStatus) }} === 'published' || $wire.postStatus === 'published'" class="w-full sm:w-1/2">
                <flux:field>
                    <flux:label>{{ __('Publish Date') }}</flux:label>
                    <flux:input wire:model="published_at" type="datetime-local" />
                    <flux:error name="published_at" />
                </flux:field>
            </div>

            {{-- ── Excerpt ─────────────────────────────────────────── --}}
            <flux:field>
                <flux:label>{{ __('Excerpt') }}</flux:label>
                <flux:textarea wire:model="excerpt" rows="2" placeholder="{{ __('Short summary…') }}" />
                <flux:error name="excerpt" />
            </flux:field>

            {{-- ── Content ─────────────────────────────────────────── --}}
            <flux:field>
                <flux:label>{{ __('Content') }}</flux:label>
                <flux:textarea wire:model="content" rows="6" placeholder="{{ __('Full post content…') }}" />
                <flux:error name="content" />
            </flux:field>

            {{-- ── Row: Image path + alt ───────────────────────────── --}}
            <div class="grid sm:grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Cover Image Path') }}</flux:label>
                    <flux:input wire:model="image_path" placeholder="/images/post.jpg" />
                    <flux:error name="image_path" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Image Alt Text') }}</flux:label>
                    <flux:input wire:model="image_alt" />
                    <flux:error name="image_alt" />
                </flux:field>
            </div>

            {{-- ── Tags ────────────────────────────────────────────── --}}
            <div>
                <flux:label class="mb-2">{{ __('Tags') }}</flux:label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="tag_ids"
                                :value="{{ $tag->id }}"
                                value="{{ $tag->id }}"
                                class="rounded border-zinc-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700"
                            />
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                <flux:error name="tag_ids" />
            </div>

            {{-- ── Featured toggle ─────────────────────────────────── --}}
            <div class="flex items-center gap-2">
                <flux:checkbox wire:model="featured" id="featured-check" />
                <label for="featured-check" class="text-sm text-zinc-700 dark:text-zinc-300 cursor-pointer">
                    {{ __('Featured post') }}
                </label>
            </div>

            {{-- ── Modal footer buttons ─────────────────────────────── --}}
            <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="save" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">{{ __('Save Post') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                </flux:button>
                <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- ─────────────────────────────────────────────────────────────────────
         Filters: search + status dropdown
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="flex gap-3">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search posts…') }}"
            icon="magnifying-glass"
            class="max-w-xs"
        />
        <flux:select wire:model.live="status" class="max-w-40">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Posts Table
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Title') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Author') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Category') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Published') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($posts as $post)
                    <tr>
                        {{-- Title + featured badge --}}
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white max-w-xs">
                            {{ $post->title }}
                            @if ($post->featured)
                                <flux:badge color="yellow" size="sm" class="ml-1">★</flux:badge>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-zinc-500">{{ $post->author?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-zinc-500">{{ $post->category?->name ?? '—' }}</td>

                        {{-- Status badge --}}
                        <td class="px-4 py-3">
                            <flux:badge :color="$post->status->value === 'published' ? 'green' : 'zinc'" size="sm">
                                {{ ucfirst($post->status->value) }}
                            </flux:badge>
                        </td>

                        <td class="px-4 py-3 text-zinc-500 whitespace-nowrap">
                            {{ $post->published_at?->format('M j, Y') ?? '—' }}
                        </td>

                        {{-- ─────────────────────────────────────────────────
                             Row actions — Edit opens the modal (no page nav).
                             Edit: requires manage-all OR (update-own AND owned)
                             Delete: requires manage-all OR (delete-own AND owned)
                        ───────────────────────────────────────────────────── --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @if (auth()->user()->can('posts.manage-all') || (auth()->user()->can('posts.update-own') && $post->isOwnedBy(auth()->user())))
                                    <flux:button
                                        wire:click="openEditModal({{ $post->id }})"
                                        size="sm"
                                        variant="ghost"
                                        icon="pencil"
                                    />
                                @endif

                                @if (auth()->user()->can('posts.manage-all') || (auth()->user()->can('posts.delete-own') && $post->isOwnedBy(auth()->user())))
                                    <flux:button
                                        wire:click="delete({{ $post->id }})"
                                        wire:confirm="{{ __('Delete this post?') }}"
                                        size="sm"
                                        variant="ghost"
                                        icon="trash"
                                    />
                                @endif
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

    {{-- Pagination --}}
    <div>{{ $posts->links() }}</div>

</div>