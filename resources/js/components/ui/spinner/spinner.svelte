<script lang="ts">
	import { Icon } from "@/components/icon/index.js";
	import { cn } from "@/lib/utils.js";
	import type { SVGAttributes } from "svelte/elements";

	let {
		class: className,
		role = "status",
		// we add name, color, and stroke for compatibility with different icon libraries props.
		// only forward them when set, otherwise `stroke: undefined` overrides the icon's `currentColor`
		name,
		color,
		stroke,
		className: legacyClassName,
		"aria-label": ariaLabel = "Loading",
		...restProps
	}: SVGAttributes<SVGSVGElement> = $props();
</script>

<Icon
	lucide={() => import("lucide-svelte/icons/loader-circle")}
	tabler={() => import("@tabler/icons-svelte/icons/loader")}
	hugeicons={() => import("@hugeicons/core-free-icons/Loading03Icon")}
	phosphor={() => import("phosphor-svelte/lib/SpinnerIcon")}
	remixicon={() => import("remixicon-svelte/icons/loader-line")}
	{role}
	{...name != null ? { name } : {}}
	{...color != null ? { color } : {}}
	{...stroke != null ? { stroke } : {}}
	{...legacyClassName != null ? { className: legacyClassName } : {}}
	aria-label={ariaLabel}
	class={cn("size-4 animate-spin", className)}
	{...restProps}
/>
