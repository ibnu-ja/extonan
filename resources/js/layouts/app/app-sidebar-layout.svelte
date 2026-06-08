<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import { scrollY } from 'svelte/reactivity/window';
    import Breadcrumbs from '@/components/breadcrumbs.svelte';
    import * as Sidebar from '@/components/ui/sidebar/index.js';
    import { Toaster } from '@/components/ui/sonner';
    import AppFooter from '@/layouts/components/app-footer.svelte';
    import AppSidebarHeader from '@/layouts/components/app-sidebar-header.svelte';
    import AppSidebar from '@/layouts/components/app-sidebar.svelte';
    import BottomNavBar from '@/layouts/components/bottom-nav-bar.svelte';
    import MobileTopBar from '@/layouts/components/mobile-top-bar.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        showHeading,
        children,
        threshold = 60,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        showHeading?: boolean;
        children?: Snippet;
        threshold?: number;
    } = $props();

    const isOpen = $derived(page.props.sidebarOpen);
    const title = $derived(breadcrumbs[breadcrumbs.length - 1]?.title ?? '');
    const { mdAndDown } = useDisplay();
    let scrolled = $derived((scrollY.current ?? 0) > threshold);
</script>

<Sidebar.Provider open={isOpen}>
    <AppSidebar />
    <Sidebar.Inset class="flex flex-col pb-16 md:pb-0 overflow-x-hidden">
        {#if mdAndDown.current}
            {#if showHeading !== false}<div class="h-16"></div>{/if}
        {:else}
            <AppSidebarHeader {breadcrumbs} />
        {/if}
        <div class="flex flex-1 flex-col">
            {#if title}
                {#if showHeading !== false && mdAndDown.current}
                    <div
                        class="px-4 pt-4 transition-opacity duration-200"
                        class:opacity-0={scrolled}
                        class:opacity-100={!scrolled}
                    >
                        <h1 class="text-3xl font-semibold font-heading">
                            {title}
                        </h1>
                        {#if breadcrumbs.length > 1}
                            <div class="mt-1">
                                <Breadcrumbs {breadcrumbs} />
                            </div>
                        {/if}
                    </div>
                {:else if showHeading !== false}
                    <div class="px-4 pt-6">
                        <h1 class="text-3xl font-semibold font-heading">
                            {title}
                        </h1>
                    </div>
                {/if}
            {/if}
            {@render children?.()}
        </div>
        <AppFooter />
    </Sidebar.Inset>
    {#if mdAndDown.current}
        <MobileTopBar {title} {threshold} />
        <BottomNavBar />
    {/if}
    <Toaster />
</Sidebar.Provider>
