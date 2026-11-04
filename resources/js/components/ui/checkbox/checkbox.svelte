<script lang="ts">
	import { Checkbox as CheckboxPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";
	import { cn, type WithoutChildrenOrChild } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		checked = $bindable(false),
		indeterminate = $bindable(false),
		class: className,
		...restProps
	}: WithoutChildrenOrChild<CheckboxPrimitive.RootProps> = $props();
</script>

<CheckboxPrimitive.Root
	bind:ref
	data-slot="checkbox"
	class={cn(
		"cn-checkbox peer relative shrink-0 outline-none after:absolute after:-inset-x-3 after:-inset-y-2 disabled:cursor-not-allowed disabled:opacity-50",
		className
	)}
	bind:checked
	bind:indeterminate
	{...restProps}
>
	{#snippet children({ checked, indeterminate })}
		<div
			data-slot="checkbox-indicator"
			class="cn-checkbox-indicator grid place-content-center text-current transition-none"
		>
			{#if checked}
				<Icon
					lucide={() => import("lucide-svelte/icons/check")}
					tabler={() => import("@tabler/icons-svelte/icons/check")}
					hugeicons={() => import("@hugeicons/core-free-icons/Tick02Icon")}
					phosphor={() => import("phosphor-svelte/lib/CheckIcon")}
					remixicon={() => import("remixicon-svelte/icons/check-line")}
				/>
			{:else if indeterminate}
				<Icon
					lucide={() => import("lucide-svelte/icons/minus")}
					tabler={() => import("@tabler/icons-svelte/icons/minus")}
					hugeicons={() => import("@hugeicons/core-free-icons/MinusSignIcon")}
					phosphor={() => import("phosphor-svelte/lib/MinusIcon")}
					remixicon={() => import("remixicon-svelte/icons/subtract-line")}
				/>
			{/if}
		</div>
	{/snippet}
</CheckboxPrimitive.Root>
