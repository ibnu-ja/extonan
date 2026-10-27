<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
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
    import { edit as editAppearance } from '@/routes/appearance';
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
        {
            title: 'Home',
            href: home(),
            icon: {
                lucide: () => import('lucide-svelte/icons/house'),
                tabler: () => import('@tabler/icons-svelte/icons/home'),
                hugeicons: () =>
                    import('@hugeicons/core-free-icons/Home01Icon'),
                phosphor: () => import('phosphor-svelte/lib/HouseIcon'),
                remixicon: () => import('remixicon-svelte/icons/home-line'),
            },
        },
        {
            title: 'Anime',
            href: anime(),
            icon: {
                lucide: () => import('lucide-svelte/icons/film'),
                tabler: () => import('@tabler/icons-svelte/icons/movie'),
                hugeicons: () =>
                    import('@hugeicons/core-free-icons/Film01Icon'),
                phosphor: () => import('phosphor-svelte/lib/FilmSlateIcon'),
                remixicon: () => import('remixicon-svelte/icons/film-line'),
            },
        },
        {
            title: 'Album',
            href: album(),
            icon: {
                lucide: () => import('lucide-svelte/icons/disc-3'),
                tabler: () => import('@tabler/icons-svelte/icons/disc'),
                hugeicons: () =>
                    import('@hugeicons/core-free-icons/DiscTwoIcon'),
                phosphor: () => import('phosphor-svelte/lib/DiscIcon'),
                remixicon: () => import('remixicon-svelte/icons/disc-line'),
            },
        },
        {
            title: 'MV',
            href: mv(),
            icon: {
                lucide: () => import('lucide-svelte/icons/clapperboard'),
                tabler: () => import('@tabler/icons-svelte/icons/movie'),
                hugeicons: () =>
                    import('@hugeicons/core-free-icons/ClapperboardIcon'),
                phosphor: () => import('phosphor-svelte/lib/FilmReelIcon'),
                remixicon: () => import('remixicon-svelte/icons/movie-line'),
            },
        },
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: {
                lucide: () => import('lucide-svelte/icons/layout-grid'),
                tabler: () => import('@tabler/icons-svelte/icons/layout-grid'),
                hugeicons: () =>
                    import('@hugeicons/core-free-icons/DashboardSquare01Icon'),
                phosphor: () => import('phosphor-svelte/lib/SquaresFourIcon'),
                remixicon: () =>
                    import('remixicon-svelte/icons/dashboard-line'),
            },
        },
    ]);

    const footerNavItems = $derived(<NavItem[]>[
        ...(!isLoggedIn
            ? [
                  {
                      title: 'Appearance',
                      href: editAppearance(),
                      icon: {
                          lucide: () => import('lucide-svelte/icons/palette'),
                          tabler: () =>
                              import('@tabler/icons-svelte/icons/palette'),
                          hugeicons: () =>
                              import('@hugeicons/core-free-icons/PaintBoardIcon'),
                          phosphor: () =>
                              import('phosphor-svelte/lib/PaletteIcon'),
                          remixicon: () =>
                              import('remixicon-svelte/icons/palette-line'),
                      },
                  },
                  {
                      title: 'Log in',
                      href: login(),
                      icon: {
                          lucide: () => import('lucide-svelte/icons/log-in'),
                          tabler: () =>
                              import('@tabler/icons-svelte/icons/login'),
                          hugeicons: () =>
                              import('@hugeicons/core-free-icons/Login01Icon'),
                          phosphor: () =>
                              import('phosphor-svelte/lib/SignInIcon'),
                          remixicon: () =>
                              import('remixicon-svelte/icons/login-box-line'),
                      },
                  },
                  ...(canRegister
                      ? [
                            {
                                title: 'Register',
                                href: registerRoute(),
                                icon: {
                                    lucide: () =>
                                        import('lucide-svelte/icons/user-plus'),
                                    tabler: () =>
                                        import('@tabler/icons-svelte/icons/user-plus'),
                                    hugeicons: () =>
                                        import('@hugeicons/core-free-icons/UserAdd01Icon'),
                                    phosphor: () =>
                                        import('phosphor-svelte/lib/UserPlusIcon'),
                                    remixicon: () =>
                                        import('remixicon-svelte/icons/user-add-line'),
                                },
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
    class="hidden md:flex cn-menu-target"
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
