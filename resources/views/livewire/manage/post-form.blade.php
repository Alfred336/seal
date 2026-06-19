<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
    {{-- Top Bar --}}
    <div class="sticky top-0 z-30 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <flux:button
                    :href="route('manage.posts.index')"
                    wire:navigate
                    variant="ghost"
                    icon="arrow-left"
                    size="sm"
                    class="shrink-0 text-zinc-500 hover:text-zinc-900 dark:hover:text-white"
                />
                <div class="h-4 w-px bg-zinc-200 dark:bg-zinc-700 shrink-0"></div>
                <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $this->title }}</p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <flux:button
                    :href="route('manage.posts.index')"
                    wire:navigate
                    variant="ghost"
                    size="sm"
                    class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 hidden sm:inline-flex"
                >
                    {{ __('Discard') }}
                </flux:button>
                <flux:button
                    type="button"
                    variant="primary"
                    size="sm"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    x-on:click="window.quillSave($wire)"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 rounded-lg shadow-sm transition-all"
                >
                    <span wire:loading.remove wire:target="save" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Save Post') }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        {{ __('Saving...') }}
                    </span>
                </flux:button>
            </div>
        </div>
    </div>

    {{-- Page Body --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <form id="post-form" wire:submit="save" class="grid grid-cols-1 xl:grid-cols-[1fr_308px] gap-8 items-start">

            {{-- ═══ LEFT COLUMN ═══ --}}
            <div class="flex flex-col gap-6">

                {{-- Title --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
                    <div class="px-5 pt-5 pb-4">
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mb-2">
                            {{ __('Title') }}
                            <span class="ml-1.5 inline-flex items-center rounded-full bg-rose-50 dark:bg-rose-900/30 px-1.5 py-0.5 text-[10px] font-semibold text-rose-500 dark:text-rose-400 normal-case tracking-normal">required</span>
                        </label>
                        <input
                            wire:model.live.debounce.400ms="title"
                            type="text"
                            placeholder="{{ __('Give your post a compelling title…') }}"
                            class="w-full text-2xl font-bold text-zinc-900 dark:text-white bg-transparent placeholder:text-zinc-300 dark:placeholder:text-zinc-600 border-none outline-none focus:ring-0 p-0 leading-snug"
                        />
                        <flux:error name="title" class="mt-2 text-xs text-rose-500" />
                    </div>

                    <div class="border-t border-zinc-100 dark:border-zinc-800 px-5 py-3.5 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-medium text-zinc-400 dark:text-zinc-500 shrink-0">Slug</span>
                            <div class="h-px flex-1 bg-zinc-200 dark:bg-zinc-700"></div>
                        </div>
                        <input
                            wire:model="slug"
                            type="text"
                            placeholder="post-url-slug"
                            class="mt-1.5 w-full text-sm font-mono text-indigo-600 dark:text-indigo-400 bg-transparent placeholder:text-zinc-300 dark:placeholder:text-zinc-600 border-none outline-none focus:ring-0 p-0"
                        />
                        <p class="mt-1 text-[11px] text-zinc-400 dark:text-zinc-500">{{ __('Auto-generated from title. Edit manually if needed.') }}</p>
                        <flux:error name="slug" class="mt-1 text-xs text-rose-500" />
                    </div>
                </div>

                {{-- Excerpt --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Excerpt') }}</p>
                    </div>
                    <div class="p-5">
                        <textarea
                            wire:model="excerpt"
                            rows="3"
                            placeholder="{{ __('A short, compelling summary shown in post listings and SEO previews…') }}"
                            class="w-full text-sm text-zinc-700 dark:text-zinc-300 bg-transparent placeholder:text-zinc-300 dark:placeholder:text-zinc-600 border-none outline-none focus:ring-0 p-0 resize-none leading-relaxed"
                        ></textarea>
                        <flux:error name="excerpt" class="mt-2 text-xs text-rose-500" />
                    </div>
                </div>

                {{-- Content (Quill) --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Content') }}</p>
                    </div>
                    <div class="p-5">
                        <div class="ql-wrapper">
                            <div x-quill="'content'"></div>
                        </div>
                        <flux:error name="content" class="mt-2 text-xs text-rose-500" />
                    </div>
                </div>
            </div>

            {{-- ═══ RIGHT COLUMN (sidebar) ═══ --}}
            <div class="flex flex-col gap-5">

                {{-- ── Publish Card ── --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Publish') }}</p>
                        <div class="h-1.5 w-1.5 rounded-full bg-emerald-400 ring-2 ring-emerald-400/25"></div>
                    </div>
                    <div class="p-4 flex flex-col gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">{{ __('Status') }}</label>
                            <flux:select wire:model="status" class="w-full text-sm rounded-lg border-zinc-200 dark:border-zinc-700 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach ($statuses as $s)
                                    <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="status" class="mt-1 text-xs text-rose-500" />
                        </div>

                        <div x-data x-show="$wire.status === 'published'" x-transition x-cloak>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">{{ __('Publish Date') }}</label>
                            <flux:input wire:model="published_at" type="datetime-local" class="w-full text-sm rounded-lg border-zinc-200 dark:border-zinc-700 focus:ring-indigo-500 focus:border-indigo-500" />
                            <flux:error name="published_at" class="mt-1 text-xs text-rose-500" />
                        </div>

                        <label class="flex items-center gap-3 p-3 rounded-xl border border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 cursor-pointer transition-colors group">
                            <flux:checkbox wire:model="featured" id="featured-check" class="rounded text-indigo-600 focus:ring-indigo-500" />
                            <div>
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ __('Featured post') }}</p>
                                <p class="text-[11px] text-zinc-400 dark:text-zinc-500 mt-0.5">{{ __('Pin to top of listings') }}</p>
                            </div>
                            <svg class="ml-auto w-4 h-4 text-amber-400 opacity-0 group-has-[:checked]:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </label>
                    </div>
                </div>

                {{-- ── Organisation Card ── --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Organisation') }}</p>
                    </div>
                    <div class="p-4 flex flex-col gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">{{ __('Category') }}</label>
                            <flux:select wire:model="category_id" class="w-full text-sm rounded-lg border-zinc-200 dark:border-zinc-700 focus:ring-indigo-500 focus:border-indigo-500">
                                <flux:select.option value="">{{ __('Uncategorised') }}</flux:select.option>
                                @foreach ($categories as $category)
                                    <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <flux:error name="category_id" class="mt-1 text-xs text-rose-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">{{ __('Read Time') }}</label>
                            <div class="relative">
                                <flux:input wire:model="read_time" placeholder="5" class="w-full text-sm rounded-lg border-zinc-200 dark:border-zinc-700 focus:ring-indigo-500 focus:border-indigo-500 pr-12" />
                                <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-zinc-400 font-medium">min</span>
                            </div>
                            <flux:error name="read_time" class="mt-1 text-xs text-rose-500" />
                        </div>

                        @if ($tags->isNotEmpty())
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-2">{{ __('Tags') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($tags as $tag)
                                        <label
                                            :for="'tag-{{ $tag->id }}'"
                                            x-data="{ id: {{ $tag->id }} }"
                                            x-bind:class="$wire.tag_ids && $wire.tag_ids.map(Number).includes(id)
                                                   ? 'relative flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-medium cursor-pointer transition-all bg-indigo-50 border-indigo-300 text-indigo-700 dark:bg-indigo-900/30 dark:border-indigo-600 dark:text-indigo-400 font-semibold'
                                                   : 'relative flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-medium cursor-pointer transition-all border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:border-indigo-300 dark:hover:border-indigo-600 hover:text-indigo-700 dark:hover:text-indigo-400'"
                                            class="relative flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-medium cursor-pointer transition-all border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:border-indigo-300 dark:hover:border-indigo-600 hover:text-indigo-700 dark:hover:text-indigo-400"
                                        >
                                            <flux:checkbox
                                                wire:model="tag_ids"
                                                :value="$tag->id"
                                                :id="'tag-' . $tag->id"
                                                class="sr-only"
                                            />
                                            # {{ $tag->name }}
                                        </label>
                                    @endforeach
                                </div>
                                <flux:error name="tag_ids" class="mt-2 text-xs text-rose-500" />
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── Cover Image Card ── --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xs overflow-hidden">
                    <div class="px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('Cover Image') }}</p>
                    </div>
                    <div class="p-4 flex flex-col gap-4">
                        {{-- Preview area --}}
                        <div
                            x-data
                            class="relative rounded-xl border-2 border-dashed border-zinc-200 dark:border-zinc-700 overflow-hidden bg-zinc-50 dark:bg-zinc-800/40 min-h-[120px] flex items-center justify-center"
                        >
                            <template x-if="!$wire.image_path">
                                <div class="text-center py-6 px-4">
                                    <svg class="mx-auto w-8 h-8 text-zinc-300 dark:text-zinc-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 21h18M3 3h18M9 9.75a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z"/>
                                    </svg>
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('No image set') }}</p>
                                </div>
                            </template>
                            <template x-if="$wire.image_path">
                                <img
                                    :src="$wire.image_path"
                                    :alt="$wire.image_alt || ''"
                                    class="w-full h-36 object-cover"
                                    onerror="this.style.display='none'"
                                />
                            </template>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">{{ __('Image Path') }}</label>
                            <flux:input wire:model="image_path" placeholder="/images/cover.jpg" class="w-full text-sm font-mono rounded-lg border-zinc-200 dark:border-zinc-700 focus:ring-indigo-500" />
                            <flux:error name="image_path" class="mt-1 text-xs text-rose-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 dark:text-zinc-400 mb-1.5">{{ __('Alt Text') }}</label>
                            <flux:input wire:model="image_alt" placeholder="{{ __('Describe the image for accessibility…') }}" class="w-full text-sm rounded-lg border-zinc-200 dark:border-zinc-700 focus:ring-indigo-500" />
                            <flux:error name="image_alt" class="mt-1 text-xs text-rose-500" />
                        </div>
                    </div>
                </div>

            </div>{{-- end right column --}}
        </form>
    </div>
</div>