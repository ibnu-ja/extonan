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

	const emblaCtx = getEmblaContext("<Carousel.Previous/>");
</script>

<Button
	data-slot="carousel-previous"
	{variant}
	{size}
	aria-disabled={!emblaCtx.canScrollPrev}
	disabled={!emblaCtx.canScrollPrev}
	class={cn(
		"cn-carousel-previous absolute touch-manipulation",
		emblaCtx.orientation === "horizontal"
			? "inset-y-0 -start-12 my-auto"
			: "start-1/2 -top-12 -translate-x-1/2 rotate-90",
		className
	)}
	onclick={emblaCtx.scrollPrev}
	onkeydown={emblaCtx.handleKeyDown}
	{...restProps}
	bind:ref
>
	<Icon
		lucide={() => import("lucide-svelte/icons/chevron-left")}
		tabler={() => import("@tabler/icons-svelte/icons/chevron-left")}
		hugeicons={() => import("@hugeicons/core-free-icons/ArrowLeft01Icon")}
		phosphor={() => import("phosphor-svelte/lib/CaretLeftIcon")}
		remixicon={() => import("remixicon-svelte/icons/arrow-left-s-line")}
		class="cn-rtl-flip"
	/>
	<span class="sr-only">Previous slide</span>
</Button>
