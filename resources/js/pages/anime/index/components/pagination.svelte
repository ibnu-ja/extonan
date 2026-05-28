<script lang="ts">
	import { ChevronLeft, ChevronRight } from 'lucide-svelte';

	type PaginationLink = {
		url: string | null;
		label: string;
		active: boolean;
	};

	let {
		links = [],
	}: {
		links: PaginationLink[];
	} = $props();
</script>

{#if links.length > 1}
	<nav class="flex items-center justify-center gap-1">
		{#each links as link (link.label + (link.url ?? ''))}
			{#if link.label === '&laquo; Previous'}
				<a
					href={link.url ?? '#'}
					class="inline-flex size-9 items-center justify-center rounded-lg text-sm transition-colors hover:bg-muted {link.active
						? 'bg-primary text-primary-foreground'
						: ''} {!link.url
						? 'pointer-events-none text-muted-foreground'
						: ''}"
				>
					<ChevronLeft class="size-4" />
				</a>
			{:else if link.label === '&raquo; Next'}
				<a
					href={link.url ?? '#'}
					class="inline-flex size-9 items-center justify-center rounded-lg text-sm transition-colors hover:bg-muted {link.active
						? 'bg-primary text-primary-foreground'
						: ''} {!link.url
						? 'pointer-events-none text-muted-foreground'
						: ''}"
				>
					<ChevronRight class="size-4" />
				</a>
			{:else}
				<a
					href={link.url ?? '#'}
					class="inline-flex size-9 items-center justify-center rounded-lg text-sm transition-colors hover:bg-muted {link.active
						? 'bg-primary text-primary-foreground'
						: ''} {!link.url
						? 'pointer-events-none text-muted-foreground'
						: ''}"
				>
					{link.label}
				</a>
			{/if}
		{/each}
	</nav>
{/if}
