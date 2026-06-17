<div class="flex flex-col gap-6 max-w-4xl">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ $this->title }}</flux:heading>
        <flux:button :href="route('manage.posts.index')" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back') }}
        </flux:button>
    </div>

    <form wire:submit="save" class="flex flex-col gap-5">

        <flux:field>
            <flux:label>{{ __('Title') }}</flux:label>
            <flux:input wire:model.live.debounce.400ms="title" placeholder="{{ __('Post title') }}" />
            <flux:error name="title" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Slug') }}</flux:label>
            <flux:input wire:model="slug" placeholder="post-url-slug" />
            <flux:error name="slug" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Excerpt') }}</flux:label>
            <flux:textarea wire:model="excerpt" rows="2" placeholder="{{ __('Short description') }}" />
            <flux:error name="excerpt" />
        </flux:field>

        <flux:field>
            <flux:label>{{ __('Content') }}</flux:label>
            <flux:textarea wire:model="content" rows="12" placeholder="{{ __('Write your post…') }}" />
            <flux:error name="content" />
        </flux:field>

        <div class="grid gap-4 sm:grid-cols-2">
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
                <flux:select wire:model="status">
                    @foreach ($statuses as $s)
                        <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Publish Date') }}</flux:label>
                <flux:input wire:model="published_at" type="datetime-local" />
                <flux:error name="published_at" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Read Time') }}</flux:label>
                <flux:input wire:model="read_time" placeholder="5 min read" />
                <flux:error name="read_time" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Image Path') }}</flux:label>
                <flux:input wire:model="image_path" placeholder="/images/post.jpg" />
                <flux:error name="image_path" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Image Alt') }}</flux:label>
                <flux:input wire:model="image_alt" placeholder="{{ __('Alt text') }}" />
                <flux:error name="image_alt" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>{{ __('Tags') }}</flux:label>
            <div class="flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <label class="flex cursor-pointer items-center gap-1.5 rounded-full border border-zinc-200 px-3 py-1 text-sm dark:border-zinc-700 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-950">
                        <input type="checkbox" wire:model="tag_ids" value="{{ $tag->id }}" class="sr-only" />
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
            <flux:error name="tag_ids" />
        </flux:field>

        <flux:field>
            <flux:checkbox wire:model="featured" :label="__('Featured post')" />
        </flux:field>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">{{ __('Save Post') }}</flux:button>
        </div>
    </form>
</div>
