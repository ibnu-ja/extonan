<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { page } from '@inertiajs/svelte';
    import Clapperboard from 'lucide-svelte/icons/clapperboard';
    import Disc3 from 'lucide-svelte/icons/disc-3';
    import Film from 'lucide-svelte/icons/film';
    import House from 'lucide-svelte/icons/house';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import LogIn from 'lucide-svelte/icons/log-in';
    import UserPlus from 'lucide-svelte/icons/user-plus';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import { dashboard, home, login, register as registerRoute } from '@/routes';
    import { index as album } from '@/routes/album';
    import { index as anime } from '@/routes/anime';
    import { index as mv } from '@/routes/mv';
    import type { NavItem } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const auth = $derived(page.props.auth);
    const isLoggedIn = $derived(!!auth.user);
    const canRegister = $derived(page.props.canRegister as boolean | undefined);

    const mainNavItems = $derived(<NavItem[]>[
        {
            title: 'Home',
            href: home(),
            icon: House,
        },
        {
            title: 'Anime',
            href: anime(),
            icon: Film,
        },
        {
            title: 'Album',
            href: album(),
            icon: Disc3,
        },
        {
            title: 'MV',
            href: mv(),
            icon: Clapperboard,
        },
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ]);

    const footerNavItems = $derived(<NavItem[]>[
        ...(!isLoggedIn ? [
            {
                title: 'Log in',
                href: login(),
                icon: LogIn,
            },
            ...(canRegister ? [{
                title: 'Register',
                href: registerRoute(),
                icon: UserPlus,
            }] : []),
        ] : []),
    ]);

</script>
<Sidebar collapsible="icon" variant="inset" class="hidden md:flex">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild class="hover:bg-transparent hover:text-inherit active:bg-transparent active:text-inherit data-[state=open]:hover:bg-transparent data-[state=open]:hover:text-inherit">
                    {#snippet children(props)}
                        <Link
                            {...props}
                            href={toUrl(home())}
                            class={props.class}
                        >
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter>
        <NavFooter items={footerNavItems} />
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
