<script lang="ts">
    import { AspectRatio } from '@/components/ui/aspect-ratio';
    import { Badge } from '@/components/ui/badge';
    import * as Card from '@/components/ui/card';
    import { t, formatDate } from '@/lib/locale.svelte';
    import { show as postShow } from '@/routes/post';

    let {
        episode,
        animeId,
    }: {
        episode: App.Data.Anime.EpisodeShowData;
        animeId: number;
    } = $props();
</script>

<a href={postShow.url({ anime: animeId, post: episode.id })} class="group">
    <Card.Root
        class="overflow-hidden py-0 gap-0 rounded-none md:rounded-xl transition-colors hover:bg-muted"
    >
        {#if episode.thumbnail}
            <AspectRatio ratio={16 / 9}>
                <img
                    src={episode.thumbnail.medium}
                    alt={t(episode.title)}
                    loading="lazy"
                    class="h-full w-full object-cover transition-transform group-hover:scale-105"
                />
            </AspectRatio>
        {:else}
            <AspectRatio ratio={16 / 9}>
                <div class="bg-muted flex h-full items-center justify-center">
                    <span class="text-muted-foreground text-lg font-medium">
                        {episode.epNo ? `EP ${episode.epNo}` : '#'}
                    </span>
                </div>
            </AspectRatio>
        {/if}
        <Card.Content class="p-3">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">
                        {#if episode.epNo}
                            <span class="text-muted-foreground"
                                >EP {episode.epNo} -
                            </span>
                        {/if}
                        {t(episode.title)}
                    </p>
                    {#if episode.publishedAt}
                        <p class="text-muted-foreground mt-0.5 text-xs">
                            {formatDate(episode.publishedAt)}
                        </p>
                    {/if}
                </div>
                {#if !episode.isPublished}
                    <Badge variant="destructive" class="shrink-0">Draft</Badge>
                {/if}
            </div>
        </Card.Content>
    </Card.Root>
</a>
