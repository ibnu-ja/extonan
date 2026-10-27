<script lang="ts" module>
	import type { Snippet } from "svelte";
	import { type VariantProps, tv } from "tailwind-variants";

	export const badgeVariants = tv({
		base: "cn-badge group/badge inline-flex w-fit shrink-0 items-center justify-center overflow-hidden whitespace-nowrap focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 [&>svg]:pointer-events-none",
		variants: {
			variant: {
				default: "cn-badge-variant-default",
				secondary: "cn-badge-variant-secondary",
				destructive: "cn-badge-variant-destructive",
				outline: "cn-badge-variant-outline",
				ghost: "cn-badge-variant-ghost",
				link: "cn-badge-variant-link",
			},
		},
		defaultVariants: {
			variant: "default",
		},
	});

	export type BadgeVariant = VariantProps<typeof badgeVariants>["variant"];
</script>

<script lang="ts">
	import type { HTMLAnchorAttributes } from "svelte/elements";
	import { cn, type WithElementRef } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		href,
		class: className,
		variant = "default",
		children,
		child,
		...restProps
	}: WithElementRef<HTMLAnchorAttributes> & {
		variant?: BadgeVariant;
		child?: Snippet<[{ props: Record<string, unknown> }]>;
	} = $props();

	const badgeProps = $derived({
		class: cn(badgeVariants({ variant }), className),
		"data-slot": "badge",
		...restProps,
	});
</script>

{#if child}
	{@render child({ props: badgeProps })}
{:else}
	<svelte:element
		this={href ? "a" : "span"}
		bind:this={ref}
		{...badgeProps}
		{href}
	>
		{@render children?.()}
	</svelte:element>
{/if}
