<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { Badge } from '@/components/ui/badge';
    import * as Item from '@/components/ui/item';
    import { t, formatDate } from '@/lib/locale.svelte';
    import { show as postShow } from '@/routes/post';

    let {
        episode,
        animeId,
        animeTitle,
    }: {
        episode: App.Data.Anime.EpisodeShowData | App.Data.EpisodeSummaryData;
        animeId: number;
        animeTitle?: Record<string, string | null>;
    } = $props();
</script>

<Item.Root class="rounded-none md:rounded-lg">
    {#snippet child({ props })}
        <Link
            href={postShow.url({ anime: animeId, post: episode.id })}
            {...props}
        >
            <Item.Media variant="image">
                {#if episode.thumbnail}
                    <img
                        src={episode.thumbnail.medium}
                        alt=""
                        class="size-10 rounded object-cover"
                    />
                {:else}
                    <div class="size-10 rounded bg-muted"></div>
                {/if}
            </Item.Media>
            <Item.Content>
                {#if animeTitle}
                    <Item.Description class="text-xs font-medium">
                        {t(animeTitle)}
                    </Item.Description>
                {/if}
                <Item.Title>
                    {#if episode.epNo}
                        <span class="text-muted-foreground"
                            >EP {episode.epNo}:
                        </span>
                    {/if}{t(episode.title)}
                </Item.Title>
                <Item.Description>
                    {formatDate(episode.publishedAt)}
                    {#if episode.author}
                        &bull; {episode.author.name}{/if}
                </Item.Description>
            </Item.Content>
            <Item.Actions>
                {#if !episode.isPublished}
                    <Badge variant="destructive">Draft</Badge>
                {/if}
            </Item.Actions>
        </Link>
    {/snippet}
</Item.Root>
