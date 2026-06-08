<script lang="ts">
    import ImageIcon from 'lucide-svelte/icons/image';
    import type { Snippet } from 'svelte';
    import { Button } from '@/components/ui/button';

    let {
        onclick,
        children,
        hasValue = false,
        disabled = false,
    }: {
        onclick?: (e: MouseEvent) => void;
        children?: Snippet;
        hasValue?: boolean;
        disabled?: boolean;
    } = $props();
</script>

<Button data-slot="media-manager-trigger" {onclick} {disabled}>
    {#if children}
        {@render children()}
    {:else}
        <ImageIcon class="mr-1 size-4" />
        {hasValue ? 'Edit' : 'Add'} Media
    {/if}
</Button>
