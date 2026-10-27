<script lang="ts">
	import type { HTMLAttributes } from "svelte/elements";
	import { cn, type WithElementRef, type WithoutChildren } from "@/lib/utils.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		...restProps
	}: WithoutChildren<WithElementRef<HTMLAttributes<HTMLSpanElement>>> = $props();
</script>

<span
	bind:this={ref}
	aria-hidden="true"
	data-slot="pagination-ellipsis"
	class={cn("flex size-9 items-center justify-center", className)}
	{...restProps}
>
	<Icon
		lucide="MoreHorizontalIcon"
		tabler="IconDots"
		hugeicons="MoreHorizontalCircle01Icon"
		phosphor="DotsThreeIcon"
		remixicon="RiMoreLine"
	/>
	<span class="sr-only">More pages</span>
</span>
