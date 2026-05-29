<script module lang="ts">
    import { index as animeIndex } from '@/routes/anime';

    export const layout = {
        breadcrumbs: [
            { title: 'Anime', href: animeIndex() },
            { title: 'Show', href: '' },
        ],
    };
</script>

<script lang="ts">
    let {
        anime,
        episodes,
        canCreateEpisode,
    }: App.Data.Anime.AnimeShowResponse = $props();
</script>

<svelte:head>
    <title>Anime</title>
</svelte:head>

<pre class="p-4 text-xs">{JSON.stringify(
        { anime, episodes, canCreateEpisode },
        null,
        2,
    )}</pre>
