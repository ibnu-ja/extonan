<script lang="ts">
	import logoSrcLight from '@/assets/logo/logo color.svg';
	import logoSrcDark from '@/assets/logo/logo white.svg';

	let {
		class: className = '',
	}: {
		class?: string;
		[key: string]: unknown;
	} = $props();

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

{#if isDark}
	<img src={logoSrcDark} alt="" class={className} />
{:else}
	<img src={logoSrcLight} alt="" class={className} />
{/if}
