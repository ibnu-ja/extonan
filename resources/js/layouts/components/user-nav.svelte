<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { Icon } from '@/components/icon';
    import { Avatar, AvatarFallback } from '@/components/ui/avatar';
    import { Button } from '@/components/ui/button';
    import {
        DropdownMenu,
        DropdownMenuContent,
        DropdownMenuItem,
        DropdownMenuTrigger,
    } from '@/components/ui/dropdown-menu';
    import UserMenuContent from '@/layouts/components/user-menu-content.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login, register as registerRoute } from '@/routes';

    const auth = $derived(page.props.auth);
    const canRegister = $derived(page.props.canRegister as boolean | undefined);
    const { mdAndDown } = useDisplay();
</script>

<div class="ml-auto flex items-center gap-2">
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
                    {#if mdAndDown.current}
                        <DropdownMenuItem>
                            {#snippet child({ props })}
                                <Link {...props} href={toUrl(dashboard())}>
                                    <Icon
                                        lucide="LayoutGridIcon"
                                        tabler="IconLayoutGrid"
                                        hugeicons="DashboardSquare01Icon"
                                        phosphor="SquaresFourIcon"
                                        remixicon="RiDashboardLine"
                                        class="mr-2 size-4"
                                    />
                                    Dashboard
                                </Link>
                            {/snippet}
                        </DropdownMenuItem>
                    {/if}
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
                        <Icon
                            lucide="CircleUserRoundIcon"
                            tabler="IconUserCircle"
                            hugeicons="UserCircleIcon"
                            phosphor="UserCircleIcon"
                            remixicon="RiUserSharedLine"
                            class="size-5 text-muted-foreground"
                        />
                    </Button>
                {/snippet}
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-40">
                <DropdownMenuItem>
                    {#snippet child({ props })}
                        <Link {...props} href={toUrl(login())}>Log in</Link>
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
        <Link
            href={toUrl(login())}
            class="inline-flex size-9 items-center justify-center rounded-full hover:bg-accent"
        >
            <Icon
                lucide="CircleUserRoundIcon"
                tabler="IconUserCircle"
                hugeicons="UserCircleIcon"
                phosphor="UserCircleIcon"
                remixicon="RiUserSharedLine"
                class="size-5 text-muted-foreground"
            />
        </Link>
    {/if}
</div>
