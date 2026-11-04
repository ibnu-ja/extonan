<script lang="ts">
    import { cn } from '@/lib/utils';
    import type { Snippet } from 'svelte';
    import { Button } from '@/components/ui/button';
    import type { ButtonSize, ButtonVariant } from '@/components/ui/button';

    let {
        class: className,
        child,
        children,
        size = 'lg' as ButtonSize,
        variant = 'default' as ButtonVariant,
        'data-slot': dataSlot,
        ...restProps
    }: {
        child?: Snippet<[{ props: Record<string, unknown> }]>;
        children?: Snippet;
        size?: ButtonSize;
        variant?: ButtonVariant;
        class?: string;
        'data-slot'?: string;
    } = $props();
</script>

<Button
    data-slot={dataSlot}
    class={cn('fixed bottom-22 right-6 z-40 hover:bg-primary hover:brightness-110 md:bottom-10 md:right-10', className)}
    {child}
    {variant}
    {size}
    {...restProps}
>
    {@render children?.()}
</Button>
