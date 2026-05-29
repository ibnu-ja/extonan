<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import Clapperboard from 'lucide-svelte/icons/clapperboard';
    import Disc3 from 'lucide-svelte/icons/disc-3';
    import Film from 'lucide-svelte/icons/film';
    import House from 'lucide-svelte/icons/house';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import LogIn from 'lucide-svelte/icons/log-in';
    import UserPlus from 'lucide-svelte/icons/user-plus';
    import type { ComponentProps } from 'svelte';
    import AppLogo from '@/components/app-logo.svelte';
    import * as Sidebar from '@/components/ui/sidebar/index.js';
    import NavFooter from '@/layouts/components/nav-footer.svelte';
    import NavMain from '@/layouts/components/nav-main.svelte';
    import NavUser from '@/layouts/components/nav-user.svelte';
    import { toUrl } from '@/lib/utils';
    import {
        dashboard,
        home,
        login,
        register as registerRoute,
    } from '@/routes';
    import { index as album } from '@/routes/album';
    import { index as anime } from '@/routes/anime';
    import { index as mv } from '@/routes/mv';
    import type { NavItem } from '@/types';

    let {
        ref = $bindable(null),
        ...restProps
    }: ComponentProps<typeof Sidebar.Root> = $props();

    const auth = $derived(page.props.auth);
    const isLoggedIn = $derived(!!auth.user);
    const canRegister = $derived(page.props.canRegister as boolean | undefined);

    const mainNavItems = $derived(<NavItem[]>[
        { title: 'Home', href: home(), icon: House },
        { title: 'Anime', href: anime(), icon: Film },
        { title: 'Album', href: album(), icon: Disc3 },
        { title: 'MV', href: mv(), icon: Clapperboard },
        { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    ]);

    const footerNavItems = $derived(<NavItem[]>[
        ...(!isLoggedIn
            ? [
                  { title: 'Log in', href: login(), icon: LogIn },
                  ...(canRegister
                      ? [
                            {
                                title: 'Register',
                                href: registerRoute(),
                                icon: UserPlus,
                            },
                        ]
                      : []),
              ]
            : []),
    ]);
</script>

<Sidebar.Root
    bind:ref
    collapsible="icon"
    variant="inset"
    class="hidden md:flex"
    {...restProps}
>
    <Sidebar.Header>
        <Sidebar.Menu>
            <Sidebar.MenuItem>
                <Sidebar.MenuButton
                    size="lg"
                    class="hover:bg-transparent hover:text-inherit active:bg-transparent active:text-inherit data-[state=open]:hover:bg-transparent data-[state=open]:hover:text-inherit"
                >
                    {#snippet child({ props })}
                        <Link
                            {...props}
                            href={toUrl(home())}
                            class={props.class}
                        >
                            <AppLogo />
                        </Link>
                    {/snippet}
                </Sidebar.MenuButton>
            </Sidebar.MenuItem>
        </Sidebar.Menu>
    </Sidebar.Header>
    <Sidebar.Content>
        <NavMain items={mainNavItems} />
    </Sidebar.Content>
    <Sidebar.Footer>
        <NavFooter items={footerNavItems} />
        <NavUser />
    </Sidebar.Footer>
</Sidebar.Root>
