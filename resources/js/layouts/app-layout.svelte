<script lang="ts">
    import type { Snippet } from 'svelte';
    import AppLayout from '@/layouts/app/app-sidebar-layout.svelte';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        showHeading,
        title,
        children,
        threshold,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
        showHeading?: boolean;
        title?: string;
        threshold?: number;
    } = $props();
</script>

<AppLayout {showHeading} {title} {breadcrumbs} {threshold}>
    {@render children?.()}
</AppLayout>
