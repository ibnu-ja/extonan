<script lang="ts">
    import type { Snippet } from 'svelte';
    import { Button } from '@/components/ui/button';

    let {
        child,
        children,
    }: {
        child?: Snippet<[{ props: Record<string, unknown> }]>;
        children?: Snippet;
    } = $props();
</script>

<Button
    class="fixed bottom-22 right-6 z-40 h-11 gap-3 rounded-full px-5 text-base shadow-lg [a]:bg-secondary [a]:hover:bg-sevondary hover:brightness-80 transition-all duration-200 md:bottom-10 md:right-10"
    size="lg"
    {child}
>
    {@render children?.()}
</Button>
