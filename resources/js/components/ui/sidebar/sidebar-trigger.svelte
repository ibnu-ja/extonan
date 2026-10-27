<script lang="ts">
	import { Button } from "@/components/ui/button/index.js";
	import { Icon } from "@/components/icon/index.js";
	import { cn } from "@/lib/utils.js";
	import type { ComponentProps } from "svelte";
	import { useSidebar } from "./context.svelte.js";

	let {
		ref = $bindable(null),
		class: className,
		onclick,
		...restProps
	}: ComponentProps<typeof Button> & {
		onclick?: (e: MouseEvent) => void;
	} = $props();

	const sidebar = useSidebar();
</script>

<Button
	bind:ref
	data-sidebar="trigger"
	data-slot="sidebar-trigger"
	variant="ghost"
	size="icon-sm"
	class={cn("cn-sidebar-trigger", className)}
	type="button"
	onclick={(e) => {
		onclick?.(e);
		sidebar.toggle();
	}}
	{...restProps}
>
	<Icon
		lucide={() => import("lucide-svelte/icons/panel-left")}
		tabler={() => import("@tabler/icons-svelte/icons/layout-sidebar")}
		hugeicons={() => import("@hugeicons/core-free-icons/SidebarLeftIcon")}
		phosphor={() => import("phosphor-svelte/lib/SidebarIcon")}
		remixicon={() => import("remixicon-svelte/icons/side-bar-line")}
		class="size-4 cn-rtl-flip"
	/>
	<span class="sr-only">Toggle Sidebar</span>
</Button>
