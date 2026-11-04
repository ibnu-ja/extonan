<script lang="ts">
	import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";
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
	}: WithoutChildrenOrChild<DropdownMenuPrimitive.CheckboxItemProps> & {
		children?: Snippet;
	} = $props();
</script>

<DropdownMenuPrimitive.CheckboxItem
	bind:ref
	bind:checked
	bind:indeterminate
	data-slot="dropdown-menu-checkbox-item"
	class={cn(
		"cn-dropdown-menu-checkbox-item relative flex cursor-default items-center outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0",
		className
	)}
	{...restProps}
>
	{#snippet children({ checked, indeterminate })}
		<span
			class="cn-dropdown-menu-item-indicator pointer-events-none"
			data-slot="dropdown-menu-checkbox-item-indicator"
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
</DropdownMenuPrimitive.CheckboxItem>
