<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { Badge } from '@/components/ui/badge';
    import * as Item from '@/components/ui/item';
    import { show as postShow } from '@/routes/post';

    let {
        title,
        epNo,
        thumbnail,
        animeTitle,
        author,
        publishedAt,
        isPublished,
        id,
        animeId,
    }: {
        title: Record<string, string | null>;
        epNo: string | null;
        thumbnail: {
            extraLarge: string;
            large: string;
            medium: string;
            color: string;
        } | null;
        animeTitle: Record<string, string | null>;
        author: {
            id: number;
            name: string | null;
            avatar: string | null;
        } | null;
        publishedAt: string | null;
        isPublished: boolean;
        id: number;
        animeId: number;
    } = $props();

    const displayTitle = $derived(
        title.en || title.romaji || title.native || '',
    );
    const animeDisplayTitle = $derived(
        animeTitle.en || animeTitle.romaji || animeTitle.native || '',
    );
    const subtitle = $derived(
        publishedAt
            ? `${new Date(publishedAt).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}`
            : '',
    );
    const episodeLabel = $derived(
        epNo ? `EP ${epNo} - ${displayTitle}` : displayTitle,
    );
</script>

<Item.Root variant="outline" class="rounded-none md:rounded-lg">
    {#snippet child({ props })}
        <Link href={postShow.url({ anime: animeId, post: id })} {...props}>
            {#if thumbnail}
                <Item.Media variant="image">
                    <img
                        src={thumbnail.medium}
                        alt={displayTitle}
                        loading="lazy"
                    />
                </Item.Media>
            {/if}
            <Item.Content>
                <Item.Description>{animeDisplayTitle}</Item.Description>
                <Item.Title>{episodeLabel}</Item.Title>
                <Item.Description>
                    {subtitle}{#if author}
                        &bull; {author.name}{/if}
                </Item.Description>
            </Item.Content>
            <Item.Actions>
                {#if !isPublished}
                    <Badge variant="destructive">Draft</Badge>
                {/if}
            </Item.Actions>
        </Link>
    {/snippet}
</Item.Root>
