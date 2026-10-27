<script lang="ts">
	import { Menubar as MenubarPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn, type WithoutChild } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		children: childrenProp,
		...restProps
	}: WithoutChild<MenubarPrimitive.RadioItemProps> = $props();
</script>

<MenubarPrimitive.RadioItem
	bind:ref
	data-slot="menubar-radio-item"
	class={cn(
		"cn-menubar-radio-item relative flex cursor-default select-none items-center outline-hidden data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
		className
	)}
	{...restProps}
>
	{#snippet children({ checked })}
		<span
			class="cn-menubar-radio-item-indicator flex size-3.5 items-center justify-center pointer-events-none"
			data-slot="menubar-radio-item-indicator"
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
</MenubarPrimitive.RadioItem>
