<script lang="ts">
	import type { WithoutChildren } from "bits-ui";
	import { getEmblaContext } from "./context.js";
	import { cn } from "@/lib/utils.js";
	import { Button, type Props } from "@/components/ui/button/index.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		variant = "outline",
		size = "icon-sm",
		...restProps
	}: WithoutChildren<Props> = $props();

	const emblaCtx = getEmblaContext("<Carousel.Next/>");
</script>

<Button
	data-slot="carousel-next"
	{variant}
	{size}
	aria-disabled={!emblaCtx.canScrollNext}
	disabled={!emblaCtx.canScrollNext}
	class={cn(
		"cn-carousel-next absolute touch-manipulation",
		emblaCtx.orientation === "horizontal"
			? "inset-y-0 -end-12 my-auto"
			: "start-1/2 -bottom-12 -translate-x-1/2 rotate-90",
		className
	)}
	onclick={emblaCtx.scrollNext}
	onkeydown={emblaCtx.handleKeyDown}
	bind:ref
	{...restProps}
>
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-right")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-right")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowRight01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretRightIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-right-s-line")}
		class="cn-rtl-flip"
	/>
	<span class="sr-only">Next slide</span>
</Button>
