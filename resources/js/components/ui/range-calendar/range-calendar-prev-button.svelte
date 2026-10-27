<script lang="ts">
	import { RangeCalendar as RangeCalendarPrimitive } from "bits-ui";
	import { buttonVariants, type ButtonVariant } from "@/components/ui/button/index.js";
	import { Icon } from "@/components/icon/index.js";
	import { cn } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		variant = "ghost",
		...restProps
	}: RangeCalendarPrimitive.PrevButtonProps & {
		variant?: ButtonVariant;
	} = $props();
</script>

{#snippet Fallback()}
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-left")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-left")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowLeft01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretLeftIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-left-s-line")}
		class={cn("cn-rtl-flip size-4", className)}
	/>
{/snippet}

<RangeCalendarPrimitive.PrevButton
	bind:ref
	class={cn(
		buttonVariants({ variant }),
		"size-(--cell-size) bg-transparent p-0 select-none disabled:opacity-50 rtl:rotate-180",
		className
	)}
	{...restProps}
>
	{#if children}
		{@render children?.()}
	{:else}
		{@render Fallback()}
	{/if}
</RangeCalendarPrimitive.PrevButton>
