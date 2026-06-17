<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Users') }}</flux:heading>
        @can('create', App\Models\User::class)
            @if (!$showForm)
                <flux:button wire:click="showCreateForm" variant="primary" icon="plus">{{ __('New User') }}</flux:button>
            @endif
        @endcan
    </div>

    {{-- Create / Edit Form --}}
    @if ($showForm)
        <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm" class="mb-4">{{ $editingId ? __('Edit User') : __('New User') }}</flux:heading>
            <div class="grid gap-4 sm:grid-cols-2">
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Email') }}</flux:label>
                    <flux:input wire:model="email" type="email" />
                    <flux:error name="email" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ $editingId ? __('New Password (leave blank to keep)') : __('Password') }}</flux:label>
                    <flux:input wire:model="password" type="password" />
                    <flux:error name="password" />
                </flux:field>
                <flux:field>
                    <flux:label>{{ __('Role') }}</flux:label>
                    <flux:select wire:model="selectedRole">
                        <flux:select.option value="">{{ __('Select role…') }}</flux:select.option>
                        @foreach ($roles as $role)
                            <flux:select.option :value="$role->value">{{ ucfirst($role->value) }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:error name="selectedRole" />
                </flux:field>
            </div>
            <div class="mt-4 flex justify-end gap-2">
                @if ($editingId)
                    <flux:button wire:click="saveEdit" variant="primary">{{ __('Save Changes') }}</flux:button>
                @else
                    <flux:button wire:click="create" variant="primary">{{ __('Create User') }}</flux:button>
                @endif
                <flux:button wire:click="cancelForm" variant="ghost">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    @endif

    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search users…') }}" icon="magnifying-glass" class="max-w-xs" />

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Name') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Email') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Role') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Joined') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($users as $user)
                    <tr @class(['opacity-60' => !$user->isActive()])>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                            <div class="flex items-center gap-2">
                                <flux:avatar :name="$user->name" :initials="$user->displayInitials()" size="sm" />
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-zinc-500">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            @if ($role = $user->roles->first())
                                <flux:badge color="blue" size="sm">{{ ucfirst($role->name) }}</flux:badge>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-500 whitespace-nowrap">{{ $user->created_at->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <flux:badge :color="$user->isActive() ? 'green' : 'zinc'" size="sm">
                                {{ $user->isActive() ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @can('update', $user)
                                    <flux:button wire:click="startEdit({{ $user->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="toggleActive({{ $user->id }})"
                                        wire:confirm="{{ $user->isActive() ? __('Deactivate this user?') : __('Reactivate this user?') }}"
                                        size="sm" variant="ghost"
                                        :icon="$user->isActive() ? 'lock-closed' : 'lock-open'"
                                    />
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-400">{{ __('No users found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</div>
