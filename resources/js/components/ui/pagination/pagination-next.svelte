<script lang="ts">
	import { Pagination as PaginationPrimitive } from "bits-ui";
	import { cn } from "@/lib/utils.js";
	import { buttonVariants } from "@/components/ui/button/index.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		...restProps
	}: PaginationPrimitive.NextButtonProps = $props();
</script>

<PaginationPrimitive.NextButton
	bind:ref
	aria-label="Go to next page"
	class={cn(
		buttonVariants({ variant: "ghost", size: "default" }),
		"pr-2!",
		className
	)}
	{...restProps}
>
	<span class="cn-pagination-next-text hidden sm:block">Next</span>
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-right")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-right")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowRight01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretRightIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-right-s-line")}
		data-icon="inline-end"
		class="cn-rtl-flip"
	/>
</PaginationPrimitive.NextButton>
