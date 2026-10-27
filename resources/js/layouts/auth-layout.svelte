<script lang="ts">
    import type { Snippet } from 'svelte';
    import AuthLayout from '@/layouts/auth/auth-simple-layout.svelte';
    import { DesignSystemProvider } from '@/lib/design-system';

    let {
        title = '',
        description = '',
        children,
    }: {
        title?: string;
        description?: string;
        children?: Snippet;
    } = $props();
</script>

<DesignSystemProvider>
    <AuthLayout {title} {description}>
        {@render children?.()}
    </AuthLayout>
</DesignSystemProvider>
