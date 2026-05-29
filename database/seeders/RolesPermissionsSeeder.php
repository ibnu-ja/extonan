<?php

namespace Database\Seeders;

use App\Enums\Permission;
use App\Enums\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\Models\Role as SpatieRole;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            Permission::POST_CREATE->value => [Role::EDITOR->value, Role::AUTHOR->value, Role::CONTRIBUTOR->value],
            Permission::POST_READ_ANY->value => [Role::EDITOR->value], // unpublished
            Permission::POST_READ_SELF->value => [Role::EDITOR->value, Role::AUTHOR->value, Role::CONTRIBUTOR->value], // unpublished
            Permission::POST_UPDATE_ANY->value => [Role::EDITOR->value],
            Permission::POST_UPDATE_SELF->value => [Role::EDITOR->value, Role::AUTHOR->value, Role::CONTRIBUTOR->value],
            Permission::POST_DELETE_ANY->value => [Role::EDITOR->value],
            Permission::POST_DELETE_SELF->value => [Role::EDITOR->value, Role::AUTHOR->value, Role::CONTRIBUTOR->value],
            Permission::POST_PUBLISH_SELF->value => [Role::EDITOR->value, Role::AUTHOR->value],
            Permission::POST_PUBLISH_ANY->value => [Role::EDITOR->value],

            Permission::USER_INVITE->value => [], // no specific roles mentioned
            Permission::USER_READ_ANY->value => [], // no specific roles mentioned
            Permission::USER_READ_SELF->value => [Role::EDITOR->value, Role::AUTHOR->value, Role::CONTRIBUTOR->value],
            Permission::USER_DELETE_ANY->value => [], // no specific roles mentioned
            Permission::USER_DELETE_SELF->value => [Role::EDITOR->value, Role::AUTHOR->value, Role::CONTRIBUTOR->value],
            Permission::USER_EDIT_SELF->value => [], // no specific roles mentioned
            Permission::USER_EDIT_ANY->value => [], // no specific roles mentioned
        ];

        // Create permissions and assign them to roles
        foreach ($permissions as $permissionName => $assignedRoles) {
            $permission = SpatiePermission::findOrCreate($permissionName);

            foreach ($assignedRoles as $roleName) {
                $role = SpatieRole::findOrCreate($roleName);
                $role->givePermissionTo($permission);
            }
        }

        // Admin gets all permissions
        $adminRole = SpatieRole::findOrCreate(Role::ADMIN->value);
        $adminRole->syncPermissions(SpatiePermission::all());
    }
}
