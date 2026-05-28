<script lang="ts">
	import { Link } from '@inertiajs/svelte';
	import ChevronRightIcon from '@lucide/svelte/icons/chevron-right';
	import * as Collapsible from '@/components/ui/collapsible/index.js';
	import * as Sidebar from '@/components/ui/sidebar/index.js';
	import { currentUrlState } from '@/lib/current-url.svelte';
	import { toUrl } from '@/lib/utils';
	import type { NavItem } from '@/types';

	let {
		items = [],
	}: {
		items: NavItem[];
	} = $props();

	const url = currentUrlState();
</script>

<Sidebar.Group>
	<Sidebar.GroupLabel>Platform</Sidebar.GroupLabel>
	<Sidebar.Menu>
		{#each items as item (item.href)}
			<Sidebar.MenuItem>
				{#if item.items?.length}
					<Collapsible.Root open={item.isActive}>
						{#snippet child({ props })}
							<Sidebar.MenuItem {...props}>
								<Sidebar.MenuButton tooltipContent={item.title}>
									{#snippet child({ props })}
										<Link {...props} href={toUrl(item.href)} class={props.class}>
											{#if item.icon}<item.icon />{/if}
											<span>{item.title}</span>
										</Link>
									{/snippet}
								</Sidebar.MenuButton>
								<Collapsible.Trigger>
									{#snippet child({ props })}
										<Sidebar.MenuAction {...props} class="data-[state=open]:rotate-90">
											<ChevronRightIcon />
											<span class="sr-only">Toggle</span>
										</Sidebar.MenuAction>
									{/snippet}
								</Collapsible.Trigger>
								<Collapsible.Content>
									<Sidebar.MenuSub>
										{#each item.items as subItem (subItem.href)}
											<Sidebar.MenuSubItem>
												<Sidebar.MenuSubButton href={subItem.href}>
													<span>{subItem.title}</span>
												</Sidebar.MenuSubButton>
											</Sidebar.MenuSubItem>
										{/each}
									</Sidebar.MenuSub>
								</Collapsible.Content>
							</Sidebar.MenuItem>
						{/snippet}
					</Collapsible.Root>
				{:else}
					<Sidebar.MenuButton isActive={url.isCurrentUrl(item.href, url.currentUrl)} tooltipContent={item.title} class="transition-all duration-200">
						{#snippet child({ props })}
							<Link {...props} href={toUrl(item.href)} class={props.class}>
								{#if item.icon}<item.icon class="size-4 shrink-0" />{/if}
								<span>{item.title}</span>
							</Link>
						{/snippet}
					</Sidebar.MenuButton>
				{/if}
			</Sidebar.MenuItem>
		{/each}
	</Sidebar.Menu>
</Sidebar.Group>
