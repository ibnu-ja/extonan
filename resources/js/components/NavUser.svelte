<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { page } from '@inertiajs/svelte';
    import ChevronsUpDown from 'lucide-svelte/icons/chevrons-up-down';
    import LogOut from 'lucide-svelte/icons/log-out';
    import Settings from 'lucide-svelte/icons/settings';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import {
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
        useSidebar,
    } from '@/components/ui/sidebar';
    import UserInfo from '@/components/UserInfo.svelte';
    import { toUrl } from '@/lib/utils';
    import { logout } from '@/routes';
    import { edit } from '@/routes/profile';

    const user = $derived(page.props.auth.user);
    const { isMobile, state: sidebarState } = useSidebar();

    function handleLogout() {
        router.post(logout());
    }
</script>

{#if user}
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    {#snippet children(props)}
                        <SidebarMenuButton
                            size="lg"
                            class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground cursor-pointer"
                            data-test="sidebar-menu-button"
                            onclick={props.onclick}
                            aria-expanded={props['aria-expanded']}
                            data-state={props['data-state']}
                        >
                            <UserInfo {user} />
                            <ChevronsUpDown class="ml-auto size-4" />
                        </SidebarMenuButton>
                    {/snippet}
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-full min-w-0 rounded-lg animate-in fade-in slide-in-from-bottom-2 duration-200"
                    side={$sidebarState === 'collapsed' && !$isMobile ? 'left' : 'top'}
                    align="end"
                    sideOffset={4}
                >
                    <DropdownMenuItem asChild>
                        {#snippet children(props)}
                            <Link
                                class={props.class}
                                href={toUrl(edit())}
                                prefetch
                                onclick={props.onClick}
                            >
                                <Settings class="mr-2 h-4 w-4" />
                                Settings
                            </Link>
                        {/snippet}
                    </DropdownMenuItem>
                    <DropdownMenuItem asChild>
                        {#snippet children(props)}
                            <button
                                class={props.class}
                                onclick={() => { props.onClick?.(); handleLogout(); }}
                                data-test="logout-button"
                            >
                                <LogOut class="mr-2 h-4 w-4" />
                                Log out
                            </button>
                        {/snippet}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
{/if}
