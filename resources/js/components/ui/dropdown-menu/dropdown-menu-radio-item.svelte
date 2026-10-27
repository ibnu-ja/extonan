<script lang="ts">
	import { DropdownMenu as DropdownMenuPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn, type WithoutChild } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		children: childrenProp,
		...restProps
	}: WithoutChild<DropdownMenuPrimitive.RadioItemProps> = $props();
</script>

<DropdownMenuPrimitive.RadioItem
	bind:ref
	data-slot="dropdown-menu-radio-item"
	class={cn(
		"cn-dropdown-menu-radio-item relative flex cursor-default items-center outline-hidden select-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0",
		className
	)}
	{...restProps}
>
	{#snippet children({ checked })}
		<span
			class="cn-dropdown-menu-item-indicator pointer-events-none"
			data-slot="dropdown-menu-radio-item-indicator"
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
</DropdownMenuPrimitive.RadioItem>
