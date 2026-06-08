<script lang="ts">
    import type { Snippet } from 'svelte';
    import type { HTMLAttributes } from 'svelte/elements';
    import { cn } from '@/lib/utils.js';
    import type { WithElementRef } from '@/lib/utils.js';

    let {
        ref = $bindable(null),
        class: className,
        value = $bindable(null),
        title = 'Add Media',
        header,
        trigger,
        onOpen,
        preview,
        empty,
        children,
        ...restProps
    }: WithElementRef<HTMLAttributes<HTMLDivElement>> & {
        value?: App.Data.MediaData | App.Data.MediaData[] | null;
        title?: string;
        header?: Snippet<[{ title: string; onOpen: () => void }]>;
        trigger?: Snippet<[{ hasValue: boolean; onclick: () => void }]>;
        onOpen?: () => void;
        preview?: Snippet<[{ value: App.Data.MediaData | App.Data.MediaData[] | null }]>;
        empty?: Snippet;
        children?: Snippet;
    } = $props();

    let hasValue = $derived(value !== null && value !== undefined);
</script>

<div
    bind:this={ref}
    data-slot="media-manager"
    class={cn('space-y-4', className)}
    {...restProps}
>
    {#if header}
        {@render header({ title, onOpen: onOpen ?? (() => {}) })}
    {:else}
        <div class="flex flex-row items-center justify-between">
            <h4 class="text-sm font-medium">
                {title}
            </h4>
            {#if trigger}
                {@render trigger({ hasValue, onclick: () => {} })}
            {/if}
        </div>
    {/if}

    {#if preview}
        {@render preview({ value })}
    {:else if value}
        {@render children?.()}
    {:else if empty}
        {@render empty()}
    {:else}
        <p class="text-sm text-muted-foreground">No media selected.</p>
    {/if}
</div>
