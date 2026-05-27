<script lang="ts">
	import * as Carousel from '@/components/ui/carousel';
	import Autoplay from 'embla-carousel-autoplay';
	import AnimeSlideCard from '@/components/home/AnimeSlideCard.svelte';
	import EpisodeCard from '@/components/home/EpisodeCard.svelte';
	import MusicCard from '@/components/home/MusicCard.svelte';
	import SectionHeading from '@/components/home/SectionHeading.svelte';

	let {
		latestAnime,
		latestEpisodes,
		latestMv,
		latestAlbum,
	}: {
		latestAnime: App.Data.AnimeSummaryData[];
		latestEpisodes: App.Data.EpisodeSummaryData[];
		latestMv: App.Data.MusicSummaryData[];
		latestAlbum: App.Data.MusicSummaryData[];
	} = $props();
</script>

<svelte:head>
	<title>Home</title>
</svelte:head>

{#if latestAnime.length > 0}
	<div class="relative overflow-hidden">
		<Carousel.Root
			opts={{ loop: true, align: 'start' }}
			plugins={[Autoplay({ delay: 5000 })]}
		>
			<Carousel.Content class="h-64 md:h-80">
				{#each latestAnime as anime (anime.id)}
					<Carousel.Item class="h-full basis-full">
						<AnimeSlideCard
							title={anime.title}
							slug={anime.slug}
							genres={anime.genres}
							bannerImage={anime.bannerImage}
							coverImage={anime.coverImage}
							link={anime.link}
						/>
					</Carousel.Item>
				{/each}
			</Carousel.Content>
		</Carousel.Root>
	</div>
{:else}
	<div class="flex h-64 items-center justify-center bg-muted md:h-80">
		<p class="text-muted-foreground">Add anime to show latest anime slider.</p>
	</div>
{/if}

<section class="mt-4 px-2 sm:mt-8 sm:px-4">
	<SectionHeading title="Latest Episodes" />
	{#if latestEpisodes.length > 0}
		<div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
			{#each latestEpisodes as episode (episode.id)}
				<EpisodeCard
					id={episode.id}
					title={episode.title}
					epNo={episode.epNo}
					postType={episode.postType}
					thumbnail={episode.thumbnail}
					animeTitle={episode.animeTitle}
					author={episode.author}
					publishedAt={episode.publishedAt}
					isPublished={episode.isPublished}
					isCurrent={episode.isCurrent}
					link={episode.link}
					permissions={episode.permissions}
				/>
			{/each}
		</div>
	{:else}
		<div class="flex h-32 items-center justify-center rounded-xl bg-muted">
			<p class="text-muted-foreground">No episodes published yet.</p>
		</div>
	{/if}
</section>

<section class="mt-4 sm:mt-8">
	<div class="px-4">
		<SectionHeading title="Latest MV" />
	</div>
	{#if latestMv.length > 0}
		<div class="relative group">
			<Carousel.Root opts={{ align: 'start' }}>
				<Carousel.Content class="ms-0 pe-4">
					{#each latestMv as mv (mv.id)}
						<Carousel.Item class="basis-1/2 sm:basis-1/3 md:basis-1/4 lg:basis-1/5 xl:basis-1/6">
							<MusicCard
								id={mv.id}
								title={mv.title}
								thumbnail={mv.thumbnail}
								author={mv.author}
								publishedAt={mv.publishedAt}
								isPublished={mv.isPublished}
								link={mv.link}
								permissions={mv.permissions}
								aspect="video"
							/>
						</Carousel.Item>
					{/each}
				</Carousel.Content>
				<Carousel.Previous class="left-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto" />
				<Carousel.Next class="right-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto" />
			</Carousel.Root>
		</div>
	{:else}
		<div class="flex h-32 items-center justify-center rounded-xl bg-muted">
			<p class="text-muted-foreground">No music videos published yet.</p>
		</div>
	{/if}
</section>

<section class="mt-4 sm:mt-8">
	<div class="px-4">
		<SectionHeading title="Latest Album" />
	</div>
	{#if latestAlbum.length > 0}
		<div class="relative group">
			<Carousel.Root opts={{ align: 'start' }}>
				<Carousel.Content class="ms-0 pe-4">
					{#each latestAlbum as album (album.id)}
						<Carousel.Item class="basis-1/2 sm:basis-1/3 md:basis-1/4 lg:basis-1/5 xl:basis-1/6">
							<MusicCard
								id={album.id}
								title={album.title}
								thumbnail={album.thumbnail}
								author={album.author}
								publishedAt={album.publishedAt}
								isPublished={album.isPublished}
								link={album.link}
								permissions={album.permissions}
							/>
						</Carousel.Item>
					{/each}
				</Carousel.Content>
				<Carousel.Previous class="left-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto" />
				<Carousel.Next class="right-2 top-2/5 mt-1 opacity-0 transition-all duration-300 pointer-events-none group-hover:mt-0 group-hover:opacity-100 group-hover:pointer-events-auto" />
			</Carousel.Root>
		</div>
	{:else}
		<div class="flex h-32 items-center justify-center rounded-xl bg-muted">
			<p class="text-muted-foreground">No albums published yet.</p>
		</div>
	{/if}
</section>
