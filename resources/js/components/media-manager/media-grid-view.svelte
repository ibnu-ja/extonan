<script lang="ts">
    import { AspectRatio } from '@/components/ui/aspect-ratio';

    let {
        images,
    }: {
        images: App.Data.MediaData | App.Data.MediaData[];
    } = $props();

    let imageList = $derived(Array.isArray(images) ? images : [images]);
</script>

<div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
    {#each imageList as item (item.id)}
        <div class="group relative overflow-hidden rounded-lg border bg-muted">
            <AspectRatio ratio={1}>
                {#if item.mediumUrl}
                    <img
                        src={item.mediumUrl}
                        alt={item.filename}
                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                        loading="lazy"
                    />
                {:else}
                    <div class="flex h-full items-center justify-center">
                        <span class="text-muted-foreground text-xs">
                            {item.filename}.{item.extension}
                        </span>
                    </div>
                {/if}
            </AspectRatio>
        </div>
    {/each}
</div>
