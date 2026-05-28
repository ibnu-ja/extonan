<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import CircleUserRound from 'lucide-svelte/icons/circle-user-round';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import Breadcrumbs from '@/components/breadcrumbs.svelte';
    import {
        Avatar,
        AvatarFallback,
    } from '@/components/ui/avatar';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import { SidebarTrigger } from '@/components/ui/sidebar';
    import UserMenuContent from '@/components/user-menu-content.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login, register as registerRoute } from '@/routes';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
    }: {
        breadcrumbs?: BreadcrumbItem[];
    } = $props();

    const auth = $derived(page.props.auth);
    const canRegister = $derived(page.props.canRegister as boolean | undefined);
</script>

<header
    class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
>
    <div class="flex items-center gap-2">
        <SidebarTrigger class="-ml-1 hidden md:inline-flex" />
        {#if breadcrumbs && breadcrumbs.length > 0}
            <Breadcrumbs {breadcrumbs} />
        {/if}
    </div>

    <div class="ml-auto flex items-center gap-2 md:hidden">
        {#if auth.user}
            <DropdownMenu>
                <DropdownMenuTrigger>
                    {#snippet child({ props })}
                        <Button
                            variant="ghost"
                            size="icon"
                            class="relative size-9 rounded-full p-0"
                            {...props}
                        >
                            <Avatar class="size-8">
                                <AvatarFallback class="rounded-full text-xs font-medium">
                                    {auth.user.name?.charAt(0)?.toUpperCase() ?? '?'}
                                </AvatarFallback>
                            </Avatar>
                        </Button>
                    {/snippet}
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-56">
                    <UserMenuContent user={auth.user}>
                        <DropdownMenuItem>
                            {#snippet child({ props })}
                                <Link {...props} href={toUrl(dashboard())}>
                                    <LayoutGrid class="mr-2 h-4 w-4" />
                                    Dashboard
                                </Link>
                            {/snippet}
                        </DropdownMenuItem>
                    </UserMenuContent>
                </DropdownMenuContent>
            </DropdownMenu>
        {:else if canRegister}
            <DropdownMenu>
                <DropdownMenuTrigger>
                    {#snippet child({ props })}
                        <Button
                            variant="ghost"
                            size="icon"
                            class="relative size-9 rounded-full p-0"
                            {...props}
                        >
                            <CircleUserRound class="size-5 text-muted-foreground" />
                        </Button>
                    {/snippet}
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-40">
                    <DropdownMenuItem>
                        {#snippet child({ props })}
                            <Link {...props} href={toUrl(login())}>
                                Log in
                            </Link>
                        {/snippet}
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                        {#snippet child({ props })}
                            <Link {...props} href={toUrl(registerRoute())}>
                                Register
                            </Link>
                        {/snippet}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        {:else}
            <Link href={toUrl(login())} class="inline-flex size-9 items-center justify-center rounded-full hover:bg-accent">
                <CircleUserRound class="size-5 text-muted-foreground" />
            </Link>
        {/if}
    </div>
</header>
