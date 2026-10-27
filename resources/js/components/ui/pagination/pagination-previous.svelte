<script lang="ts">
	import { Pagination as PaginationPrimitive } from "bits-ui";
	import { cn } from "@/lib/utils.js";
	import { buttonVariants } from "@/components/ui/button/index.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		...restProps
	}: PaginationPrimitive.PrevButtonProps = $props();
</script>

<PaginationPrimitive.PrevButton
	bind:ref
	aria-label="Go to previous page"
	class={cn(
		buttonVariants({ variant: "ghost", size: "default" }),
		"pl-2!",
		className
	)}
	{...restProps}
>
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-left")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-left")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowLeft01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretLeftIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-left-s-line")}
		data-icon="inline-start"
		class="cn-rtl-flip"
	/>
	<span class="cn-pagination-prev-text hidden sm:block">Previous</span>
</PaginationPrimitive.PrevButton>
