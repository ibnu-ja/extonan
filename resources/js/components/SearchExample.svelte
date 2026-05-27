<script lang="ts">
	import Search from "./Search.svelte";
	import SearchView from "./SearchView.svelte";
	import IconSearch from "@ktibow/iconset-material-symbols/search";
	import IconClose from "@ktibow/iconset-material-symbols/close";

	let searchQuery = $state("");
	let searchViewQuery = $state("");
	let showSearchView = $state(false);

</script>

<div class="demo-container">
	<h2>Search Examples</h2>

	<section>
		<h3>Basic Search</h3>
		<Search bind:value={searchQuery} prependIcon={IconSearch} />
	</section>

	<section>
		<h3>Search with Append Slot</h3>
		<Search bind:value={searchQuery} prependIcon={IconSearch}>
			{#snippet children()}
				<button class="append-button">Filter</button>
			{/snippet}
		</Search>
	</section>

	<section>
		<h3>SearchView - Docked</h3>
		<SearchView
			bind:value={searchViewQuery}
			viewType="docked"
			prependIcon={IconSearch}
			onClose={() => (showSearchView = false)}
		>
			{#snippet results()}
				<div class="results">Results go here</div>
			{/snippet}
		</SearchView>
	</section>
</div>

<style>
	.demo-container {
		display: flex;
		flex-direction: column;
		gap: 2rem;
		padding: 2rem;
		max-width: 600px;
	}

	h2 {
		@apply --m3-headline-large;
		color: var(--m3c-on-surface);
		margin: 0;
	}

	section {
		display: flex;
		flex-direction: column;
		gap: 0.75rem;
	}

	h3 {
		@apply --m3-title-medium;
		color: var(--m3c-on-surface);
		margin: 0;
	}

	.append-button {
		padding: 0.5rem 1rem;
		background: none;
		border: none;
		color: var(--m3c-on-surface);
		cursor: pointer;
		font-size: 0.875rem;
	}

	.results {
		padding: 1rem;
		color: var(--m3c-on-surface-variant);
	}
</style>
