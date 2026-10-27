<script lang="ts">
	import { cn, type WithElementRef } from "@/lib/utils.js";
	import type { HTMLAttributes } from "svelte/elements";

	let {
		ref = $bindable(null),
		class: className,
		children,
		...restProps
	}: WithElementRef<HTMLAttributes<HTMLTableRowElement>> = $props();
</script>

<tr
	bind:this={ref}
	data-slot="table-row"
	class={cn("cn-table-row has-aria-expanded:bg-muted/50 data-[state=selected]:bg-accent/40 data-[state=selected]:text-accent-foreground", className)}
	{...restProps}
>
	{@render children?.()}
</tr>
