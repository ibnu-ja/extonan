<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { page } from '@inertiajs/svelte';
    import BookOpen from 'lucide-svelte/icons/book-open';
    import Clapperboard from 'lucide-svelte/icons/clapperboard';
    import Disc3 from 'lucide-svelte/icons/disc-3';
    import Film from 'lucide-svelte/icons/film';
    import FolderGit2 from 'lucide-svelte/icons/folder-git-2';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
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
    import { dashboard } from '@/routes';
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

    const mainNavItems = $derived(<NavItem[]>[
        ...(isLoggedIn ? [{
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        }] : []),
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
    ]);

    const footerNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/laravel/svelte-starter-kit',
            icon: FolderGit2,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#svelte',
            icon: BookOpen,
        },
    ];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild>
                    {#snippet children(props)}
                        <Link
                            {...props}
                            href={toUrl(dashboard())}
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
