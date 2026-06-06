<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { Badge } from '@/components/ui/badge';
    import * as Item from '@/components/ui/item';
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

<Item.Root class="rounded-none md:rounded-lg">
    {#snippet child({ props })}
        <Link href={postShow.url({ anime: animeId, post: episode.id })} {...props}>
            <Item.Media variant="image">
                {#if episode.thumbnail}
                    <img
                        src={episode.thumbnail.medium}
                        alt=""
                        class="size-10 rounded object-cover"
                    />
                {/if}
            </Item.Media>
            <Item.Content>
                <Item.Title>
                    {#if episode.epNo}{episode.epNo} -
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
