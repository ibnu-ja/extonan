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

	const linkProps = $derived({
		class: cn("data-[active=true]:focus:bg-muted data-[active=true]:hover:bg-muted data-[active=true]:bg-muted/50 focus-visible:ring-ring/50 hover:bg-muted focus:bg-muted flex items-center gap-1.5 rounded-2xl p-3 text-sm transition-all outline-none focus-visible:ring-[3px] focus-visible:outline-1 in-data-[slot=navigation-menu-content]:rounded-xl [&_svg:not([class*='size-'])]:size-4", className),
		"data-slot": "navigation-menu-link",
		...restProps,
	});
</script>

{#if child}
	{@render child({ props: linkProps })}
{:else}
	<NavigationMenuPrimitive.Link
		bind:ref
		{...linkProps}
	>
		{@render children?.()}
	</NavigationMenuPrimitive.Link>
{/if}
