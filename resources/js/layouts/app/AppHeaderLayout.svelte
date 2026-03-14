<script lang="ts">
    import AppContent from '@/components/layout/AppContent.svelte';
    import AppHeader from '@/components/layout/AppHeader.svelte';
    import AppShell from '@/components/layout/AppShell.svelte';
    import type { BreadcrumbItemType } from '@/types';
    import type { Snippet } from 'svelte';

    interface Props {
        breadcrumbs?: BreadcrumbItemType[];
        children?: Snippet;
    }

    let { breadcrumbs = [], children }: Props = $props();
</script>

<AppShell variant="header">
    <AppHeader {breadcrumbs} />
    <AppContent variant="header">
        {@render children?.()}
    </AppContent>
</AppShell>
