<script lang="ts">
    import { router, Link } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import { Icon } from '@/components/icon';
    import {
        DropdownMenuGroup,
        DropdownMenuItem,
        DropdownMenuLabel,
        DropdownMenuSeparator,
    } from '@/components/ui/dropdown-menu';
    import UserInfo from '@/layouts/components/user-info.svelte';
    import { toUrl } from '@/lib/utils';
    import { logout } from '@/routes';
    import { edit } from '@/routes/profile';
    import type { User } from '@/types';

    let {
        user,
        children,
    }: {
        user: User;
        children?: Snippet;
    } = $props();
</script>

<DropdownMenuLabel class="p-0 font-normal">
    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
        <UserInfo {user} showEmail={true} />
    </div>
</DropdownMenuLabel>
<DropdownMenuSeparator />
{#if children}
    {@render children?.()}
    <DropdownMenuSeparator />
{/if}
<DropdownMenuGroup>
    <DropdownMenuItem>
        {#snippet child({ props })}
            <Link {...props} href={toUrl(edit())}>
                <Icon
                    lucide="SettingsIcon"
                    tabler="IconSettings"
                    hugeicons="Settings01Icon"
                    phosphor="GearIcon"
                    remixicon="RiSettings3Line"
                    class="size-4"
                />
                Settings
            </Link>
        {/snippet}
    </DropdownMenuItem>
</DropdownMenuGroup>
<DropdownMenuSeparator />
<DropdownMenuItem class="w-full">
    {#snippet child({ props })}
        <button {...props} onclick={() => router.post(logout().url)}>
            <Icon
                lucide="LogOutIcon"
                tabler="IconLogout"
                hugeicons="Logout01Icon"
                phosphor="SignOutIcon"
                remixicon="RiLogoutBoxRLine"
                class="size-4"
            />
            Log out
        </button>
    {/snippet}
</DropdownMenuItem>
