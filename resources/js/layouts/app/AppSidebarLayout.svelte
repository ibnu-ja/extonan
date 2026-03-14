<script lang="ts">
    import AppContent from '@/components/layout/AppContent.svelte';
    import AppShell from '@/components/layout/AppShell.svelte';
    import AppSidebar from '@/components/layout/AppSidebar.svelte';
    import AppSidebarHeader from '@/components/layout/AppSidebarHeader.svelte';
    import type { BreadcrumbItemType } from '@/types';
    import type { Snippet } from 'svelte';

    interface Props {
        breadcrumbs?: BreadcrumbItemType[];
        children?: Snippet;
    }

    let { breadcrumbs = [], children }: Props = $props();
</script>

<AppShell variant="sidebar">
    <AppSidebar />
    <AppContent variant="sidebar" class="overflow-x-hidden">
        <AppSidebarHeader {breadcrumbs} />
        {@render children?.()}
    </AppContent>
</AppShell>
