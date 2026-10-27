<script lang="ts">
	import { Pagination as PaginationPrimitive } from "bits-ui";
	import { cn } from "@/lib/utils.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: PaginationPrimitive.PrevButtonProps = $props();
</script>

<PaginationPrimitive.PrevButton
	bind:ref
	aria-label="Go to previous page"
	class={cn("flex size-9 items-center justify-center rounded-[var(--radius)] border", className)}
	{...restProps}
>
	{#if children}
		{@render children?.()}
	{:else}
		<Icon
		lucide={() => import("lucide-svelte/icons/chevron-left")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-left")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowLeft01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretLeftIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-left-s-line")}
		class={cn("cn-rtl-flip size-4", className)}
	/>
	{/if}
</PaginationPrimitive.PrevButton>
