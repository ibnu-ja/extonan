<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppLayout from '@/layouts/app/app-sidebar-layout.svelte';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        showHeading,
        children,
        threshold,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
        showHeading?: boolean;
        threshold?: number;
    } = $props();
</script>

<AppLayout {showHeading} {breadcrumbs} {threshold}>
    {@render children?.()}
</AppLayout>
