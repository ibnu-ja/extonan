<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import { Badge } from '@/components/ui/badge';
	import type {} from '@/types/generated';

	let {
		title,
		genres,
		bannerImage,
		coverImage,
		link,
	}: {
		title: Record<string, string | null>;
		genres: string[];
		bannerImage: string | null;
		coverImage: { extraLarge: string; large: string; medium: string; color: string };
		link: string;
	} = $props();

	const bgImage = $derived(
		`linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.1) 100%), url('${bannerImage || coverImage.extraLarge}')`,
	);

	const displayTitle = $derived(title.en || title.romaji || title.native || '');
</script>

<Link
	href={link}
	class="flex h-full w-full flex-col justify-end bg-cover bg-center p-5 md:p-10"
	style="background-image: {bgImage}"
>
	<h3 class="font-heading mb-4 text-2xl font-bold text-white md:text-3xl">{displayTitle}</h3>
	<div class="flex flex-wrap gap-2">
		{#each genres as genre (genre)}
			<Badge variant="secondary" class="bg-white/20 text-white hover:bg-white/30">{genre}</Badge>
		{/each}
	</div>
</Link>
