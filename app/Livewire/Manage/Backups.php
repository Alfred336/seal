<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Backups')]
#[Layout('layouts.app')]
class Backups extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    /**
     * Run a manual database backup from the UI.
     */
    public function generateBackup(bool $onlyDb = false): void
    {
        abort_unless(auth()->user()->can(Permission::BackupsManage->value), 403);

        try {
            // Set 5 minutes execution limit to prevent script timeouts
            set_time_limit(300);

            $params = [];
            if ($onlyDb) {
                $params['--only-db'] = true;
            }

            Artisan::call('backup:run', $params);

            Log::info('Backup generated manually from Administration area', [
                'user_id' => auth()->id(),
                'only_db' => $onlyDb,
            ]);

            $this->dispatch('notify', message: __('Backup generated successfully!'));
        } catch (\Exception $e) {
            Log::error('Manual backup failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('notify', message: __('Backup failed: ').$e->getMessage());
        }
    }

    /**
     * Download backup file securely.
     */
    public function downloadBackup(string $path)
    {
        abort_unless(auth()->user()->can(Permission::BackupsManage->value), 403);

        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
        $disk = Storage::disk($diskName);

        if (! $disk->exists($path)) {
            $this->dispatch('notify', message: __('Backup file does not exist.'));

            return null;
        }

        return $disk->download($path);
    }

    /**
     * Delete backup file from disk.
     */
    public function deleteBackup(string $path): void
    {
        abort_unless(auth()->user()->can(Permission::BackupsManage->value), 403);

        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
        $disk = Storage::disk($diskName);

        if ($disk->exists($path)) {
            $disk->delete($path);
            Log::info('Backup file deleted manually', [
                'user_id' => auth()->id(),
                'path' => $path,
            ]);
            $this->dispatch('notify', message: __('Backup deleted successfully.'));
        } else {
            $this->dispatch('notify', message: __('Backup file not found.'));
        }
    }

    public function render(): View
    {
        abort_unless(auth()->user()->can(Permission::BackupsManage->value), 403);

        $diskName = config('backup.backup.destination.disks')[0] ?? 'local';
        $backupName = config('backup.backup.name') ?? env('APP_NAME', 'laravel-backup');
        $disk = Storage::disk($diskName);

        $backupFiles = [];

        if ($disk->exists($backupName)) {
            $files = $disk->allFiles($backupName);

            foreach ($files as $file) {
                if (str_ends_with($file, '.zip')) {
                    $backupFiles[] = [
                        'path' => $file,
                        'name' => basename($file),
                        'size' => $disk->size($file),
                        'last_modified' => $disk->lastModified($file),
                    ];
                }
            }

            $backupFiles = collect($backupFiles)
                ->filter(function (array $file): bool {
                    $name = strtolower($file['name']);
                    $search = strtolower($this->search);

                    if ($search !== '' && str_contains($name, $search) === false) {
                        return false;
                    }

                    if ($this->filter === 'database' && str_contains($name, 'database') === false && str_contains($name, 'db') === false) {
                        return false;
                    }

                    if ($this->filter === 'files' && str_contains($name, 'files') === false && str_contains($name, 'full') === false) {
                        return false;
                    }

                    return true;
                })
                ->sortByDesc('last_modified')
                ->values()
                ->all();
        }

        $perPage = 10;
        $pagedBackupFiles = collect($backupFiles)->slice(($this->getPage() - 1) * $perPage, $perPage)->values()->all();

        return view('livewire.manage.backups', [
            'backupFiles' => $pagedBackupFiles,
            'backupFilesPagination' => new LengthAwarePaginator(
                collect($backupFiles)->forPage($this->getPage(), $perPage)->values()->all(),
                count($backupFiles),
                $perPage,
                $this->getPage(),
                ['path' => request()->getRequestUri()]
            ),
        ]);
    }
}
