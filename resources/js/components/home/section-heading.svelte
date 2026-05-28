<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import { toUrl } from '@/lib/utils';
	import { cn } from '@/lib/utils';

	let {
		title,
		href = undefined,
		class: className = '',
	}: {
		title: string;
		href?: string;
		class?: string;
	} = $props();
</script>

<div class={cn('mb-4 flex items-center justify-between', className)}>
	<h2 class="font-heading text-lg font-semibold">{title}</h2>
	{#if href}
		<Link href={toUrl(href)} class="text-sm text-muted-foreground hover:text-foreground">View all</Link>
	{/if}
</div>
