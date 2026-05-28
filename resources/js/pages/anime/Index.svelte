<script module lang="ts">
	import { index as animeIndex } from '@/routes/anime';

	export const layout = {
		breadcrumbs: [
			{ title: 'Anime', href: animeIndex() },
		],
	};
</script>

<script lang="ts">
	import { page } from '@inertiajs/svelte';

	let data = $derived(page.props);
</script>

<svelte:head>
	<title>Anime Index</title>
</svelte:head>

<pre class="p-4 text-xs">{JSON.stringify(data, null, 2)}</pre>
