<script lang="ts">
	import type { Snippet } from "svelte";
	import { cn } from "@/lib/utils.js";
	import Fab from "../fab/fab.svelte";

	let {
		ref = $bindable(null),
		class: className,
		variant = "default" as import("@/components/ui/button").ButtonVariant,
		children,
		...restProps
	}: {
		ref?: HTMLButtonElement | null;
		class?: string;
		variant?: import("@/components/ui/button").ButtonVariant;
		children?: Snippet;
		[key: string]: unknown;
	} = $props();
</script>

<Fab
	data-slot="speed-dial-item"
	{variant}
	class={cn(
		"static justify-start",
		"opacity-0 translate-y-2 transition-all duration-200",
		"group-data-[state=open]:opacity-100 group-data-[state=open]:translate-y-0",
		className,
	)}
	{...restProps}
>
	{@render children?.()}
</Fab>
