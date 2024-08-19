<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $roles = [
//            'admin',
//            'editor',
//            'author',
//            'contributor',
//            'subscriber'
//        ];

        $permissions = [
            'post.create' => ['editor', 'author'],
            'post.read.any' => ['editor'], // unpublished
            'post.read.self' => ['editor', 'author', 'contributor'], // unpublished
            'post.update.any' => ['editor'],
            'post.update.self' => ['editor', 'author', 'contributor'],
            'post.delete.any' => ['editor'],
            'post.delete.self' => ['editor', 'author', 'contributor'],
            'post.publish.self' => ['editor', 'author'],
            'post.publish.any' => ['editor'],

            'user.invite' => [], // no specific roles mentioned
            'user.read.any' => [], // no specific roles mentioned
            'user.read.self' => ['editor', 'author', 'contributor'],
            'user.delete.any' => [], // no specific roles mentioned
            'user.delete.self' => ['editor', 'author', 'contributor'],
            'user.edit.self' => [], // no specific roles mentioned
            'user.edit.any' => [], // no specific roles mentioned
        ];

        // Create permissions and assign them to roles
        foreach ($permissions as $permissionName => $assignedRoles) {
            $permission = Permission::findOrCreate($permissionName);

            foreach ($assignedRoles as $roleName) {
                $role = Role::findOrCreate($roleName);
                $role->givePermissionTo($permission);
            }
        }
    }
}
