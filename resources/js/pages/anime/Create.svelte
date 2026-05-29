<script module lang="ts">
    import { index as animeIndex } from '@/routes/anime';

    export const layout = {
        breadcrumbs: [
            { title: 'Anime', href: animeIndex() },
            { title: 'Create', href: '' },
        ],
    };
</script>

<script lang="ts">
    type CreatePageProps =
        | ({ anime?: undefined } & App.Data.Anime.AnimeFormData)
        | { anime: App.Data.Anime.AnimeFormData };

    let props: CreatePageProps = $props();
    let pageTitle = $derived(props.anime ? 'Edit Anime' : 'Create Anime');
</script>

<svelte:head>
    <title>{pageTitle}</title>
</svelte:head>

<pre class="p-4 text-xs">{JSON.stringify(props, null, 2)}</pre>
