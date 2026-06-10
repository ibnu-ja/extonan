<script lang="ts">
    import type { Snippet } from 'svelte';
    import { Button } from '@/components/ui/button';
    import type { ButtonSize, ButtonVariant } from '@/components/ui/button';

    let {
        child,
        children,
        size = 'lg' as ButtonSize,
        variant = 'default' as ButtonVariant,
    }: {
        child?: Snippet<[{ props: Record<string, unknown> }]>;
        children?: Snippet;
        size?: ButtonSize;
        variant?: ButtonVariant;
    } = $props();
</script>

<Button
    class="fixed bottom-22 right-6 z-40 hover:bg-primary hover:brightness-110 md:bottom-10 md:right-10"
    {child}
    {variant}
    {size}
>
    {@render children?.()}
</Button>
