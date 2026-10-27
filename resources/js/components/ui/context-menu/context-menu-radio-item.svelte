<script lang="ts">
	import { ContextMenu as ContextMenuPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn, type WithoutChild } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		children: childrenProp,
		...restProps
	}: WithoutChild<ContextMenuPrimitive.RadioItemProps> = $props();
</script>

<ContextMenuPrimitive.RadioItem
	bind:ref
	data-slot="context-menu-radio-item"
	class={cn(
		"focus:bg-accent focus:text-accent-foreground focus:**:text-accent-foreground gap-2.5 rounded-xl py-2 pr-8 pl-3 text-sm data-inset:pl-9.5 [&_svg:not([class*='size-'])]:size-4 relative flex cursor-default items-center outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0",
		className
	)}
	{...restProps}
>
	{#snippet children({ checked })}
		<span
			class="absolute right-2 flex items-center justify-center pointer-events-none"
			data-slot="context-menu-radio-item-indicator"
		>
			{#if checked}
				<Icon
					lucide="CheckIcon"
					tabler="IconCheck"
					hugeicons="Tick02Icon"
					phosphor="CheckIcon"
					remixicon="RiCheckLine"
				/>
			{/if}
		</span>
		{@render childrenProp?.({ checked })}
	{/snippet}
</ContextMenuPrimitive.RadioItem>
