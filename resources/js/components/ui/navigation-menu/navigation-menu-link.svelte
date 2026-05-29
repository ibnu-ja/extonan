<script lang="ts">
	import { NavigationMenu as NavigationMenuPrimitive } from "bits-ui";
	import { cn, type WithElementRef } from "@/lib/utils.js";
	import type { Snippet } from "svelte";

	type LinkProps = WithElementRef<NavigationMenuPrimitive.LinkProps> & {
		children?: Snippet;
		child?: Snippet<[{ props: Record<string, unknown> }]>;
	};

	let {
		ref = $bindable(null),
		class: className,
		children,
		child,
		...restProps
	}: LinkProps = $props();
</script>

{#if child}
	{@render child({ props: restProps })}
{:else}
	<NavigationMenuPrimitive.Link
		bind:ref
		data-slot="navigation-menu-link"
		class={cn("data-active:focus:bg-muted data-active:hover:bg-muted data-active:bg-muted/50 focus-visible:ring-ring/50 hover:bg-muted focus:bg-muted flex items-center gap-2 rounded-lg p-2 text-sm transition-all outline-none focus-visible:ring-3 focus-visible:outline-1 in-data-[slot=navigation-menu-content]:rounded-md [&_svg:not([class*='size-'])]:size-4", className)}
		{...restProps}
	>
		{@render children?.()}
	</NavigationMenuPrimitive.Link>
{/if}
