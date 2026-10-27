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
		lucide="ChevronRightIcon"
		tabler="IconChevronRight"
		hugeicons="ArrowRight01Icon"
		phosphor="CaretRightIcon"
		remixicon="RiArrowRightSLine"
		data-icon="inline-end"
		class="cn-rtl-flip"
	/>
</PaginationPrimitive.NextButton>
