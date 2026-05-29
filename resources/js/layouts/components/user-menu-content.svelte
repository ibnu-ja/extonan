<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import LogOut from 'lucide-svelte/icons/log-out';
    import Settings from 'lucide-svelte/icons/settings';
    import type { Snippet } from 'svelte';
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
    <DropdownMenuItem class="w-full">
        {#snippet child({ props })}
            <a
                {...props}
                href={toUrl(edit())}
                class="flex w-full cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground disabled:cursor-default"
            >
                <Settings class="size-4" />
                Settings
            </a>
        {/snippet}
    </DropdownMenuItem>
</DropdownMenuGroup>
<DropdownMenuSeparator />
<DropdownMenuItem class="w-full">
    {#snippet child({ props })}
        <button
            {...props}
            onclick={() => router.post(logout().url)}
            class="flex w-full cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground disabled:cursor-default"
        >
            <LogOut class="size-4" />
            Log out
        </button>
    {/snippet}
</DropdownMenuItem>
