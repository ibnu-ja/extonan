<script lang="ts">
	import { Select as SelectPrimitive } from "bits-ui";
	import { cn, type WithoutChildrenOrChild } from "@/lib/utils.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		...restProps
	}: WithoutChildrenOrChild<SelectPrimitive.ScrollUpButtonProps> = $props();
</script>

<SelectPrimitive.ScrollUpButton
	bind:ref
	data-slot="select-scroll-up-button"
	class={cn("cn-select-scroll-up-button bg-popover z-10 flex cursor-default items-center justify-center py-1 [&_svg:not([class*='size-'])]:size-4 top-0 w-full", className)}
	{...restProps}
>
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-up")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-up")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowUp01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretUpIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-up-s-line")}
	/>
</SelectPrimitive.ScrollUpButton>
