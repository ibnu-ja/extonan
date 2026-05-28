<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import ChevronRightIcon from '@lucide/svelte/icons/chevron-right';
    import * as Collapsible from '@/components/ui/collapsible/index.js';
    import * as Sidebar from '@/components/ui/sidebar/index.js';
    import { currentUrlState } from '@/lib/current-url.svelte';
    import { toUrl } from '@/lib/utils';
    import type { NavItem } from '@/types';

    let {
        items = [],
    }: {
        items: NavItem[];
    } = $props();

    const url = currentUrlState();
</script>

<Sidebar.Group>
    <Sidebar.GroupLabel>Platform</Sidebar.GroupLabel>
    <Sidebar.Menu>
        {#each items as { title, href, icon: Icon, isActive, items: subItems } (href)}
            <Sidebar.MenuItem>
                {#if subItems?.length}
                    <Collapsible.Root open={isActive}>
                        {#snippet child({ props })}
                            <Sidebar.MenuItem {...props}>
                                <Sidebar.MenuButton tooltipContent={title}>
                                    {#snippet child({ props })}
                                        <Link
                                            {...props}
                                            href={toUrl(href)}
                                            class={props.class}
                                        >
                                            {#if Icon}<Icon />{/if}
                                            <span>{title}</span>
                                        </Link>
                                    {/snippet}
                                </Sidebar.MenuButton>
                                <Collapsible.Trigger>
                                    {#snippet child({ props })}
                                        <Sidebar.MenuAction
                                            {...props}
                                            class="data-[state=open]:rotate-90"
                                        >
                                            <ChevronRightIcon />
                                            <span class="sr-only">Toggle</span>
                                        </Sidebar.MenuAction>
                                    {/snippet}
                                </Collapsible.Trigger>
                                <Collapsible.Content>
                                    <Sidebar.MenuSub>
                                        {#each subItems as { title: subTitle, href: subHref } (subHref)}
                                            <Sidebar.MenuSubItem>
                                                <Sidebar.MenuSubButton
                                                    href={toUrl(subHref)}
                                                >
                                                    <span>{subTitle}</span>
                                                </Sidebar.MenuSubButton>
                                            </Sidebar.MenuSubItem>
                                        {/each}
                                    </Sidebar.MenuSub>
                                </Collapsible.Content>
                            </Sidebar.MenuItem>
                        {/snippet}
                    </Collapsible.Root>
                {:else}
                    <Sidebar.MenuButton
                        isActive={url.isCurrentUrl(href, url.currentUrl)}
                        tooltipContent={title}
                        class="transition-all duration-200"
                    >
                        {#snippet child({ props })}
                            <Link
                                {...props}
                                href={toUrl(href)}
                                class={props.class}
                            >
                                {#if Icon}<Icon class="size-4 shrink-0" />{/if}
                                <span>{title}</span>
                            </Link>
                        {/snippet}
                    </Sidebar.MenuButton>
                {/if}
            </Sidebar.MenuItem>
        {/each}
    </Sidebar.Menu>
</Sidebar.Group>
