<script module lang="ts">
    import { index as animeIndex } from '@/routes/anime';

    export const layout = {
        breadcrumbs: [
            { title: 'Anime', href: animeIndex() },
            { title: 'A-Z', href: '' },
        ],
    };
</script>

<script lang="ts">
    let {
        items,
        canCreate,
    }: App.Data.Anime.AnimeAZResponse = $props();
</script>

<svelte:head>
    <title>Anime A-Z</title>
</svelte:head>

<pre class="p-4 text-xs">{JSON.stringify({ items, canCreate }, null, 2)}</pre>
