import { page } from '@inertiajs/svelte';

export type AuthState = {
    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasPermissions.php#L252 HasPermissions::checkPermissionTo}
     */
    can: (permission: App.Enums.Permission) => boolean;

    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasPermissions.php#L252 HasPermissions::checkPermissionTo}
     */
    cannot: (permission: App.Enums.Permission) => boolean;

    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasPermissions.php#L266 HasPermissions::hasAnyPermission}
     */
    canAny: (permissions: App.Enums.Permission[]) => boolean;

    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasRoles.php#L321 HasRoles::hasRole}
     */
    hasRole: (role: App.Enums.Role) => boolean;

    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasRoles.php#L380 HasRoles::hasAnyRole}
     */
    hasAnyRole: (roles: App.Enums.Role[]) => boolean;

    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasRoles.php#L390 HasRoles::hasAllRoles}
     */
    hasAllRoles: (roles: App.Enums.Role[]) => boolean;

    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasRoles.php#L424 HasRoles::hasExactRoles}
     */
    hasExactRoles: (roles: App.Enums.Role[]) => boolean;

    /**
     * @see {@link https://github.com/spatie/laravel-permission/blob/2f54798b9e3381ec526201f74ae2831c096cfa8a/src/Traits/HasRoles.php#L321 HasRoles::hasRole}
     */
    unlessRole: (role: App.Enums.Role) => boolean;
};

export function useAuth(): AuthState {
    const permissions = $derived(page.props.auth?.permissions ?? []);
    const roles = $derived(page.props.auth?.roles ?? []);

    return {
        can: (permission: App.Enums.Permission): boolean => {
            return permissions.includes(permission);
        },

        cannot: (permission: App.Enums.Permission): boolean => {
            return !permissions.includes(permission);
        },

        canAny: (checkPermissions: App.Enums.Permission[]): boolean => {
            return checkPermissions.some((p) => permissions.includes(p));
        },

        hasRole: (role: App.Enums.Role): boolean => {
            return roles.includes(role);
        },

        hasAnyRole: (checkRoles: App.Enums.Role[]): boolean => {
            return checkRoles.some((r) => roles.includes(r));
        },

        hasAllRoles: (checkRoles: App.Enums.Role[]): boolean => {
            return checkRoles.every((r) => roles.includes(r));
        },

        hasExactRoles: (checkRoles: App.Enums.Role[]): boolean => {
            if (roles.length !== checkRoles.length) {
                return false;
            }

            return checkRoles.every((r) => roles.includes(r));
        },

        unlessRole: (role: App.Enums.Role): boolean => {
            return !roles.includes(role);
        },
    };
}
