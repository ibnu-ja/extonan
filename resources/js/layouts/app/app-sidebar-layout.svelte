<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import AppFooter from '@/components/app-footer.svelte';
    import AppSidebarHeader from '@/components/app-sidebar-header.svelte';
    import AppSidebar from '@/components/app-sidebar.svelte';
    import BottomNavBar from '@/components/bottom-nav-bar.svelte';
    import Breadcrumbs from '@/components/breadcrumbs.svelte';
    import MobileTopBar from '@/components/mobile-top-bar.svelte';
    import * as Sidebar from '@/components/ui/sidebar/index.js';
    import { Toaster } from '@/components/ui/sonner';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        children,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
    } = $props();

    const isOpen = $derived(page.props.sidebarOpen);
    const title = $derived(breadcrumbs[breadcrumbs.length - 1]?.title ?? '');

    let isMobile = $state(false);
    let scrolled = $state(false);

    $effect(() => {
        if (typeof window === 'undefined') {
            return;
        }

        const md = getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-md').trim();
        const mq = window.matchMedia(`(max-width: ${md})`);
        isMobile = mq.matches;
        const handler = (e: MediaQueryListEvent) => {
            isMobile = e.matches;
        };
        mq.addEventListener('change', handler);

        return () => mq.removeEventListener('change', handler);
    });

    $effect(() => {
        if (typeof window === 'undefined') {
            return;
        }

        const onScroll = () => {
            scrolled = window.scrollY > 60;
        };
        window.addEventListener('scroll', onScroll, { passive: true });

        return () => window.removeEventListener('scroll', onScroll);
    });
</script>

<Sidebar.Provider open={isOpen}>
    <AppSidebar />
    <Sidebar.Inset class="flex flex-col pb-16 md:pb-0 overflow-x-hidden">
        {#if isMobile}
            <div class="h-16"></div>
            <MobileTopBar {title} {scrolled} />
        {:else}
			<AppSidebarHeader {breadcrumbs} />
        {/if}
        <div class="flex flex-1 flex-col">
            {#if title}
                {#if isMobile}
                    <div
                        class="px-4 pt-4 transition-opacity duration-200"
                        class:opacity-0={scrolled}
                        class:opacity-100={!scrolled}
                    >
                        <h1 class="text-3xl font-semibold font-heading">{title}</h1>
                        {#if breadcrumbs.length > 1}
                            <div class="mt-1"><Breadcrumbs {breadcrumbs} /></div>
                        {/if}
                    </div>
                {:else}
                    <div class="px-4 pt-6">
                        {#if breadcrumbs.length > 1}
                            <Breadcrumbs {breadcrumbs} />
                        {/if}
                        <h1 class="text-3xl font-semibold font-heading">{title}</h1>
                    </div>
                {/if}
            {/if}
            {@render children?.()}
        </div>
        <AppFooter />
    </Sidebar.Inset>
    {#if isMobile}
        <BottomNavBar />
    {/if}
    <Toaster />
</Sidebar.Provider>
