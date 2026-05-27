<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import logoFullSrcLight from '@/assets/logo/inline color.svg';
	import logoFullSrcDark from '@/assets/logo/inline white.svg';
	import logoSrcLight from '@/assets/logo/logo color.svg';
	import logoSrcDark from '@/assets/logo/logo white.svg';
	import { toUrl } from '@/lib/utils';
	import { home } from '@/routes';

	const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

	let isDark = $state(false);

	$effect(() => {
		const el = document.documentElement;
		isDark = el.classList.contains('dark');
		const observer = new MutationObserver(() => {
			isDark = el.classList.contains('dark');
		});
		observer.observe(el, { attributes: true, attributeFilter: ['class'] });

		return () => observer.disconnect();
	});
</script>

<Link href={toUrl(home())} class="mx-auto flex items-center justify-center">
	{#if isDark}
		<img src={logoSrcDark} alt={appName} class="mx-auto h-8 w-auto group-data-[collapsible=icon]:block hidden" />
		<img src={logoFullSrcDark} alt={appName} class="mx-auto h-8 w-auto group-data-[collapsible=icon]:hidden" />
	{:else}
		<img src={logoSrcLight} alt={appName} class="mx-auto h-8 w-auto group-data-[collapsible=icon]:block hidden" />
		<img src={logoFullSrcLight} alt={appName} class="mx-auto h-8 w-auto group-data-[collapsible=icon]:hidden" />
	{/if}
</Link>
