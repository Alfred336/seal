<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\PermissionRegistrar;

/**
 * RolePermissionSeeder
 *
 * Seeds all Spatie permission strings (sourced from App\Enums\Permission)
 * and assigns them to roles (sourced from App\Enums\Role).
 *
 * Permission strings must match the @can() directives used in all Blade
 * views and the Gate::define() calls in AppServiceProvider exactly.
 *
 * ┌─────────────────────────────────────────────────────────────────────┐
 * │  Permission string          │ Blade directive used                  │
 * ├─────────────────────────────────────────────────────────────────────┤
 * │  posts.view                 │ @can('posts.view')                    │
 * │  posts.create               │ @can('posts.create')                  │
 * │  posts.update-own           │ auth()->user()->can('posts.update-own')│
 * │  posts.delete-own           │ auth()->user()->can('posts.delete-own')│
 * │  posts.manage-all           │ auth()->user()->can('posts.manage-all')│
 * │  posts.publish              │ (reserved for future publish workflow) │
 * │  categories.manage          │ @can('categories.manage')             │
 * │  tags.manage                │ @can('tags.manage')                   │
 * │  services.view              │ @can('services.view')                 │
 * │  services.manage            │ @can('services.manage')               │
 * │  projects.view              │ @can('projects.view')                 │
 * │  projects.manage            │ @can('projects.manage')               │
 * │  contact-submissions.view   │ @can('contact-submissions.view')      │
 * │  contact-submissions.update │ @can('contact-submissions.update')    │
 * │  call-requests.view         │ @can('call-requests.view')            │
 * │  call-requests.update       │ @can('call-requests.update')          │
 * │  project-requests.view      │ @can('project-requests.view')         │
 * │  project-requests.update    │ @can('project-requests.update')       │
 * │  subscriptions.view         │ @can('subscriptions.view')            │
 * │  subscriptions.manage       │ @can('subscriptions.manage')          │
 * │  users.view                 │ @can('users.view')                    │
 * │  users.manage               │ @can('users.manage')                  │
 * └─────────────────────────────────────────────────────────────────────┘
 *
 * Role hierarchy:
 *   admin   → all permissions (via syncPermissions(all))
 *   editor  → blog + content + taxonomy management
 *   author  → own post create/update/delete only
 *   support → inquiry management + subscription viewing
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Defines which Permission enum cases each role receives.
     *
     * Notes:
     * - admin is handled separately: it receives ALL permissions.
     * - Permission strings must exactly match App\Enums\Permission values.
     * - Adding a new permission to the enum automatically seeds it; you only
     *   need to add it here to assign it to the appropriate role(s).
     *
     * @var array<string, list<Permission>>
     */
    private const ROLE_PERMISSIONS = [

        // ─────────────────────────────────────────────────────────────────
        // admin: receives ALL permissions — handled via syncPermissions(all)
        //        below. Leave this array empty.
        // ─────────────────────────────────────────────────────────────────
        'admin' => [],

        // ─────────────────────────────────────────────────────────────────
        // editor: full control over blog content, taxonomy, services &
        //         projects.  Cannot manage users or view inquiries/leads.
        // ─────────────────────────────────────────────────────────────────
        'editor' => [
            // Blog — posts
            Permission::PostsView,        // @can('posts.view')        → sidebar Blog section
            Permission::PostsCreate,      // @can('posts.create')      → "New Post" button
            Permission::PostsManageAll,   // can('posts.manage-all')   → edit/delete any post
            Permission::PostsPublish,     // reserved for publish workflow

            // Blog — taxonomy
            Permission::CategoriesManage, // @can('categories.manage') → categories CRUD
            Permission::TagsManage,       // @can('tags.manage')        → tags CRUD

            // Content — services & projects
            Permission::ServicesView,     // @can('services.view')     → sidebar Services
            Permission::ServicesManage,   // @can('services.manage')   → services CRUD + reorder
            Permission::ProjectsView,     // @can('projects.view')     → sidebar Projects
            Permission::ProjectsManage,   // @can('projects.manage')   → projects CRUD + toggles
            Permission::CareersManage,
        ],

        // ─────────────────────────────────────────────────────────────────
        // author: can write and manage their own posts only.
        //         Cannot create categories/tags, manage services/projects,
        //         or access any admin areas.
        // ─────────────────────────────────────────────────────────────────
        'author' => [
            Permission::PostsView,        // @can('posts.view')        → can see posts index
            Permission::PostsCreate,      // @can('posts.create')      → "New Post" button
            Permission::PostsUpdateOwn,   // can('posts.update-own')   → edit own posts only
            Permission::PostsDeleteOwn,   // can('posts.delete-own')   → delete own posts only
        ],

        // ─────────────────────────────────────────────────────────────────
        // support: handles all incoming leads/inquiries and the newsletter
        //          subscriber list.  No access to blog or content sections.
        // ─────────────────────────────────────────────────────────────────
        'support' => [
            // Inquiries — contact form
            Permission::ContactSubmissionsView,   // @can('contact-submissions.view')
            Permission::ContactSubmissionsUpdate, // @can('contact-submissions.update')

            // Inquiries — scheduled call requests
            Permission::CallRequestsView,         // @can('call-requests.view')
            Permission::CallRequestsUpdate,       // @can('call-requests.update')

            // Inquiries — project brief requests
            Permission::ProjectRequestsView,      // @can('project-requests.view')
            Permission::ProjectRequestsUpdate,    // @can('project-requests.update')

            // Marketing — newsletter subscribers
            Permission::SubscriptionsView,        // @can('subscriptions.view')    → list subs
            Permission::SubscriptionsManage,      // @can('subscriptions.manage')  → unsubscribe + export CSV
        ],
    ];

    /**
     * Run the seeder.
     *
     * 1. Clear the Spatie permission cache to avoid stale data.
     * 2. Create every permission defined in the Permission enum (idempotent).
     * 3. Create every role defined in the Role enum (idempotent).
     * 4. Sync each role's permissions from ROLE_PERMISSIONS.
     * 5. Ensure the default admin user exists and holds the admin role.
     */
    public function run(): void
    {
        // ── Step 1: Clear permission cache ─────────────────────────────
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // ── Step 2: Create / ensure all permissions exist ──────────────
        // Iterating Permission::cases() ensures every enum value is seeded
        // automatically when new permissions are added to the enum.
        foreach (Permission::cases() as $permission) {
            PermissionModel::findOrCreate($permission->value, 'web');
        }

        // ── Step 3 & 4: Create roles and sync their permissions ─────────
        foreach (Role::cases() as $role) {
            $roleModel = RoleModel::findOrCreate($role->value, 'web');

            // Admin gets every permission in the system.
            if ($role === Role::Admin) {
                $roleModel->syncPermissions(PermissionModel::all());
                continue;
            }

            // All other roles receive only their declared permission set.
            $permissions = collect(self::ROLE_PERMISSIONS[$role->value])
                ->map(fn (Permission $permission) => $permission->value)
                ->all();

            $roleModel->syncPermissions($permissions);
        }

        // ── Step 5: Create the default admin user ──────────────────────
        // Uses firstOrCreate so re-seeding is safe and idempotent.
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@sealtech.test'],
            [
                'name'              => 'SealTech Admin',
                'password'          => bcrypt('password'),
                'email_verified_at' => now(),
            ],
        );

        // Assign the admin role only if not already assigned.
        if (! $admin->hasRole(Role::Admin->value)) {
            $admin->assignRole(Role::Admin->value);
        }
    }
}
