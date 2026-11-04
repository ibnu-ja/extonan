<script lang="ts">
	import { Menubar as MenubarPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn, type WithoutChildrenOrChild } from "@/lib/utils.js";
	import type { Snippet } from "svelte";

	let {
		ref = $bindable(null),
		checked = $bindable(false),
		indeterminate = $bindable(false),
		class: className,
		children: childrenProp,
		...restProps
	}: WithoutChildrenOrChild<MenubarPrimitive.CheckboxItemProps> & {
		children?: Snippet;
	} = $props();
</script>

<MenubarPrimitive.CheckboxItem
	bind:ref
	bind:checked
	bind:indeterminate
	data-slot="menubar-checkbox-item"
	class={cn(
		"cn-menubar-checkbox-item relative flex cursor-default select-none items-center outline-hidden data-[disabled]:pointer-events-none data-[disabled]:opacity-50",
		className
	)}
	{...restProps}
>
	{#snippet children({ checked, indeterminate })}
		<span
			class="cn-menubar-checkbox-item-indicator flex size-3.5 items-center justify-center pointer-events-none"
			data-slot="menubar-checkbox-item-indicator"
		>
			{#if indeterminate}
				<Icon
					lucide={() => import("lucide-svelte/icons/minus")}
					tabler={() => import("@tabler/icons-svelte/icons/minus")}
					hugeicons={() => import("@hugeicons/core-free-icons/MinusSignIcon")}
					phosphor={() => import("phosphor-svelte/lib/MinusIcon")}
					remixicon={() => import("remixicon-svelte/icons/subtract-line")}
				/>
			{:else if checked}
				<Icon
					lucide={() => import("lucide-svelte/icons/check")}
					tabler={() => import("@tabler/icons-svelte/icons/check")}
					hugeicons={() => import("@hugeicons/core-free-icons/Tick02Icon")}
					phosphor={() => import("phosphor-svelte/lib/CheckIcon")}
					remixicon={() => import("remixicon-svelte/icons/check-line")}
				/>
			{/if}
		</span>
		{@render childrenProp?.()}
	{/snippet}
</MenubarPrimitive.CheckboxItem>
