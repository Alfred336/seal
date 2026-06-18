<div class="flex flex-col gap-4">

    {{-- ─────────────────────────────────────────────────────────────────────
         Page Header
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="lg">{{ __('Roles & Permissions') }}</flux:heading>
            <flux:text class="text-zinc-500 text-sm mt-0.5">
                {{ __('Manage roles and assign permission strings to each role.') }}
            </flux:text>
        </div>

        {{-- Requires: roles.manage --}}
        @can('roles.manage')
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                {{ __('New Role') }}
            </flux:button>
        @endcan
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Create / Edit Role Modal
         Controlled by $showModal via wire:model.
         Permission checkboxes are grouped by functional section.
    ─────────────────────────────────────────────────────────────────────── --}}
    <flux:modal wire:model="showModal" class="md:w-4xl">
        <div class="space-y-5 p-1">

            <flux:heading>
                {{ $editingId ? __('Edit Role') : __('New Role') }}
            </flux:heading>

            {{-- ── Role Name ─────────────────────────────────────────── --}}
            <flux:field class="max-w-sm">
                <flux:label>{{ __('Role Name') }}</flux:label>
                <flux:input
                    wire:model="roleName"
                    placeholder="{{ __('e.g. moderator') }}"
                    :disabled="$editingId && $roleName === 'admin'"
                />
                <flux:error name="roleName" />
                @if ($editingId && $roleName === 'admin')
                    <flux:text class="text-xs text-amber-500 mt-1">
                        {{ __('The admin role name cannot be changed.') }}
                    </flux:text>
                @endif
            </flux:field>

            {{-- ── Permission Groups ─────────────────────────────────── --}}
            <div>
                <flux:label class="mb-3">{{ __('Permissions') }}</flux:label>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($permissionGroups as $groupLabel => $permissions)
                        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500 mb-2">
                                {{ __($groupLabel) }}
                            </p>
                            <div class="flex flex-col gap-1.5">
                                @foreach ($permissions as $permission)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            wire:model="selectedPermissions"
                                            value="{{ $permission }}"
                                            class="rounded border-zinc-300 text-blue-600 shadow-sm focus:ring-blue-500 dark:border-zinc-600 dark:bg-zinc-700"
                                            @if ($editingId && $roleName === 'admin') disabled @endif
                                        />
                                        <span class="text-xs text-zinc-700 dark:text-zinc-300 font-mono group-hover:text-zinc-900 dark:group-hover:text-white">
                                            {{ $permission }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <flux:error name="selectedPermissions" />
            </div>

            {{-- ── Modal footer ──────────────────────────────────────── --}}
            <div class="flex justify-end gap-2 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                @if ($editingId)
                    <flux:button
                        wire:click="saveEdit"
                        variant="primary"
                        :disabled="$editingId && $roleName === 'admin'"
                    >
                        {{ __('Save Changes') }}
                    </flux:button>
                @else
                    <flux:button wire:click="create" variant="primary">{{ __('Create Role') }}</flux:button>
                @endif
                <flux:button wire:click="closeModal" variant="ghost">{{ __('Cancel') }}</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- ─────────────────────────────────────────────────────────────────────
         Roles Table
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
        <table class="w-full text-sm">
            <thead class="bg-zinc-50 text-left dark:bg-zinc-900">
                <tr>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Role') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Type') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Permissions') }}</th>
                    <th class="px-4 py-3 font-medium text-zinc-500">{{ __('Assigned') }}</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse ($roles as $role)
                    <tr>
                        <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ $role->name }}</td>

                        <td class="px-4 py-3">
                            @if ($role->name === 'admin')
                                <flux:badge color="amber" size="sm" icon="lock-closed">{{ __('System') }}</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">{{ __('Custom') }}</flux:badge>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <flux:badge color="blue" size="sm">{{ $role->permissions_count }}</flux:badge>
                        </td>

                        {{-- Permission pill preview (first 4 + overflow count) --}}
                        <td class="px-4 py-3">
                            @if ($role->name === 'admin')
                                <span class="text-xs text-zinc-400 italic">{{ __('All permissions') }}</span>
                            @else
                                @php
                                    $rolePerms = $role->permissions ?? collect();
                                    $shown     = $rolePerms->take(4);
                                    $remaining = $rolePerms->count() - $shown->count();
                                @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($shown as $perm)
                                        <span class="inline-flex items-center rounded-full bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 text-xs font-mono text-zinc-600 dark:text-zinc-300">
                                            {{ $perm->name }}
                                        </span>
                                    @endforeach
                                    @if ($remaining > 0)
                                        <span class="inline-flex items-center rounded-full bg-zinc-200 dark:bg-zinc-700 px-2 py-0.5 text-xs text-zinc-500">
                                            +{{ $remaining }} {{ __('more') }}
                                        </span>
                                    @endif
                                    @if ($rolePerms->isEmpty())
                                        <span class="text-xs text-zinc-400 italic">{{ __('None') }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- ─────────────────────────────────────────────────
                             Actions: Edit (opens modal) + Delete
                             Admin role row is locked — no edit/delete buttons.
                        ───────────────────────────────────────────────────── --}}
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @can('roles.manage')
                                    @if ($role->name !== 'admin')
                                        <flux:button wire:click="startEdit({{ $role->id }})" size="sm" variant="ghost" icon="pencil" />
                                        <flux:button
                                            wire:click="delete({{ $role->id }})"
                                            wire:confirm="{{ __('Delete role \":name\"? Users will lose its permissions.', ['name' => $role->name]) }}"
                                            size="sm" variant="ghost" icon="trash"
                                        />
                                    @else
                                        <flux:icon icon="lock-closed" class="size-4 text-zinc-300 dark:text-zinc-600 mx-2" />
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-zinc-400">{{ __('No roles found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Permission Reference — read-only overview of all available strings
    ─────────────────────────────────────────────────────────────────────── --}}
    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 p-4">
        <flux:heading size="sm" class="mb-1">{{ __('All Available Permissions') }}</flux:heading>
        <p class="text-xs text-zinc-400 mb-4">
            {{ __('Permissions are enum-driven and seeded automatically. They cannot be created via the UI.') }}
        </p>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($permissionGroups as $groupLabel => $permissions)
                <div class="rounded-lg border border-zinc-100 dark:border-zinc-800 p-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500 mb-2">
                        {{ __($groupLabel) }}
                    </p>
                    <ul class="flex flex-col gap-1">
                        @foreach ($permissions as $permission)
                            <li class="text-xs font-mono text-zinc-500 dark:text-zinc-400">{{ $permission }}</li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

</div>
