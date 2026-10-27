<script lang="ts">
	import { Pagination as PaginationPrimitive } from "bits-ui";
	import { cn } from "@/lib/utils.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: PaginationPrimitive.NextButtonProps = $props();
</script>

<PaginationPrimitive.NextButton
	bind:ref
	aria-label="Go to next page"
	class={cn("flex size-9 items-center justify-center rounded-md border", className)}
	{...restProps}
>
	{#if children}
		{@render children?.()}
	{:else}
		<Icon
		lucide="ChevronRightIcon"
		tabler="IconChevronRight"
		hugeicons="ArrowRightIcon"
		phosphor="CaretRightIcon"
		remixicon="RiArrowRightSLine"
		class={cn("cn-rtl-flip size-4", className)}
	/>
	{/if}
</PaginationPrimitive.NextButton>
