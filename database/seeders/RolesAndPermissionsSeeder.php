<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    private const string GUARD = 'web';

    /**
     * @var list<string>
     */
    private const array PERMISSIONS = [
        'admin.access',
        'users.view',
        'users.create',
        'users.update',
        'users.suspend',
        'users.restore',
        'users.reset-password',
        'users.manage-api-keys',
        'articles.view',
        'articles.create',
        'articles.update',
        'articles.delete',
        'articles.restore',
        'articles.publish',
        'categories.view',
        'categories.create',
        'categories.update',
        'categories.delete',
        'settings.manage',
    ];

    /**
     * @var list<string>
     */
    private const array MANAGER_PERMISSIONS = [
        'admin.access',
        'articles.view',
        'articles.create',
        'articles.update',
        'articles.delete',
        'articles.restore',
        'articles.publish',
        'categories.view',
        'categories.create',
        'categories.update',
        'categories.delete',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionRegistrar = app(PermissionRegistrar::class);
        $permissionRegistrar->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, self::GUARD);
        }

        $permissionRegistrar->forgetCachedPermissions();

        $admin = Role::findOrCreate('admin', self::GUARD);
        $manager = Role::findOrCreate('manager', self::GUARD);

        $admin->syncPermissions(self::PERMISSIONS);
        $manager->syncPermissions(self::MANAGER_PERMISSIONS);

        $permissionRegistrar->forgetCachedPermissions();
    }
}
