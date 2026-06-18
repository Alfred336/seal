<?php

namespace App\Livewire\Manage;

use App\Enums\Permission;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role as RoleModel;

/**
 * Roles Livewire Component
 *
 * Manages the full CRUD lifecycle for Spatie roles, including inline
 * permission assignment via grouped checkboxes.
 *
 * Access: requires the "roles.manage" permission (admin-only by design).
 *
 * Guards:
 *  - The built-in "admin" role cannot be renamed, edited, or deleted via
 *    the UI.  Its permissions are always synced to ALL via the seeder and
 *    the Gate::before() bypass in AppServiceProvider.
 */
#[Title('Roles & Permissions')]
#[Layout('layouts.app')]
class Roles extends Component
{
    // ── Form state ─────────────────────────────────────────────────────

    /** Whether the create/edit form panel is visible. */
    /** Controls flux:modal visibility via wire:model. */
    public bool $showModal = false;

    /** ID of the role being edited; null means "create new role" mode. */
    public ?int $editingId = null;

    // ── Form fields ────────────────────────────────────────────────────

    /** The role name input. */
    public string $roleName = '';

    /**
     * The currently checked permissions in the form.
     *
     * @var list<string>
     */
    public array $selectedPermissions = [];

    // ── Permission group definitions ───────────────────────────────────

    /**
     * Groups every permission string (from the Permission enum) into
     * labelled sections for the checkbox UI in the view.
     *
     * Ordering within each group intentionally matches the sidebar order
     * so the UI is predictable for administrators.
     *
     * @return array<string, list<string>>
     */
    public function permissionGroups(): array
    {
        return [
            // Blog — post lifecycle permissions
            'Blog' => [
                Permission::PostsView->value,        // view posts index
                Permission::PostsCreate->value,      // create new post
                Permission::PostsUpdateOwn->value,   // edit own posts only
                Permission::PostsDeleteOwn->value,   // delete own posts only
                Permission::PostsManageAll->value,   // edit / delete any post
                Permission::PostsPublish->value,     // publish workflow
            ],

            // Taxonomy — category & tag management
            'Taxonomy' => [
                Permission::CategoriesManage->value,
                Permission::TagsManage->value,
            ],

            // Content — services & projects
            'Content' => [
                Permission::ServicesView->value,
                Permission::ServicesManage->value,
                Permission::ProjectsView->value,
                Permission::ProjectsManage->value,
            ],

            // Inquiries — incoming leads & contact forms
            'Inquiries' => [
                Permission::ContactSubmissionsView->value,
                Permission::ContactSubmissionsUpdate->value,
                Permission::CallRequestsView->value,
                Permission::CallRequestsUpdate->value,
                Permission::ProjectRequestsView->value,
                Permission::ProjectRequestsUpdate->value,
            ],

            // Marketing — newsletter subscribers
            'Marketing' => [
                Permission::SubscriptionsView->value,
                Permission::SubscriptionsManage->value,
            ],

            // Administration — user & role management (high privilege)
            'Administration' => [
                Permission::UsersView->value,
                Permission::UsersManage->value,
                Permission::RolesManage->value,
            ],
        ];
    }

    // ── Actions ────────────────────────────────────────────────────────

    /**
     * Open the create-mode form panel.
     * Resets all form fields first to avoid stale values from a previous edit.
     */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    /**
     * Persist a brand-new role with the selected permissions.
     *
     * Requires: roles.manage
     */
    public function create(): void
    {
        // Server-side permission guard (double-checks middleware)
        abort_unless(auth()->user()->can(Permission::RolesManage->value), 403);

        $this->validate([
            'roleName'              => ['required', 'string', 'max:64', 'unique:roles,name'],
            'selectedPermissions'   => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = RoleModel::create(['name' => $this->roleName, 'guard_name' => 'web']);
        $role->syncPermissions($this->selectedPermissions);

        $this->resetForm();
        $this->showModal = false;

        $this->dispatch('notify', message: __('Role ":name" created.', ['name' => $this->roleName]));
    }

    /**
     * Load an existing role into the form for editing.
     *
     * @param int $id The Spatie role ID.
     */
    public function startEdit(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::RolesManage->value), 403);

        $role = RoleModel::with('permissions')->findOrFail($id);

        $this->editingId            = $id;
        $this->roleName             = $role->name;
        $this->selectedPermissions  = $role->permissions->pluck('name')->all();
        $this->showModal            = true;
    }

    /**
     * Save changes to an existing role: rename + re-sync permissions.
     *
     * Requires: roles.manage
     * Guard: the "admin" role cannot be modified via the UI.
     */
    public function saveEdit(): void
    {
        abort_unless(auth()->user()->can(Permission::RolesManage->value), 403);

        $role = RoleModel::findOrFail($this->editingId);

        // Hard-guard: the admin role is a system role and must not be changed.
        abort_if(
            $role->name === 'admin',
            403,
            __('The admin role is a system role and cannot be modified.')
        );

        $this->validate([
            'roleName'              => ['required', 'string', 'max:64', "unique:roles,name,{$this->editingId}"],
            'selectedPermissions'   => ['array'],
            'selectedPermissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->update(['name' => $this->roleName]);
        $role->syncPermissions($this->selectedPermissions);

        $this->resetForm();
        $this->showModal  = false;
        $this->editingId  = null;

        $this->dispatch('notify', message: __('Role updated successfully.'));
    }

    /**
     * Permanently delete a role.
     *
     * Requires: roles.manage
     * Guard: cannot delete the "admin" system role.
     *
     * @param int $id The Spatie role ID.
     */
    public function delete(int $id): void
    {
        abort_unless(auth()->user()->can(Permission::RolesManage->value), 403);

        $role = RoleModel::findOrFail($id);

        // Hard-guard: the admin role must never be removable via the UI.
        abort_if(
            $role->name === 'admin',
            403,
            __('The admin role is a system role and cannot be deleted.')
        );

        $roleName = $role->name;
        $role->delete();

        $this->dispatch('notify', message: __('Role ":name" deleted.', ['name' => $roleName]));
    }

    /**
     * Close the create/edit form and reset all fields.
     */
    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal  = false;
        $this->editingId  = null;
    }

    // ── Render ─────────────────────────────────────────────────────────

    /**
     * Load all roles (with their permission count) and pass the grouped
     * permission map to the view.
     */
    public function render(): View
    {
        // Eager-load permission count for the badge column
        $roles = RoleModel::withCount('permissions')->orderBy('name')->get();

        return view('livewire.manage.roles', [
            'roles'            => $roles,
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────

    /**
     * Reset all form-related public properties to their default values.
     */
    private function resetForm(): void
    {
        $this->reset('roleName', 'selectedPermissions');
    }
}
