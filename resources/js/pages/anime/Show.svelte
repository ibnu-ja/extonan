<script lang="ts">
    import { Link, setLayoutProps } from '@inertiajs/svelte';
    import Calendar from '@lucide/svelte/icons/calendar';
    import Clapperboard from '@lucide/svelte/icons/clapperboard';
    import Film from '@lucide/svelte/icons/film';
    import Hash from '@lucide/svelte/icons/hash';
    import Info from '@lucide/svelte/icons/info';
    import LayoutGrid from '@lucide/svelte/icons/layout-grid';
    import List from '@lucide/svelte/icons/list';
    import Banner from '@/components/anime/banner.svelte';
    import Casts from '@/components/anime/casts.svelte';
    import EpisodeListItem from '@/components/anime/episode-list-item.svelte';
    import EpisodeThumbnail from '@/components/anime/episode-thumbnail.svelte';
    import { Badge } from '@/components/ui/badge';
    import * as Item from '@/components/ui/item';
    import * as Tabs from '@/components/ui/tabs';
    import * as ToggleGroup from '@/components/ui/toggle-group';
    import { t } from '@/lib/locale.svelte';
    import { index as animeIndex } from '@/routes/anime';

    let { anime, episodes, metadata }: App.Data.Anime.AnimeShowResponse =
        $props();

    const displayTitle = $derived(t(anime.title));

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

    let activeTab = $state('episodes');
    let displayMode = $state<'thumbnail' | 'list'>('list');
</script>

<svelte:head>
    <title>{displayTitle}</title>
</svelte:head>

<Banner
    bannerImage={metadata?.bannerImage}
    coverImage={metadata?.coverImage?.extraLarge}
    title={anime.title}
    summary={metadata?.description}
    isDraft={!anime.isPublished}
/>

<div class="w-full mx-auto lg:max-w-7xl xl:max-w-screen-2xl">
    <div class="grid gap-4 md:gap-6 md:grid-cols-12">
        <div class="space-y-6 md:col-span-8 lg:col-span-9">
            <Tabs.Root bind:value={activeTab}>
                <div class="flex items-center justify-between px-4">
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
                    {#if activeTab === 'episodes'}
                        <ToggleGroup.Root
                            type="single"
                            variant="outline"
                            size="sm"
                            bind:value={displayMode}
                        >
                            <ToggleGroup.Item
                                value="thumbnail"
                                aria-label="Thumbnail view"
                            >
                                <LayoutGrid class="size-4" />
                            </ToggleGroup.Item>
                            <ToggleGroup.Item
                                value="list"
                                aria-label="List view"
                            >
                                <List class="size-4" />
                            </ToggleGroup.Item>
                        </ToggleGroup.Root>
                    {/if}
                </div>

                <Tabs.Content value="episodes" class="mt-4">
                    {#if episodes.length > 0}
                        {#if displayMode === 'thumbnail'}
                            <div
                                class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 md:p-4"
                            >
                                {#each episodes as episode (episode.id)}
                                    <EpisodeThumbnail
                                        {episode}
                                        animeId={anime.id}
                                    />
                                {/each}
                            </div>
                        {:else}
            <Item.Group class="px-0 md:px-4">
                                {#each episodes as episode (episode.id)}
                                    <EpisodeListItem
                                        {episode}
                                        animeId={anime.id}
                                    />
                                {/each}
                            </Item.Group>
                        {/if}
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
            class="flex flex-col gap-4 md:col-span-4 lg:col-span-3 md:sticky md:top-4 md:self-start"
        >
            {#if metadata?.genres?.length}
                <div class="px-4 md:px-0">
                    <h3 class="text-lg font-semibold font-heading mb-2">
                        Genres
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        {#each metadata.genres as genre (genre)}
                            <Badge variant="secondary">
                                {#snippet child({ props })}
                                    <Link
                                        href={animeIndex({
                                            query: {
                                                filter: { genreIn: [genre] },
                                            },
                                        })}
                                        {...props}>{genre}</Link
                                    >
                                {/snippet}
                            </Badge>
                        {/each}
                    </div>
                </div>
            {/if}

            {#if metadata?.tags?.length}
                <div class="px-4 md:px-0">
                    <h3 class="text-lg font-semibold font-heading mb-2">
                        Tags
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        {#each metadata.tags as tag (tag.id)}
                            <Badge variant="outline">
                                {#snippet child({ props })}
                                    <Link
                                        href={animeIndex({
                                            query: {
                                                filter: { tagIn: [tag.id] },
                                            },
                                        })}
                                        {...props}>{tag.name}</Link
                                    >
                                {/snippet}
                            </Badge>
                        {/each}
                    </div>
                </div>
            {/if}

                            <Item.Group class="px-0 md:px-4">
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

            {#if !anime.isPublished}
                <!-- TODO: implement FAB -->
            {/if}
        </div>
    </div>
</div>
