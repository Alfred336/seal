<div class="flex flex-col gap-6">

    {{-- Stats Grid --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @php
            $cards = [
                ['label' => __('Users'),              'value' => $stats['users'],              'icon' => 'users',                   'show' => auth()->user()->can('viewAny', App\Models\User::class)],
                ['label' => __('Total Posts'),         'value' => $stats['posts_total'],         'icon' => 'document-text',           'show' => auth()->user()->can('viewAny', App\Models\Post::class)],
                ['label' => __('Published'),           'value' => $stats['posts_published'],     'icon' => 'check-circle',            'show' => auth()->user()->can('viewAny', App\Models\Post::class)],
                ['label' => __('Drafts'),              'value' => $stats['posts_draft'],         'icon' => 'pencil-square',           'show' => auth()->user()->can('viewAny', App\Models\Post::class)],
                ['label' => __('Services'),            'value' => $stats['services'],            'icon' => 'briefcase',               'show' => auth()->user()->can('viewAny', App\Models\Service::class)],
                ['label' => __('Projects'),            'value' => $stats['projects'],            'icon' => 'photo',                   'show' => auth()->user()->can('viewAny', App\Models\Project::class)],
                ['label' => __('Contact'),             'value' => $stats['contact_submissions'], 'icon' => 'envelope',                'show' => auth()->user()->can('viewAny', App\Models\ContactSubmission::class)],
                ['label' => __('Call Requests'),       'value' => $stats['call_requests'],       'icon' => 'phone',                   'show' => auth()->user()->can('viewAny', App\Models\CallRequest::class)],
                ['label' => __('Project Requests'),    'value' => $stats['project_requests'],    'icon' => 'clipboard-document-list', 'show' => auth()->user()->can('viewAny', App\Models\ProjectRequest::class)],
                ['label' => __('Subscribers'),         'value' => $stats['subscribers'],         'icon' => 'newspaper',               'show' => auth()->user()->can('viewAny', App\Models\Subscription::class)],
            ];
        @endphp

        @foreach ($cards as $card)
            @if ($card['show'])
                <div class="flex items-center gap-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900">
                    <flux:icon :icon="$card['icon']" class="size-8 shrink-0 text-zinc-400 dark:text-zinc-500" />
                    <div>
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($card['value']) }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $card['label'] }}</div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Recent Activity --}}
    <div class="grid gap-6 lg:grid-cols-2">

        @can('viewAny', App\Models\Post::class)
            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    <flux:heading size="sm">{{ __('Recent Posts') }}</flux:heading>
                    <flux:link :href="route('manage.posts.index')" wire:navigate class="text-xs">{{ __('View all') }}</flux:link>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($recentPosts as $post)
                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $post->title }}</p>
                                <p class="text-xs text-zinc-500">{{ $post->author?->name ?? __('Unknown') }}</p>
                            </div>
                            <flux:badge :color="$post->status->value === 'published' ? 'green' : 'zinc'" size="sm" class="ml-3 shrink-0">
                                {{ $post->status->value }}
                            </flux:badge>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-center text-sm text-zinc-400">{{ __('No posts yet.') }}</p>
                    @endforelse
                </div>
            </div>
        @endcan

        @can('viewAny', App\Models\ContactSubmission::class)
            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                    <flux:heading size="sm">{{ __('Recent Contact Submissions') }}</flux:heading>
                    <flux:link :href="route('manage.contact-submissions.index')" wire:navigate class="text-xs">{{ __('View all') }}</flux:link>
                </div>
                <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($recentSubmissions as $submission)
                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ $submission->name }}</p>
                                <p class="text-xs text-zinc-500">{{ $submission->email }} · {{ $submission->submitted_at->diffForHumans() }}</p>
                            </div>
                            <flux:badge :color="$submission->status->value === 'new' ? 'blue' : ($submission->status->value === 'reviewed' ? 'yellow' : 'zinc')" size="sm" class="ml-3 shrink-0">
                                {{ $submission->status->value }}
                            </flux:badge>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-center text-sm text-zinc-400">{{ __('No submissions yet.') }}</p>
                    @endforelse
                </div>
            </div>
        @endcan

    </div>
</div>
