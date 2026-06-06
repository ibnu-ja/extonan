<script lang="ts">
    import type { Snippet } from 'svelte';
    import { SidebarInset } from '@/components/ui/sidebar';
    import AppFooter from '@/layouts/components/app-footer.svelte';
    import { cn } from '@/lib/utils';
    import type { AppVariant } from '@/types';

    let {
        variant = 'sidebar',
        class: className = '',
        children,
    }: {
        variant?: AppVariant;
        class?: string;
        children?: Snippet;
    } = $props();
</script>

{#if variant === 'sidebar'}
    <SidebarInset class={cn('flex flex-col pb-16 md:pb-0', className)}>
        {@render children?.()}
        <AppFooter />
    </SidebarInset>
{:else}
    <main
        class={cn(
            'mx-auto flex h-full w-full flex-1 flex-col gap-4 rounded-xl',
            className,
        )}
    >
        {@render children?.()}
        <AppFooter />
    </main>
{/if}
