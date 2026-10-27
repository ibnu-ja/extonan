<script lang="ts">
    import { Link, router, setLayoutProps } from '@inertiajs/svelte';
    import Banner from '@/components/anime/banner.svelte';
    import Casts from '@/components/anime/casts.svelte';
    import { Icon } from '@/components/icon';
    import PostCard from '@/components/post-card.svelte';
    import PostListItem from '@/components/post-list-item.svelte';
    import { Badge } from '@/components/ui/badge';
    import * as Item from '@/components/ui/item';
    import * as SpeedDial from '@/components/ui/speed-dial/index.js';
    import * as Tabs from '@/components/ui/tabs';
    import * as ToggleGroup from '@/components/ui/toggle-group';
    import { t } from '@/lib/locale.svelte';
    import {
        index as animeIndex,
        edit as animeEdit,
        destroy as animeDestroy,
        update as animeUpdate,
    } from '@/routes/anime';
    import { create as postCreate } from '@/routes/post';

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
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/film')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/movie')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/Film01Icon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/FilmStripIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/film-line')}
                                class="size-4"
                                data-icon="inline-start"
                            />
                            Episodes ({episodes.length})
                        </Tabs.Trigger>
                        {#if metadata?.characters?.edges?.length}
                            <Tabs.Trigger value="casts">
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/clapperboard')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/movie')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/ClapperboardIcon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/FilmSlateIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/clapperboard-line')}
                                    class="size-4"
                                    data-icon="inline-start"
                                />
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
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/layout-grid')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/layout-grid')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/GridIcon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/SquaresFourIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/grid-line')}
                                    class="size-4"
                                />
                            </ToggleGroup.Item>
                            <ToggleGroup.Item
                                value="list"
                                aria-label="List view"
                            >
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/list')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/list')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/Menu01Icon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/ListIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/list-unordered')}
                                    class="size-4"
                                />
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
                                    <PostCard
                                        post={episode}
                                        animeId={anime.id}
                                        animeTitle={anime.title}
                                    />
                                {/each}
                            </div>
                        {:else}
                            <Item.Group class="px-0 md:px-4">
                                {#each episodes as episode (episode.id)}
                                    <PostListItem
                                        post={episode}
                                        animeId={anime.id}
                                        animeTitle={anime.title}
                                    />
                                {/each}
                            </Item.Group>
                        {/if}
                    {:else}
                        <p class="text-muted-foreground py-8 text-center">
                            No episodes available.
                        </p>
                    {/if}
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
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/calendar')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/calendar')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/Calendar03Icon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/CalendarIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/calendar-line')}
                                class="size-4"
                            />
                        </Item.Media>
                        <Item.Content>
                            <Item.Description>{season}</Item.Description>
                        </Item.Content>
                    </Item.Root>
                {/if}

                {#if episodeCount}
                    <Item.Root>
                        <Item.Media variant="icon">
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/hash')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/hash')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/Tag01Icon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/HashIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/hashtag')}
                                class="size-4"
                            />
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
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/clapperboard')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/movie')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/ClapperboardIcon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/FilmSlateIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/clapperboard-line')}
                                class="size-4"
                            />
                        </Item.Media>
                        <Item.Content>
                            <Item.Description>{mainStudio}</Item.Description>
                        </Item.Content>
                    </Item.Root>
                {/if}

                {#if startDate}
                    <Item.Root>
                        <Item.Media variant="icon">
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/info')}
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/info-circle')}
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/InformationCircleIcon')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/InfoIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/information-line')}
                                class="size-4"
                            />
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

<SpeedDial.Root>
    <SpeedDial.Trigger />
    <SpeedDial.Content>
        <SpeedDial.Item variant="default">
            {#snippet child({ props })}
                <Link href={animeEdit(anime.id).url} {...props}>
                    <Icon
                        lucide={() => import('lucide-svelte/icons/pencil')}
                        tabler={() =>
                            import('@tabler/icons-svelte/icons/pencil')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/PencilEdit02Icon')}
                        phosphor={() =>
                            import('phosphor-svelte/lib/PencilSimpleIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/edit-line')}
                        class="size-4"
                    />
                    Edit
                </Link>
            {/snippet}
        </SpeedDial.Item>
        <SpeedDial.Item
            variant="destructive"
            onclick={() => {
                if (confirm('Delete this anime?')) {
                    router.delete(animeDestroy(anime.id).url);
                }
            }}
        >
            <Icon
                lucide={() => import('lucide-svelte/icons/trash-2')}
                tabler={() => import('@tabler/icons-svelte/icons/trash')}
                hugeicons={() =>
                    import('@hugeicons/core-free-icons/Delete02Icon')}
                phosphor={() => import('phosphor-svelte/lib/TrashIcon')}
                remixicon={() =>
                    import('remixicon-svelte/icons/delete-bin-line')}
                class="size-4"
            />
            Delete
        </SpeedDial.Item>
        <SpeedDial.Item variant="secondary">
            {#snippet child({ props })}
                <Link href={postCreate(anime.id).url} {...props}>
                    <Icon
                        lucide={() => import('lucide-svelte/icons/plus')}
                        tabler={() => import('@tabler/icons-svelte/icons/plus')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/PlusSignIcon')}
                        phosphor={() => import('phosphor-svelte/lib/PlusIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/add-line')}
                        class="size-4"
                    />
                    Add Episode
                </Link>
            {/snippet}
        </SpeedDial.Item>
        {#if !anime.isPublished}
            <SpeedDial.Item
                variant="default"
                onclick={() =>
                    router.patch(animeUpdate(anime.id).url, {
                        isPublished: true,
                    })}
            >
                <Icon
                    lucide={() => import('lucide-svelte/icons/send')}
                    tabler={() => import('@tabler/icons-svelte/icons/send')}
                    hugeicons={() =>
                        import('@hugeicons/core-free-icons/SentIcon')}
                    phosphor={() =>
                        import('phosphor-svelte/lib/PaperPlaneTiltIcon')}
                    remixicon={() =>
                        import('remixicon-svelte/icons/send-plane-line')}
                    class="size-4"
                />
                Publish
            </SpeedDial.Item>
        {/if}
    </SpeedDial.Content>
</SpeedDial.Root>
