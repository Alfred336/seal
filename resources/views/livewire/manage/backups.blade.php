<div class="flex flex-col gap-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <flux:heading size="lg" class="font-bold tracking-tight text-zinc-800 dark:text-zinc-200">{{ __('Backups') }}</flux:heading>
            <flux:text class="text-xs text-zinc-400 mt-0.5">{{ __('Create database/file archives and download or delete previous snapshot versions.') }}</flux:text>
        </div>

        <div class="flex items-center gap-2">
            {{-- Manual Backup Triggers --}}
            <flux:button wire:click="generateBackup(true)" wire:loading.attr="disabled" variant="ghost" icon="circle-stack" class="shadow-sm">
                {{ __('Database Only') }}
            </flux:button>
            <flux:button wire:click="generateBackup(false)" wire:loading.attr="disabled" variant="primary" icon="arrow-path" class="shadow-sm">
                {{ __('Generate Full Backup') }}
            </flux:button>
        </div>
    </div>

    {{-- Loading indicator panel --}}
    <div wire:loading class="w-full">
        <div class="flex items-center gap-3 p-4 bg-primary/10 border border-primary/20 rounded-2xl text-primary text-sm font-semibold">
            <span class="animate-spin inline-block w-4 h-4 border-2 border-current border-t-transparent rounded-full"></span>
            <span>{{ __('Generating backup archive file, please wait... This may take a few moments.') }}</span>
        </div>
    </div>

    {{-- Backups list container --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700/80 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th>{{ __('Filename') }}</th>
                        <th>{{ __('Size') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th class="w-px"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/60">
                    @forelse ($backupFiles as $file)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                            <td class="font-medium text-zinc-800 dark:text-zinc-100 font-mono text-xs">
                                {{ $file['name'] }}
                            </td>
                            <td class="text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                {{ number_format($file['size'] / 1024 / 1024, 2) }} MB
                            </td>
                            <td class="text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                                <span>{{ date('M j, Y, h:i A', $file['last_modified']) }}</span>
                                <span class="text-xs text-zinc-405 dark:text-zinc-500 ml-1.5">({{ \Carbon\Carbon::createFromTimestamp($file['last_modified'])->diffForHumans() }})</span>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="flex justify-end gap-1">
                                    <flux:button
                                        wire:click="downloadBackup('{{ $file['path'] }}')"
                                        size="sm" variant="ghost" icon="arrow-down-tray"
                                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-white"
                                        title="{{ __('Download Archive') }}"
                                    />
                                    <flux:button
                                        wire:click="deleteBackup('{{ $file['path'] }}')"
                                        wire:confirm="{{ __('Permanently delete this backup archive?') }}"
                                        size="sm" variant="ghost" icon="trash"
                                        class="text-rose-450 hover:text-rose-600 dark:hover:text-rose-300"
                                        title="{{ __('Delete Archive') }}"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center text-zinc-400 dark:text-zinc-500">
                                <flux:icon icon="circle-stack" class="size-8 mx-auto mb-2 opacity-50" />
                                <p class="text-sm font-medium">{{ __('No backups found on this server.') }}</p>
                                <p class="text-xs text-zinc-400 mt-1">{{ __('Click one of the buttons above to generate a snapshot.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
