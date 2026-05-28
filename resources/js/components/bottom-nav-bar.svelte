<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import Clapperboard from 'lucide-svelte/icons/clapperboard';
	import Disc3 from 'lucide-svelte/icons/disc-3';
	import Film from 'lucide-svelte/icons/film';
	import House from 'lucide-svelte/icons/house';
	import { currentUrlState } from '@/lib/currentUrl.svelte';
	import { toUrl } from '@/lib/utils';
	import { home } from '@/routes';
	import { index as album } from '@/routes/album';
	import { index as anime } from '@/routes/anime';
	import { index as mv } from '@/routes/mv';

	const url = currentUrlState();

	function navClass(href: ReturnType<typeof toUrl>): string {
		const resolved = toUrl(href);
		const isActive = url.isCurrentUrl(resolved, url.currentUrl) || url.isCurrentOrParentUrl(resolved, url.currentUrl);

		return isActive
			? 'relative flex flex-col items-center gap-0.5 px-3 pb-1 pt-2 text-[0.75rem] text-primary'
			: 'relative flex flex-col items-center gap-0.5 px-3 pb-1 pt-2 text-[0.75rem] text-muted-foreground';
	}
</script>

<nav class="fixed bottom-0 left-0 right-0 z-50 flex h-16 items-center justify-around border-t bg-background md:hidden">
	<Link
		href={toUrl(home())}
		class={navClass(home())}
	>
		<House class="size-4" />
		<span>Home</span>
	</Link>
	<Link
		href={toUrl(anime())}
		class={navClass(anime())}
	>
		<Film class="size-4" />
		<span>Anime</span>
	</Link>
	<Link
		href={toUrl(album())}
		class={navClass(album())}
	>
		<Disc3 class="size-4" />
		<span>Album</span>
	</Link>
	<Link
		href={toUrl(mv())}
		class={navClass(mv())}
	>
		<Clapperboard class="size-4" />
		<span>MV</span>
	</Link>
</nav>
