<script lang="ts">
	import { Command as CommandPrimitive } from "bits-ui";
	import { cn } from "@/lib/utils.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: CommandPrimitive.ItemProps = $props();
</script>

<CommandPrimitive.Item
	bind:ref
	data-slot="command-item"
	class={cn(
		"cn-command-item group/command-item relative flex cursor-default items-center outline-hidden select-none data-selected:bg-accent data-selected:text-accent-foreground data-selected:*:[svg]:text-accent-foreground data-[disabled=true]:pointer-events-none data-[disabled=true]:opacity-50 [&_svg]:pointer-events-none [&_svg]:shrink-0",
		className
	)}
	{...restProps}
>
	{@render children?.()}
	<Icon
		lucide={() => import("lucide-svelte/icons/check")}
		tabler={() => import("@tabler/icons-svelte/icons/check")}
		hugeicons={() => import("@hugeicons/core-free-icons/Tick02Icon")}
		phosphor={() => import("phosphor-svelte/lib/CheckIcon")}
		remixicon={() => import("remixicon-svelte/icons/check-line")}
		class="cn-command-item-indicator ml-auto opacity-0 group-has-data-[slot=command-shortcut]/command-item:hidden group-data-[checked=true]/command-item:opacity-100"
	/>
</CommandPrimitive.Item>
