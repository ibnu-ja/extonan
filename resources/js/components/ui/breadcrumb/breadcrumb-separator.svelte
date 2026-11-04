<script lang="ts">
	import { cn, type WithElementRef } from "@/lib/utils.js";
	import type { HTMLLiAttributes } from "svelte/elements";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: WithElementRef<HTMLLiAttributes> = $props();
</script>

<li
	bind:this={ref}
	data-slot="breadcrumb-separator"
	role="presentation"
	aria-hidden="true"
	class={cn("cn-breadcrumb-separator [&>svg]:size-3.5", className)}
	{...restProps}
>
	{#if children}
		{@render children?.()}
	{:else}
		<Icon
			lucide={() => import("lucide-svelte/icons/chevron-right")}
			tabler={() => import("@tabler/icons-svelte/icons/chevron-right")}
			hugeicons={() => import("@hugeicons/core-free-icons/ArrowRight01Icon")}
			phosphor={() => import("phosphor-svelte/lib/CaretRightIcon")}
			remixicon={() => import("remixicon-svelte/icons/arrow-right-s-line")}
			class="cn-rtl-flip"
		/>
	{/if}
</li>
