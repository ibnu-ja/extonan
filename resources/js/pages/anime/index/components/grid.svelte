<script lang="ts">
    import Card from './card.svelte';

    type Item = {
        id: number;
        title: Record<string, string | null>;
        slug: Record<string, string | null>;
        coverImage: {
            extraLarge: string;
            large: string;
            medium: string;
            color: string;
        };
        isPublished: boolean;
        permissions: { update: boolean; delete: boolean; publish: boolean };
    };

    let {
        items = [],
    }: {
        items: Item[];
    } = $props();
</script>

{#if items.length > 0}
    <div
        class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6"
    >
        {#each items as item (item.id)}
            <Card {item} />
        {/each}
    </div>
{:else}
    <div class="flex h-48 items-center justify-center rounded-xl bg-muted">
        <p class="text-muted-foreground text-sm">No anime found.</p>
    </div>
{/if}
