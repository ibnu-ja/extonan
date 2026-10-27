<script lang="ts">
	import { Dialog as DialogPrimitive } from "bits-ui";
	import DialogPortal from "./dialog-portal.svelte";
	import type { Snippet } from "svelte";
	import * as Dialog from "./index.js";
	import { cn, type WithoutChildrenOrChild } from "@/lib/utils.js";
	import type { ComponentProps } from "svelte";
	import { Button } from "@/components/ui/button/index.js";
	import { Icon } from "@/components/icon/index.js";

	let {
		ref = $bindable(null),
		class: className,
		portalProps,
		children,
		showCloseButton = true,
		...restProps
	}: WithoutChildrenOrChild<DialogPrimitive.ContentProps> & {
		portalProps?: WithoutChildrenOrChild<ComponentProps<typeof DialogPortal>>;
		children: Snippet;
		showCloseButton?: boolean;
	} = $props();
</script>

<DialogPortal {...portalProps}>
	<Dialog.Overlay />
	<DialogPrimitive.Content
		bind:ref
		data-slot="dialog-content"
		class={cn(
			"cn-dialog-content fixed top-1/2 left-1/2 z-50 w-full -translate-x-1/2 -translate-y-1/2 outline-none",
			className
		)}
		{...restProps}
	>
		{@render children?.()}
		{#if showCloseButton}
			<DialogPrimitive.Close data-slot="dialog-close">
				{#snippet child({ props })}
					<Button variant="ghost" class="cn-dialog-close" size="icon-sm" {...props}>
						<Icon
							lucide={() => import("lucide-svelte/icons/x")}
							tabler={() => import("@tabler/icons-svelte/icons/x")}
							hugeicons={() => import("@hugeicons/core-free-icons/Cancel01Icon")}
							phosphor={() => import("phosphor-svelte/lib/XIcon")}
							remixicon={() => import("remixicon-svelte/icons/close-line")}
						/>
						<span class="sr-only">Close</span>
					</Button>
				{/snippet}
			</DialogPrimitive.Close>
		{/if}
	</DialogPrimitive.Content>
</DialogPortal>
