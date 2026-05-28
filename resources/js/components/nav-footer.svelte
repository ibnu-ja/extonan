<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import * as Sidebar from '@/components/ui/sidebar/index.js';
	import { toUrl } from '@/lib/utils';
	import type { NavItem } from '@/types';

	let {
		items = [],
		class: className = '',
	}: {
		items: NavItem[];
		class?: string;
	} = $props();
</script>

<Sidebar.Group class={`group-data-[collapsible=icon]:p-0 ${className}`}>
	<Sidebar.GroupContent>
		<Sidebar.Menu>
			{#each items as item (toUrl(item.href))}
				<Sidebar.MenuItem>
					<Sidebar.MenuButton class="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100 transition-all duration-200">
						{#snippet child({ props })}
							<Link {...props} href={toUrl(item.href)} class={props.class}>
								{#if item.icon}<item.icon class="size-4 shrink-0" />{/if}
								<span>{item.title}</span>
							</Link>
						{/snippet}
					</Sidebar.MenuButton>
				</Sidebar.MenuItem>
			{/each}
		</Sidebar.Menu>
	</Sidebar.GroupContent>
</Sidebar.Group>
