<script lang="ts">
    import { Link, page, router } from '@inertiajs/svelte';
    import { Icon } from '@/components/icon';
    import * as DropdownMenu from '@/components/ui/dropdown-menu/index.js';
    import * as Sidebar from '@/components/ui/sidebar/index.js';
    import { useSidebar } from '@/components/ui/sidebar/index.js';
    import UserInfo from '@/layouts/components/user-info.svelte';
    import { toUrl } from '@/lib/utils';
    import { logout } from '@/routes';
    import { edit } from '@/routes/profile';

    const user = $derived(page.props.auth.user);
    const sidebar = useSidebar();
</script>

{#if user}
    <Sidebar.Menu>
        <Sidebar.MenuItem>
            <DropdownMenu.Root>
                <DropdownMenu.Trigger>
                    {#snippet child({ props })}
                        <Sidebar.MenuButton
                            size="lg"
                            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                            {...props}
                        >
                            <UserInfo {user} />
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/chevrons-up-down')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/selector')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/UnfoldMoreIcon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/CaretUpDownIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/expand-up-down-line')}
                                class="ms-auto size-4"
                            />
                        </Sidebar.MenuButton>
                    {/snippet}
                </DropdownMenu.Trigger>
                <DropdownMenu.Content
                    class="w-(--bits-dropdown-menu-anchor-width) min-w-56 rounded-[var(--radius)]"
                    side={sidebar.state === 'collapsed' ? 'right' : 'top'}
                    align="end"
                    sideOffset={4}
                >
                    <DropdownMenu.Label class="p-0 font-normal">
                        <div
                            class="flex items-center gap-2 px-1 py-1.5 text-start text-sm"
                        >
                            <UserInfo {user} showEmail={true} />
                        </div>
                    </DropdownMenu.Label>
                    <DropdownMenu.Separator />
                    <DropdownMenu.Group>
                        <DropdownMenu.Item>
                            {#snippet child({ props })}
                                <Link {...props} href={toUrl(edit())} prefetch>
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/settings')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/settings')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/Settings01Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/GearIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/settings-3-line')}
                                        class="size-4"
                                    />
                                    Settings
                                </Link>
                            {/snippet}
                        </DropdownMenu.Item>
                    </DropdownMenu.Group>
                    <DropdownMenu.Separator />
                    <DropdownMenu.Item class="w-full">
                        {#snippet child({ props })}
                            <button
                                {...props}
                                onclick={() => router.post(logout().url)}
                            >
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/log-out')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/logout')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/Logout01Icon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/SignOutIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/logout-box-r-line')}
                                    class="size-4"
                                />
                                Log out
                            </button>
                        {/snippet}
                    </DropdownMenu.Item>
                </DropdownMenu.Content>
            </DropdownMenu.Root>
        </Sidebar.MenuItem>
    </Sidebar.Menu>
{/if}
