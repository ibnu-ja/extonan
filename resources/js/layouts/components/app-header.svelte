<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import AppLogo from '@/components/app-logo.svelte';
    import { Icon } from '@/components/icon';
    import {
        Avatar,
        AvatarFallback,
        AvatarImage,
    } from '@/components/ui/avatar';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import {
        NavigationMenuLink,
        NavigationMenuRoot,
        NavigationMenuItem,
        NavigationMenuList,
    } from '@/components/ui/navigation-menu';
    import UserMenuContent from '@/layouts/components/user-menu-content.svelte';
    import { getInitials } from '@/lib/initials';
    import { toUrl } from '@/lib/utils';
    import { dashboard } from '@/routes';

    const auth = $derived(page.props.auth);

    const mainNavItems = [
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
    ];
</script>

<div>
    <div class="border-b border-sidebar-border/80">
        <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
            <Link href={toUrl(dashboard())} class="flex items-center gap-x-2">
                <AppLogo />
            </Link>

            <!-- Desktop Menu -->
            <div class="hidden h-full lg:flex lg:flex-1">
                <NavigationMenuRoot class="ml-10 flex h-full items-stretch">
                    <NavigationMenuList
                        class="flex h-full items-stretch space-x-2"
                    >
                        {#each mainNavItems as item (toUrl(item.href))}
                            <NavigationMenuItem>
                                <NavigationMenuLink href={toUrl(item.href)}>
                                    {#snippet child({ props })}
                                        <Link
                                            href={toUrl(item.href)}
                                            {...props}
                                        >
                                            {#if item.icon}
                                                <Icon
                                                    lucide={item.icon.lucide}
                                                    tabler={item.icon.tabler}
                                                    hugeicons={item.icon
                                                        .hugeicons}
                                                    phosphor={item.icon
                                                        .phosphor}
                                                    remixicon={item.icon
                                                        .remixicon}
                                                    class="mr-2 size-4"
                                                />
                                            {/if}
                                            {item.title}
                                        </Link>
                                    {/snippet}
                                </NavigationMenuLink>
                            </NavigationMenuItem>
                        {/each}
                    </NavigationMenuList>
                </NavigationMenuRoot>
            </div>

            <div class="ml-auto flex items-center space-x-2">
                <DropdownMenu>
                    <DropdownMenuTrigger>
                        {#snippet child({ props })}
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative size-10 w-auto rounded-full p-1 focus-within:ring-2 focus-within:ring-primary"
                                {...props}
                            >
                                <Avatar
                                    class="size-8 overflow-hidden rounded-full"
                                >
                                    {#if auth.user?.avatar}
                                        <AvatarImage
                                            src={auth.user.avatar}
                                            alt={auth.user?.name}
                                        />
                                    {/if}
                                    <AvatarFallback
                                        class="rounded-full bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {getInitials(auth.user?.name ?? '')}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        {/snippet}
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        {#if auth.user}
                            <UserMenuContent user={auth.user} />
                        {/if}
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    </div>
</div>
