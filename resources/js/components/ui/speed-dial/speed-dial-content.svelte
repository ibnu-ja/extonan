<script lang="ts">
	import type { Snippet } from "svelte";
	import { Popover as PopoverPrimitive } from "bits-ui";
	import { cn } from "@/lib/utils.js";

	let {
		ref = $bindable(null),
		class: className,
		side = "top" as PopoverPrimitive.ContentProps["side"],
		align = "end" as PopoverPrimitive.ContentProps["align"],
		sideOffset = 8,
		children,
		...restProps
	}: PopoverPrimitive.ContentProps & {
		children?: Snippet;
	} = $props();
</script>

<PopoverPrimitive.Portal>
	<PopoverPrimitive.Content
		bind:ref
		data-slot="speed-dial-content"
		{side}
		{align}
		{sideOffset}
		class={cn(
			"group z-50 flex flex-col-reverse items-end gap-2 outline-hidden rounded-xl p-1 duration-100",
			"data-open:animate-in data-closed:animate-out data-closed:fade-out-0 data-open:fade-in-0 data-closed:zoom-out-95 data-open:zoom-in-95",
			"data-[side=top]:slide-in-from-bottom-2 origin-(--transform-origin)",
			className,
		)}
		{...restProps}
	>
		{@render children?.()}
	</PopoverPrimitive.Content>
</PopoverPrimitive.Portal>
