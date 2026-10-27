<script lang="ts">
	import { Accordion as AccordionPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn, type WithoutChild } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		level = 3,
		children,
		...restProps
	}: WithoutChild<AccordionPrimitive.TriggerProps> & {
		level?: AccordionPrimitive.HeaderProps["level"];
	} = $props();
</script>

<AccordionPrimitive.Header {level} class="flex">
	<AccordionPrimitive.Trigger
		data-slot="accordion-trigger"
		bind:ref
		class={cn(
			"cn-accordion-trigger group/accordion-trigger relative flex flex-1 items-start justify-between border border-transparent transition-all outline-none disabled:pointer-events-none disabled:opacity-50",
			className
		)}
		{...restProps}
	>
		{@render children?.()}
		<Icon
			lucide={() => import("lucide-svelte/icons/chevron-down")}
			tabler={() => import("@tabler/icons-svelte/icons/chevron-down")}
			data-slot="accordion-trigger-icon"
			hugeicons={() => import("@hugeicons/core-free-icons/ArrowDown01Icon")}
			phosphor={() => import("phosphor-svelte/lib/CaretDownIcon")}
			remixicon={() => import("remixicon-svelte/icons/arrow-down-s-line")}
			class="cn-accordion-trigger-icon pointer-events-none shrink-0 group-aria-expanded/accordion-trigger:hidden"
		/>
		<Icon
			lucide={() => import("lucide-svelte/icons/chevron-up")}
			tabler={() => import("@tabler/icons-svelte/icons/chevron-up")}
			data-slot="accordion-trigger-icon"
			hugeicons={() => import("@hugeicons/core-free-icons/ArrowUp01Icon")}
			phosphor={() => import("phosphor-svelte/lib/CaretUpIcon")}
			remixicon={() => import("remixicon-svelte/icons/arrow-up-s-line")}
			class="cn-accordion-trigger-icon pointer-events-none hidden shrink-0 group-aria-expanded/accordion-trigger:inline"
		/>
	</AccordionPrimitive.Trigger>
</AccordionPrimitive.Header>
