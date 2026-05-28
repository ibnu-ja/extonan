<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppContent from '@/components/app-content.svelte';
    import AppHeader from '@/components/app-header.svelte';
    import AppShell from '@/components/app-shell.svelte';
    import { Toaster } from '@/components/ui/sonner';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        children,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
    } = $props();
</script>

<AppShell variant="header">
    <AppHeader {breadcrumbs} />
    <AppContent variant="header">
        {@render children?.()}
    </AppContent>
    <Toaster />
</AppShell>
