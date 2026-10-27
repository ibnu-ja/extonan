<script lang="ts" module>
	import { cn, type WithElementRef } from "@/lib/utils.js";
	import type { HTMLAnchorAttributes, HTMLButtonAttributes } from "svelte/elements";
	import type { Snippet } from "svelte";
	import { type VariantProps, tv } from "tailwind-variants";

	export const buttonVariants = tv({
		base: "cn-button group/button inline-flex shrink-0 items-center justify-center whitespace-nowrap transition-all outline-none select-none disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0",
		variants: {
			variant: {
				default: "cn-button-variant-default",
				outline: "cn-button-variant-outline",
				secondary: "cn-button-variant-secondary",
				ghost: "cn-button-variant-ghost",
				destructive: "cn-button-variant-destructive",
				link: "cn-button-variant-link",
			},
			size: {
				default: "cn-button-size-default",
				xs: "cn-button-size-xs",
				sm: "cn-button-size-sm",
				lg: "cn-button-size-lg",
				icon: "cn-button-size-icon",
				"icon-xs": "cn-button-size-icon-xs",
				"icon-sm": "cn-button-size-icon-sm",
				"icon-lg": "cn-button-size-icon-lg",
			},
		},
		defaultVariants: {
			variant: "default",
			size: "default",
		},
	});

	export type ButtonVariant = VariantProps<typeof buttonVariants>["variant"];
	export type ButtonSize = VariantProps<typeof buttonVariants>["size"];

	export type ButtonProps = WithElementRef<HTMLButtonAttributes> &
		WithElementRef<HTMLAnchorAttributes> & {
			variant?: ButtonVariant;
			size?: ButtonSize;
			child?: Snippet<[{ props: Record<string, unknown> }]>;
		};
</script>

<script lang="ts">
	let {
		class: className,
		variant = "default",
		size = "default",
		ref = $bindable(null),
		href = undefined,
		type = "button",
		disabled,
		children,
		child,
		...restProps
	}: ButtonProps = $props();

	const buttonProps = $derived({
		class: cn(buttonVariants({ variant, size }), className),
		"data-slot": "button",
		...restProps,
	});
</script>

{#if child}
	{@render child({ props: buttonProps })}
{:else if href}
	<a
		bind:this={ref}
		{...buttonProps}
		href={disabled ? undefined : href}
		aria-disabled={disabled}
		role={disabled ? "link" : undefined}
		tabindex={disabled ? -1 : undefined}
	>
		{@render children?.()}
	</a>
{:else}
	<button
		bind:this={ref}
		{...buttonProps}
		{type}
		{disabled}
	>
		{@render children?.()}
	</button>
{/if}
