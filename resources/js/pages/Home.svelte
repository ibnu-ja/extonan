<script lang="ts">
	import Layout from '@/layouts/Layout.svelte';
	import Card from '@/components/cards/Card.svelte';
	import { Button } from "m3-svelte";
	import { inertiaNav } from '@/lib/inertia-nav';
    import { inertia } from '@inertiajs/svelte';

	const videos = Array.from({ length: 12 }, (_, i) => ({
		id: i + 1,
		title: `Video ${i + 1}`,
		description: 'Video description goes here',
		thumbnail: `https://placehold.co/600x400?text=Video+${i + 1}`
	}));
</script>

<svelte:head>
	<title>Home</title>
</svelte:head>

<Layout>
    <div class="nav-buttons">
        <Button href="/dashboard" data-preserve-scroll="true" {@attach inertiaNav}>
            Dashboard
        </Button>
    </div>
	<div class="content">
		<h1 class="heading">Home</h1>

		<!-- Responsive Grid of Video Cards -->
		<div class="grid">
			{#each videos as video (video.id)}
				<Card
                    {@attach inertiaNav}
                    href="/"
                    variant="elevated"
					image={{ src: video.thumbnail, alt: video.title }}
					headline={video.title}
					supporting={video.description}
				/>
			{/each}
		</div>
	</div>
    <a href="/#test" use:inertia class="dashboard-link">
        Dashboard
    </a>
</Layout>

<style>
	.nav-buttons {
		padding: 1.5rem;
	}

	.heading {
		font-family: var(--m3-font);
		font-size: 1.875rem;
		font-weight: 700;
		line-height: 1.2;
		margin: 0 0 2rem 0;
		color: var(--m3c-on-surface);
	}

	.grid {
		display: grid;
		gap: 1rem;
		grid-template-columns: repeat(1, minmax(0, 1fr));
	}

	@media (width >= 36rem) {
		.grid {
			grid-template-columns: repeat(2, minmax(0, 1fr));
		}
	}

	@media (width >= 64rem) {
		.grid {
			grid-template-columns: repeat(3, minmax(0, 1fr));
		}
	}

	@media (width >= 80rem) {
		.grid {
			grid-template-columns: repeat(4, minmax(0, 1fr));
		}
	}

	.dashboard-link {
		padding: 1.5rem;
		cursor: pointer;
	}
</style>
