<script lang="ts">
	import UserNav from '@/components/user-nav.svelte';
	import type { BreadcrumbItem } from '@/types';

	let {
		breadcrumbs = [],
	}: {
		breadcrumbs?: BreadcrumbItem[];
	} = $props();

	let lastScrollY = $state(0);
	let hidden = $state(false);

	$effect(() => {
		if (typeof window === 'undefined') {
return;
}

		const onScroll = () => {
			const sy = window.scrollY;

			if (sy > lastScrollY && sy > 50) {
				hidden = true;
			} else if (sy < lastScrollY) {
				hidden = false;
			}

			lastScrollY = sy;
		};
		window.addEventListener('scroll', onScroll, { passive: true });

		return () => window.removeEventListener('scroll', onScroll);
	});
</script>

<header
	class="fixed top-0 left-0 right-0 z-50 flex h-16 items-center gap-2 border-b bg-background px-4 transition-transform duration-300"
	style={hidden ? 'transform: translateY(-100%);' : ''}
>
	{#if breadcrumbs && breadcrumbs.length > 0}
		<nav class="flex items-center gap-1 text-sm text-muted-foreground">
			{#each breadcrumbs as crumb, i (crumb.href)}
				{#if i > 0}
					<span class="text-muted-foreground/40">/</span>
				{/if}
				<span class={i === breadcrumbs.length - 1 ? 'font-medium text-foreground' : ''}>
					{crumb.title}
				</span>
			{/each}
		</nav>
	{/if}

	<UserNav />
</header>
