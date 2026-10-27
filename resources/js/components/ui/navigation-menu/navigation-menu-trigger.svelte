<script lang="ts" module>
	import { cn } from "@/lib/utils.js";
	import { tv } from "tailwind-variants";

	export const navigationMenuTriggerStyle = tv({
		base: "cn-navigation-menu-trigger group/navigation-menu-trigger inline-flex h-9 w-max items-center justify-center outline-none disabled:pointer-events-none",
	});
</script>

<script lang="ts">
	import { NavigationMenu as NavigationMenuPrimitive } from "bits-ui";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: NavigationMenuPrimitive.TriggerProps = $props();
</script>

<NavigationMenuPrimitive.Trigger
	bind:ref
	data-slot="navigation-menu-trigger"
	class={cn(navigationMenuTriggerStyle(), "group", className)}
	{...restProps}
>
	{@render children?.()}
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-down")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-down")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowDown01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretDownIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-down-s-line")}
		class="cn-navigation-menu-trigger-icon"
		aria-hidden="true"
	/>
</NavigationMenuPrimitive.Trigger>
