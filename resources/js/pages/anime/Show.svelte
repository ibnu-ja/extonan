<script lang="ts">
    import { Link, page, setLayoutProps } from '@inertiajs/svelte';
    import Calendar from '@lucide/svelte/icons/calendar';
    import Clapperboard from '@lucide/svelte/icons/clapperboard';
    import Film from '@lucide/svelte/icons/film';
    import Hash from '@lucide/svelte/icons/hash';
    import Info from '@lucide/svelte/icons/info';
    import Banner from '@/components/anime/banner.svelte';
    import Casts from '@/components/anime/casts.svelte';
    import { Badge } from '@/components/ui/badge';
    import * as Card from '@/components/ui/card';
    import * as Item from '@/components/ui/item';
    import * as Tabs from '@/components/ui/tabs';
    import { index as animeIndex } from '@/routes/anime';
    import { show as postShow } from '@/routes/post';

    let {
        anime,
        episodes,
        metadata,
    }: App.Data.Anime.AnimeShowResponse = $props();

    let currentLang = $state<'en' | 'id'>(
        (page.props.locale as 'en' | 'id') ?? 'en',
    );
    const displayTitle = $derived(
        anime.title[currentLang] || anime.title.native || 'Untitled',
    );

    $effect(() => {
        setLayoutProps({
            showHeading: false,
            threshold: 400,
            breadcrumbs: [
                { title: 'Anime', href: animeIndex() },
                { title: displayTitle, href: '' },
            ],
        });
    });

    const mainStudio = $derived(
        metadata?.studios?.edges?.find((e) => e.isMain)?.node?.name ?? null,
    );

    const episodeCount = $derived(metadata?.episodes ?? episodes.length);

    const season = $derived(
        metadata?.season && metadata?.seasonYear
            ? `${metadata.season.charAt(0) + metadata.season.slice(1).toLowerCase()} ${metadata.seasonYear}`
            : null,
    );

    const startDate = $derived(
        metadata?.startDate?.year
            ? new Date(
                  metadata.startDate.year,
                  (metadata.startDate.month ?? 1) - 1,
                  metadata.startDate.day ?? 1,
              ).toLocaleDateString('en-US', {
                  day: 'numeric',
                  month: 'short',
                  year: 'numeric',
              })
            : null,
    );
</script>

<svelte:head>
    <title>{displayTitle}</title>
</svelte:head>

<Banner
    bannerImage={metadata?.bannerImage}
    coverImage={metadata?.coverImage?.extraLarge}
    title={displayTitle}
    summary={metadata?.description}
    isDraft={!anime.isPublished}
/>

