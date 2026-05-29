<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import BookOpen from 'lucide-svelte/icons/book-open';
    import Folder from 'lucide-svelte/icons/folder';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import Search from 'lucide-svelte/icons/search';
    import AppLogo from '@/components/app-logo.svelte';
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
    import {
        Tooltip,
        TooltipContent,
        TooltipProvider,
        TooltipTrigger,
    } from '@/components/ui/tooltip';
    import UserMenuContent from '@/layouts/components/user-menu-content.svelte';
    import { getInitials } from '@/lib/initials';
    import { toUrl } from '@/lib/utils';
    import { dashboard } from '@/routes';
    import type { NavItem } from '@/types';

    const auth = $derived(page.props.auth);

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    const rightNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/laravel/svelte-starter-kit',
            icon: Folder,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs/starter-kits#svelte',
            icon: BookOpen,
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
                        {#each mainNavItems as { icon: Icon, ...item } (toUrl(item.href))}
                            <NavigationMenuItem>
                                <NavigationMenuLink href={toUrl(item.href)}>
                                    {#snippet child({ props })}
                                        <Link
                                            href={toUrl(item.href)}
                                            {...props}
                                        >
                                            {#if Icon}
                                                <Icon class="mr-2 h-4 w-4" />
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
                <div class="relative flex items-center space-x-1">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="group h-9 w-9 cursor-pointer"
                    >
                        <Search
                            class="size-5 opacity-80 group-hover:opacity-100"
                        />
                    </Button>

                    <div class="hidden space-x-1 lg:flex">
                        {#each rightNavItems as { icon: Icon, ...item } (toUrl(item.href))}
                            <TooltipProvider delayDuration={0}>
                                <Tooltip>
                                    <TooltipTrigger>
                                        {#snippet child({ props })}
                                            <a
                                                href={toUrl(item.href)}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                {...props}
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground h-9 w-9 group cursor-pointer"
                                            >
                                                <span class="sr-only"
                                                    >{item.title}</span
                                                >
                                                <Icon
                                                    class="size-5 opacity-80 group-hover:opacity-100"
                                                />
                                            </a>
                                        {/snippet}
                                    </TooltipTrigger>
                                    <TooltipContent>
                                        <p>{item.title}</p>
                                    </TooltipContent>
                                </Tooltip>
                            </TooltipProvider>
                        {/each}
                    </div>
                </div>

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
                                        class="rounded-lg bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white"
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
