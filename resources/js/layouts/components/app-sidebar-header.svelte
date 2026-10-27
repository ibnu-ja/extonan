<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import Breadcrumbs from '@/components/breadcrumbs.svelte';
    import { Icon } from '@/components/icon';
    import { Avatar, AvatarFallback } from '@/components/ui/avatar';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuSeparator,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import { SidebarTrigger } from '@/components/ui/sidebar';
    import UserMenuContent from '@/layouts/components/user-menu-content.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login, register as registerRoute } from '@/routes';
    import { edit as editAppearance } from '@/routes/appearance';
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
        {#if breadcrumbs.length > 1}
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
                                <AvatarFallback
                                    class="rounded-full text-xs font-medium"
                                >
                                    {auth.user.name?.charAt(0)?.toUpperCase() ??
                                        '?'}
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
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/layout-grid')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/layout-grid')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/DashboardSquare01Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/SquaresFourIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/dashboard-line')}
                                        class="mr-2 size-4"
                                    />
                                    Dashboard
                                </Link>
                            {/snippet}
                        </DropdownMenuItem>
                    </UserMenuContent>
                </DropdownMenuContent>
            </DropdownMenu>
        {:else}
            <DropdownMenu>
                <DropdownMenuTrigger>
                    {#snippet child({ props })}
                        <Button
                            variant="ghost"
                            size="icon"
                            class="relative size-9 rounded-full p-0"
                            {...props}
                        >
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/circle-user-round')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/user-circle')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/UserCircleIcon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/UserCircleIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/user-shared-line')}
                                class="size-5 text-muted-foreground"
                            />
                        </Button>
                    {/snippet}
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-44">
                    <DropdownMenuItem>
                        {#snippet child({ props })}
                            <Link {...props} href={toUrl(editAppearance())}>
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/palette')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/palette')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/PaintBoardIcon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/PaletteIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/palette-line')}
                                    class="mr-2 size-4"
                                />
                                Appearance
                            </Link>
                        {/snippet}
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem>
                        {#snippet child({ props })}
                            <Link {...props} href={toUrl(login())}>
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/log-in')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/login')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/Login01Icon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/SignInIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/login-box-line')}
                                    class="mr-2 size-4"
                                />
                                Log in
                            </Link>
                        {/snippet}
                    </DropdownMenuItem>
                    {#if canRegister}
                        <DropdownMenuItem>
                            {#snippet child({ props })}
                                <Link {...props} href={toUrl(registerRoute())}>
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/user-plus')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/user-plus')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/UserAdd01Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/UserPlusIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/user-add-line')}
                                        class="mr-2 size-4"
                                    />
                                    Register
                                </Link>
                            {/snippet}
                        </DropdownMenuItem>
                    {/if}
                </DropdownMenuContent>
            </DropdownMenu>
        {/if}
    </div>
</header>