<div class="p-4 w-full mx-auto lg:max-w-7xl xl:max-w-screen-2xl">
    <div class="mt-6 grid gap-4 md:gap-6 md:grid-cols-12">
        <div class="space-y-6 md:col-span-8">
            <Tabs.Root value="episodes">
                <Tabs.List>
                    <Tabs.Trigger value="episodes">
                        <Film data-icon="inline-start" />
                        Episodes ({episodes.length})
                    </Tabs.Trigger>
                    {#if metadata?.characters?.edges?.length}
                        <Tabs.Trigger value="casts">
                            <Clapperboard data-icon="inline-start" />
                            Casts
                        </Tabs.Trigger>
                    {/if}
                </Tabs.List>

                <Tabs.Content value="episodes" class="mt-4">
                    {#if episodes.length > 0}
                        <Item.Group>
                            {#each episodes as episode (episode.id)}
                                <Item.Root>
                                    {#snippet child({ props })}
                                        <a href={postShow.url({ anime: anime.id, post: episode.id })} {...props}>
                                            <Item.Media variant="image">
                                                {#if episode.thumbnail}
                                                    <img
                                                        src={episode.thumbnail
                                                            .medium}
                                                        alt=""
                                                        class="size-10 rounded object-cover"
                                                    />
                                                {/if}
                                            </Item.Media>
                                            <Item.Content>
                                                <Item.Title>
                                                    {#if episode.epNo}
                                                        EP {episode.epNo} -
                                                    {/if}
                                                    {episode.title.en ||
                                                        episode.title.romaji ||
                                                        episode.title.native ||
                                                        'Untitled'}
                                                </Item.Title>
                                                <Item.Description>
                                                    {#if episode.publishedAt}
                                                        {new Date(
                                                            episode.publishedAt,
                                                        ).toLocaleDateString(
                                                            'en-GB',
                                                            {
                                                                day: 'numeric',
                                                                month: 'short',
                                                                year: 'numeric',
                                                            },
                                                        )}
                                                    {/if}
                                                    {#if episode.author}
                                                        &bull; {episode.author
                                                            .name}
                                                    {/if}
                                                </Item.Description>
                                            </Item.Content>
                                            <Item.Actions>
                                                {#if !episode.isPublished}
                                                    <Badge variant="destructive"
                                                        >Draft</Badge
                                                    >
                                                {/if}
                                            </Item.Actions>
                                        </a>
                                    {/snippet}
                                </Item.Root>
                            {/each}
                        </Item.Group>
                    {:else}
                        <p class="text-muted-foreground py-8 text-center">
                            No episodes available.
                        </p>
                    {/if}

                    <!-- TODO: create FAB -->
                </Tabs.Content>

                {#if metadata?.characters?.edges?.length}
                    <Tabs.Content value="casts" class="mt-4">
                        <Casts characters={metadata.characters.edges} />
                    </Tabs.Content>
                {/if}
            </Tabs.Root>
        </div>

        <div
            class="flex flex-col gap-4 md:col-span-4 md:sticky md:top-4 md:self-start"
        >
            <Item.Group>
                {#if season}
                    <Item.Root>
                        <Item.Media variant="icon">
                            <Calendar />
                        </Item.Media>
                        <Item.Content>
                            <Item.Description>{season}</Item.Description>
                        </Item.Content>
                    </Item.Root>
                {/if}

                {#if episodeCount}
                    <Item.Root>
                        <Item.Media variant="icon">
                            <Hash />
                        </Item.Media>
                        <Item.Content>
                            <Item.Description
                                >{episodeCount} episodes</Item.Description
                            >
                        </Item.Content>
                    </Item.Root>
                {/if}

                {#if mainStudio}
                    <Item.Root>
                        <Item.Media variant="icon">
                            <Clapperboard />
                        </Item.Media>
                        <Item.Content>
                            <Item.Description>{mainStudio}</Item.Description>
                        </Item.Content>
                    </Item.Root>
                {/if}

                {#if startDate}
                    <Item.Root>
                        <Item.Media variant="icon">
                            <Info />
                        </Item.Media>
                        <Item.Content>
                            <Item.Description>{startDate}</Item.Description>
                        </Item.Content>
                    </Item.Root>
                {/if}
            </Item.Group>

            {#if metadata?.genres?.length}
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Genres</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <div class="flex flex-wrap gap-2">
                            {#each metadata.genres as genre (genre)}
                                <Badge variant="secondary">
                                    {#snippet child({ props })}
                                        <Link href={animeIndex({ query: { filter: { genreIn: [genre] } } })} {...props}>{genre}</Link>
                                    {/snippet}
                                </Badge>
                            {/each}
                        </div>
                    </Card.Content>
                </Card.Root>
            {/if}

            {#if metadata?.tags?.length}
                <Card.Root>
                    <Card.Header>
                        <Card.Title>Tags</Card.Title>
                    </Card.Header>
                    <Card.Content>
                        <div class="flex flex-wrap gap-2">
                            {#each metadata.tags as tag (tag.id)}
                                <Badge variant="outline">
                                    {#snippet child({ props })}
                                        <Link href={animeIndex({ query: { filter: { tagIn: [tag.id] } } })} {...props}>{tag.name}</Link>
                                    {/snippet}
                                </Badge>
                            {/each}
                        </div>
                    </Card.Content>
                </Card.Root>
            {/if}

            {#if !anime.isPublished}
                <!-- TODO: implement FAB -->
            {/if}
        </div>
    </div>
</div>
