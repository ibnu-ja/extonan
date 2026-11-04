<script module lang="ts">
    import { index as mvIndex } from '@/routes/mv';

    export const layout = {
        breadcrumbs: [{ title: 'Music Video', href: mvIndex() }],
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { Icon } from '@/components/icon';
    import Pagination from '@/components/pagination.svelte';
    import PostCard from '@/components/post-card.svelte';
    import { Fab } from '@/components/ui/fab';
    import { create as mvCreate } from '@/routes/mv';

    let {
        mv,
        canCreate = false,
    }: App.Data.Music.MusicIndexResponse = $props();
</script>

<svelte:head>
    <title>Music Videos</title>
</svelte:head>

<div class="space-y-6 p-4">
    {#if mv.data && mv.data.length > 0}
        <div
            class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6"
        >
            {#each mv.data as item (item.id)}
                <PostCard post={item as any} aspect="video" />
            {/each}
        </div>

        {#if mv.links && mv.links.length > 3}
            <Pagination data={mv as any} only={['mv']} />
        {/if}
    {:else}
        <div
            class="flex h-64 flex-col items-center justify-center rounded-[var(--radius)] border border-dashed border-border bg-muted/30 p-8 text-center"
        >
            <p class="text-lg font-medium text-muted-foreground">
                No music videos found.
            </p>
            <p class="text-sm text-muted-foreground/80 mt-1">
                New music videos will appear here once published.
            </p>
        </div>
    {/if}

    {#if canCreate}
        <Fab>
            {#snippet child({ props })}
                <Link href={mvCreate().url} prefetch="hover" {...props}>
                    <Icon
                        lucide={() => import('lucide-svelte/icons/plus')}
                        tabler={() => import('@tabler/icons-svelte/icons/plus')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/PlusSignIcon')}
                        phosphor={() => import('phosphor-svelte/lib/PlusIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/add-line')}
                        class="size-6"
                    />
                    Add
                </Link>
            {/snippet}
        </Fab>
    {/if}
</div>
