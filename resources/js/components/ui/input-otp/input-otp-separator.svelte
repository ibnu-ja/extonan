<script lang="ts">
	import type { HTMLAttributes } from "svelte/elements";
	import type { WithElementRef } from "@/lib/utils.js";
	import { cn } from "@/lib/utils.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: WithElementRef<HTMLAttributes<HTMLDivElement>> = $props();
</script>

<div
	bind:this={ref}
	data-slot="input-otp-separator"
	role="separator"
	class={cn("[&_svg:not([class*='size-'])]:size-4 flex items-center", className)}
	{...restProps}
>
	{#if children}
		{@render children?.()}
	{:else}
		<Icon
			lucide="MinusIcon"
			tabler="IconMinus"
			hugeicons="MinusSignIcon"
			phosphor="MinusIcon"
			remixicon="RiSubtractLine"
		/>
	{/if}
</div>
