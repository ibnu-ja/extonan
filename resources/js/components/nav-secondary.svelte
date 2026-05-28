<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import type { Component, ComponentProps } from 'svelte';
	import * as Sidebar from '@/components/ui/sidebar/index.js';
	import { toUrl } from '@/lib/utils';

	let {
		ref = $bindable(null),
		items,
		...restProps
	}: {
		items: {
			title: string;
			href: string;
			icon: Component;
		}[];
	} & ComponentProps<typeof Sidebar.Group> = $props();
</script>

<Sidebar.Group bind:ref {...restProps}>
	<Sidebar.GroupContent>
		<Sidebar.Menu>
			{#each items as item (item.href)}
				<Sidebar.MenuItem>
					<Sidebar.MenuButton size="sm">
						{#snippet child({ props })}
							<Link {...props} href={toUrl(item.href)} class={props.class}>
								<item.icon />
								<span>{item.title}</span>
							</Link>
						{/snippet}
					</Sidebar.MenuButton>
				</Sidebar.MenuItem>
			{/each}
		</Sidebar.Menu>
	</Sidebar.GroupContent>
</Sidebar.Group>
