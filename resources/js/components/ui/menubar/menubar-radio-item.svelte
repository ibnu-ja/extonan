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
					lucide={() => import("lucide-svelte/icons/check")}
					tabler={() => import("@tabler/icons-svelte/icons/check")}
					hugeicons={() => import("@hugeicons/core-free-icons/Tick02Icon")}
					phosphor={() => import("phosphor-svelte/lib/CheckIcon")}
					remixicon={() => import("remixicon-svelte/icons/check-line")}
				/>
			{/if}
		</span>
		{@render childrenProp?.({ checked })}
	{/snippet}
</MenubarPrimitive.RadioItem>
