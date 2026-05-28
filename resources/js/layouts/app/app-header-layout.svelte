<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppContent from '@/components/app-content.svelte';
    import AppHeader from '@/components/app-header.svelte';
    import AppShell from '@/components/app-shell.svelte';
    import Breadcrumbs from '@/components/breadcrumbs.svelte';
    import { Toaster } from '@/components/ui/sonner';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        children,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
    } = $props();

    const title = $derived(breadcrumbs[breadcrumbs.length - 1]?.title ?? '');
</script>

<AppShell variant="header">
    <AppHeader />
    {#if title}
        <div class="px-4 pt-6">
            {#if breadcrumbs.length > 1}
                <Breadcrumbs {breadcrumbs} />
            {/if}
            <h1 class="text-3xl font-semibold font-heading">{title}</h1>
        </div>
    {/if}
    <AppContent variant="header">
        {@render children?.()}
    </AppContent>
    <Toaster />
</AppShell>
