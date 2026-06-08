<script lang="ts">
    import Check from 'lucide-svelte/icons/check';
    import { index as mediaIndex } from '@/actions/App/Http/Controllers/MediaController';
    import { AspectRatio } from '@/components/ui/aspect-ratio';
    import { Button } from '@/components/ui/button';
    import { Spinner } from '@/components/ui/spinner';

    type MediaResponse = App.Data.PaginatedCollection<App.Data.MediaData>;

    let {
        media,
        value = $bindable(null),
        multiple = false,
        onselected,
    }: {
        media: MediaResponse | null;
        value?: App.Data.MediaData | App.Data.MediaData[] | null;
        multiple?: boolean;
        onselected?: (item: App.Data.MediaData | null) => void;
    } = $props();

    let currentMedia = $state<MediaResponse | null>(null);
    let loaded = $state<App.Data.MediaData[]>([]);
    let loading = $state(false);

    async function loadMore() {
        if (
            !currentMedia ||
            currentMedia.currentPage >= currentMedia.lastPage
        ) {
            return;
        }

        const nextPage = currentMedia.currentPage + 1;
        const response = await fetch(
            mediaIndex.url({ query: { page: String(nextPage) } }),
        );
        const data: MediaResponse = await response.json();
        currentMedia = data;
        loaded = [...loaded, ...data.data];
        loading = false;
    }

    $effect(() => {
        if (media) {
            currentMedia = media;
            loaded = media.data;
        }
    });

    function onMediaSelect(item: App.Data.MediaData) {
        if (!multiple) {
            handleSingleSelect(item);
        } else {
            handleMultipleSelect(item);
        }
    }

    function handleSingleSelect(item: App.Data.MediaData) {
        if (
            !value ||
            (Array.isArray(value)
                ? value[0]?.id !== item.id
                : value.id !== item.id)
        ) {
            value = item;
            onselected?.(item);

            return;
        }

        value = null;
        onselected?.(null);
    }

    function handleMultipleSelect(item: App.Data.MediaData) {
        const current = Array.isArray(value) ? value : [];

        if (current.some((i) => i.id === item.id)) {
            value = current.filter((i) => i.id !== item.id);
            onselected?.(null);
        } else {
            value = [...current, item];
            onselected?.(item);
        }
    }

    function isSelected(item: App.Data.MediaData): boolean {
        if (Array.isArray(value)) {
            return value.some((i) => i.id === item.id);
        }

        return value?.id === item.id;
    }

    function selectedIndex(item: App.Data.MediaData): number {
        if (Array.isArray(value)) {
            const idx = value.findIndex((i) => i.id === item.id);

            return idx >= 0 ? idx + 1 : 0;
        }

        return 0;
    }
</script>

<div class="space-y-4">
    <h3 class="font-heading text-lg font-semibold">Recent Media</h3>

    <div class="grid grid-cols-3 gap-3 md:grid-cols-4 lg:grid-cols-5">
        {#each loaded as item (item.id)}
            <button
                type="button"
                class="group relative overflow-hidden rounded-lg border bg-muted cursor-pointer ring-offset-background transition-all hover:ring-2 hover:ring-primary data-[selected=true]:ring-2 data-[selected=true]:ring-primary"
                data-selected={isSelected(item)}
                onclick={() => onMediaSelect(item)}
            >
                <AspectRatio ratio={1}>
                    {#if item.mediumUrl}
                        <img
                            src={item.mediumUrl}
                            alt={item.filename}
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                    {:else}
                        <div class="flex h-full items-center justify-center">
                            <span
                                class="text-muted-foreground text-xs p-1 text-center break-all"
                            >
                                {item.filename}.{item.extension}
                            </span>
                        </div>
                    {/if}
                </AspectRatio>
                {#if isSelected(item)}
                    <div class="absolute top-1 right-1">
                        {#if multiple}
                            <span
                                class="flex size-5 items-center justify-center rounded-full bg-primary text-[10px] font-medium text-primary-foreground"
                            >
                                {selectedIndex(item)}
                            </span>
                        {:else}
                            <span
                                class="flex size-5 items-center justify-center rounded-full bg-primary text-primary-foreground"
                            >
                                <Check class="size-3" />
                            </span>
                        {/if}
                    </div>
                {/if}
            </button>
        {/each}
    </div>

    {#if currentMedia && currentMedia.currentPage < currentMedia.lastPage}
        <div class="flex justify-center">
            <Button
                variant="ghost"
                size="sm"
                onclick={loadMore}
                disabled={loading}
            >
                {#if loading}
                    <Spinner class="mr-2 size-4" />
                    Loading...
                {:else}
                    Load More
                {/if}
            </Button>
        </div>
    {/if}
</div>
