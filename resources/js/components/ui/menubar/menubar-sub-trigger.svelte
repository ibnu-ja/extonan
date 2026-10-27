<script lang="ts">
	import { Menubar as MenubarPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		inset,
		children,
		...restProps
	}: MenubarPrimitive.SubTriggerProps & {
		inset?: boolean;
	} = $props();
</script>

<MenubarPrimitive.SubTrigger
	bind:ref
	data-slot="menubar-sub-trigger"
	data-inset={inset}
	class={cn(
		"focus:bg-accent focus:text-accent-foreground data-open:bg-accent data-open:text-accent-foreground gap-2 rounded-xs px-2 py-1 text-xs data-inset:pl-7.5 [&_svg:not([class*='size-'])]:size-3.5 flex cursor-default select-none items-center outline-hidden data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
		className
	)}
	{...restProps}
>
	{@render children?.()}
	<Icon
		lucide="ChevronRightIcon"
		tabler="IconChevronRight"
		hugeicons="ArrowRight01Icon"
		phosphor="CaretRightIcon"
		remixicon="RiArrowRightSLine"
		class="cn-rtl-flip ml-auto size-4"
	/>
</MenubarPrimitive.SubTrigger>
