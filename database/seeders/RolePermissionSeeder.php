<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * @var array<string, list<Permission>>
     */
    private const ROLE_PERMISSIONS = [
        'admin' => [],

        'editor' => [
            Permission::PostsView,
            Permission::PostsCreate,
            Permission::PostsManageAll,
            Permission::PostsPublish,
            Permission::CategoriesManage,
            Permission::TagsManage,
            Permission::ServicesView,
            Permission::ServicesManage,
            Permission::ProjectsView,
            Permission::ProjectsManage,
        ],

        'author' => [
            Permission::PostsView,
            Permission::PostsCreate,
            Permission::PostsUpdateOwn,
            Permission::PostsDeleteOwn,
        ],

        'support' => [
            Permission::ContactSubmissionsView,
            Permission::ContactSubmissionsUpdate,
            Permission::CallRequestsView,
            Permission::CallRequestsUpdate,
            Permission::ProjectRequestsView,
            Permission::ProjectRequestsUpdate,
            Permission::SubscriptionsView,
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Permission::cases() as $permission) {
            PermissionModel::findOrCreate($permission->value, 'web');
        }

        foreach (Role::cases() as $role) {
            $roleModel = RoleModel::findOrCreate($role->value, 'web');

            if ($role === Role::Admin) {
                $roleModel->syncPermissions(PermissionModel::all());

                continue;
            }

            $permissions = collect(self::ROLE_PERMISSIONS[$role->value])
                ->map(fn (Permission $permission) => $permission->value)
                ->all();

            $roleModel->syncPermissions($permissions);
        }

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@sealtech.test'],
            [
                'name' => 'SealTech Admin',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'Administrator',
            ],
        );

        if (! $admin->hasRole(Role::Admin->value)) {
            $admin->assignRole(Role::Admin->value);
        }
    }
}
