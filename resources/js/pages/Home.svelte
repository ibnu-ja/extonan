<script lang="ts">
    import { setLayoutProps } from '@inertiajs/svelte';
    import Autoplay from 'embla-carousel-autoplay';
    import EpisodeThumbnail from '@/components/anime/episode-thumbnail.svelte';
    import AnimeSlideCard from '@/components/home/anime-slide-card.svelte';
    import MusicCard from '@/components/home/music-card.svelte';
    import SectionHeading from '@/components/home/section-heading.svelte';
    import * as Carousel from '@/components/ui/carousel';
    import * as Item from '@/components/ui/item';

    let {
        latestAnime,
        latestEpisodes,
        latestMv,
        latestAlbum,
    }: App.Data.Home.HomePageResponse = $props();

    $effect(() => {
        setLayoutProps({ showHeading: false });
    });
</script>

<svelte:head>
    <title>Home</title>
</svelte:head>

{#if latestAnime.length > 0}
    <div class="relative overflow-hidden">
        <Carousel.Root
            opts={{ loop: true, align: 'start', skipSnaps: true }}
            plugins={[Autoplay({ delay: 5000 })]}
        >
            <Carousel.Content class="h-64 md:h-80 ms-0">
                {#each latestAnime as anime (anime.id)}
                    <Carousel.Item class="h-full basis-full ps-0">
                        <AnimeSlideCard
                            title={anime.title}
                            genres={anime.genres}
                            bannerImage={anime.bannerImage}
                            coverImage={anime.coverImage}
                            id={anime.id}
                        />
                    </Carousel.Item>
                {/each}
            </Carousel.Content>
        </Carousel.Root>
    </div>
{:else}
    <div class="flex h-64 items-center justify-center bg-muted md:h-80">
        <p class="text-muted-foreground">
            Add anime to show latest anime slider.
        </p>
    </div>
{/if}

<section class="mt-4 px-0 sm:mt-8 md:px-4">
    <div class="px-4">
        <SectionHeading title="Latest Episodes" />
    </div>
    {#if latestEpisodes.length > 0}
        <div
            class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        >
            {#each latestEpisodes as episode (episode.id)}
                <EpisodeThumbnail
                    {episode}
                    animeId={episode.animeId}
                    animeTitle={episode.animeTitle}
                />
            {/each}
        </div>
    {:else}
        <div class="flex h-32 items-center justify-center rounded-xl bg-muted">
            <p class="text-muted-foreground">No episodes published yet.</p>
        </div>
    {/if}
</section>

<section class="mt-4 px-0 sm:mt-8 md:px-4">
    <div class="px-4">
        <SectionHeading title="Latest MV" />
    </div>
    {#if latestMv.length > 0}
        <div class="relative group">
            <Carousel.Root opts={{ align: 'start', skipSnaps: true }}>
                <Carousel.Content class="ms-0 pe-4">
                    {#each latestMv as mv (mv.id)}
                        <Carousel.Item
                            class="basis-1/2 sm:basis-1/3 md:basis-1/4 lg:basis-1/5 xl:basis-1/6"
                        >
                            <MusicCard
                                title={mv.title}
                                thumbnail={mv.thumbnail}
                                author={mv.author}
                                publishedAt={mv.publishedAt}
                                isPublished={mv.isPublished}
                                id={mv.id}
                                permissions={mv.permissions}
                                aspect="video"
                            />
                        </Carousel.Item>
                    {/each}
                </Carousel.Content>
                <Carousel.Previous
                    class="left-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto"
                />
                <Carousel.Next
                    class="right-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto"
                />
            </Carousel.Root>
        </div>
    {:else}
        <div class="flex h-32 items-center justify-center rounded-xl bg-muted">
            <p class="text-muted-foreground">No music videos published yet.</p>
        </div>
    {/if}
</section>

<section class="mt-4 px-0 sm:mt-8 md:px-4">
    <div class="px-4">
        <SectionHeading title="Latest Album" />
    </div>
    {#if latestAlbum.length > 0}
        <div class="relative group">
            <Carousel.Root opts={{ align: 'start', skipSnaps: true }}>
                <Carousel.Content class="ms-0 pe-4">
                    {#each latestAlbum as album (album.id)}
                        <Carousel.Item
                            class="basis-1/2 sm:basis-1/3 md:basis-1/4 lg:basis-1/5 xl:basis-1/6"
                        >
                            <MusicCard
                                title={album.title}
                                thumbnail={album.thumbnail}
                                author={album.author}
                                publishedAt={album.publishedAt}
                                isPublished={album.isPublished}
                                id={album.id}
                                permissions={album.permissions}
                            />
                        </Carousel.Item>
                    {/each}
                </Carousel.Content>
                <Carousel.Previous
                    class="left-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto"
                />
                <Carousel.Next
                    class="right-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto"
                />
            </Carousel.Root>
        </div>
    {:else}
        <div class="flex h-32 items-center justify-center rounded-xl bg-muted">
            <p class="text-muted-foreground">No albums published yet.</p>
        </div>
    {/if}
</section>
