<script lang="ts">
	import { ContextMenu as ContextMenuPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		inset,
		children,
		...restProps
	}: ContextMenuPrimitive.SubTriggerProps & {
		inset?: boolean;
	} = $props();
</script>

<ContextMenuPrimitive.SubTrigger
	bind:ref
	data-slot="context-menu-sub-trigger"
	data-inset={inset}
	class={cn(
		"cn-context-menu-sub-trigger flex cursor-default items-center outline-hidden select-none data-[inset]:pl-8 [&_svg]:pointer-events-none [&_svg]:shrink-0",
		className
	)}
	{...restProps}
>
	{@render children?.()}
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-right")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-right")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowRight01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretRightIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-right-s-line")}
		class="cn-rtl-flip ml-auto"
	/>
</ContextMenuPrimitive.SubTrigger>
