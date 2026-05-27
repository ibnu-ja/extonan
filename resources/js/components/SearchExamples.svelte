<script lang="ts">
	import Search from "./Search.svelte";
	import SearchView from "./SearchView.svelte";
	import IconSearch from "@ktibow/iconset-material-symbols/search";
	import IconClose from "@ktibow/iconset-material-symbols/close";

	let searchQuery = $state("");
	let searchQuery2 = $state("");
	let dockedSearchQuery = $state("");
	let fullScreenActive = $state(false);
	let fullScreenQuery = $state("");

	function handleTrailingClick() {
		searchQuery = "";
	}

	function handleTrailingClick2() {
		searchQuery2 = "";
	}

	function handleDockedTrailingClick() {
		dockedSearchQuery = "";
	}

	function handleFullScreenClose() {
		fullScreenActive = false;
		fullScreenQuery = "";
	}
</script>

<div class="demo-container">
	<h2>Search Component Examples</h2>

	<section>
		<h3>Search Bar - Basic</h3>
		<Search bind:value={searchQuery} label="Search" />
	</section>

	<section>
		<h3>Search Bar - With Leading Icon</h3>
		<Search
			bind:value={searchQuery}
			label="Search"
			leadingIcon={IconSearch}
		/>
	</section>

	<section>
		<h3>Search Bar - With Icons and Clear Button</h3>
		<Search
			bind:value={searchQuery2}
			label="Search"
			leadingIcon={IconSearch}
			trailingIcon={searchQuery2 ? IconClose : undefined}
			onTrailingIconClick={handleTrailingClick2}
		/>
	</section>

	<section>
		<h3>Search Bar - With Supporting Text</h3>
		<Search
			bind:value={searchQuery}
			label="Search products"
			supportingText="Try searching for 'electronics' or 'clothing'"
			leadingIcon={IconSearch}
		/>
	</section>

	<hr />

	<h2>Search View Examples</h2>

	<section>
		<h3>Search View - Docked</h3>
		<SearchView
			bind:value={dockedSearchQuery}
			label="Search"
			viewType="docked"
			leadingIcon={IconSearch}
			trailingIcon={dockedSearchQuery ? IconClose : undefined}
			onTrailingIconClick={handleDockedTrailingClick}
		>
			<div slot="results" class="results-placeholder">
				{#if dockedSearchQuery}
					<p>Results for: <strong>{dockedSearchQuery}</strong></p>
				{:else}
					<p>No search query</p>
				{/if}
			</div>
		</SearchView>
	</section>

	<section>
		<h3>Search View - Full Screen</h3>
		<button onclick={() => (fullScreenActive = true)} class="open-fullscreen-btn">
			Open Full Screen Search
		</button>

		{#if fullScreenActive}
			<SearchView
				bind:value={fullScreenQuery}
				label="Search"
				viewType="full-screen"
				leadingIcon={IconSearch}
				trailingIcon={IconClose}
				onClose={handleFullScreenClose}
			>
				<div slot="results" class="fullscreen-results">
					{#if fullScreenQuery}
						<p>Results for: <strong>{fullScreenQuery}</strong></p>
						<p>This is a full screen search view</p>
					{:else}
						<p>Enter search query to see results</p>
					{/if}
				</div>
			</SearchView>
		{/if}
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

	hr {
		border: none;
		border-top: 1px solid var(--m3c-outline-variant);
		margin: 1rem 0;
	}

	.results-placeholder {
		padding: 1rem;
		color: var(--m3c-on-surface-variant);
		background-color: var(--m3c-surface-container-low);
	}

	.fullscreen-results {
		padding: 1.5rem;
		color: var(--m3c-on-surface-variant);
	}

	.open-fullscreen-btn {
		padding: 0.75rem 1rem;
		background-color: var(--m3c-primary);
		color: var(--m3c-on-primary);
		border: none;
		border-radius: var(--m3-shape-medium);
		font-size: 0.875rem;
		cursor: pointer;
		transition: background-color var(--m3-easing-fast);
	}

	.open-fullscreen-btn:hover {
		background-color: color-mix(in srgb, var(--m3c-primary), var(--m3c-on-primary) 8%);
	}

	.open-fullscreen-btn:active {
		background-color: color-mix(in srgb, var(--m3c-primary), var(--m3c-on-primary) 12%);
	}
</style>
