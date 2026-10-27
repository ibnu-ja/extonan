<script lang="ts">
	import type { Snippet } from "svelte";
	import { fade } from "svelte/transition";
	import { Popover as PopoverPrimitive } from "bits-ui";
	import Fab from "../fab/fab.svelte";
	import { Icon } from "@/components/icon/index.js";

	function rotate(_node: Element, { duration = 200 } = {}) {
		return {
			duration,
			css: (t: number) => `transform: rotate(${(1 - t) * 90}deg);`,
		};
	}

	let {
		children,
		icon,
		iconClose,
		labelClose,
		...restProps
	}: {
		children?: Snippet;
		icon?: Snippet;
		iconClose?: Snippet;
		labelClose?: Snippet;
	} = $props();
</script>

<PopoverPrimitive.Trigger {...restProps}>
	{#snippet child({ props })}
		<Fab
			{...props}
			data-slot="speed-dial-trigger"
			class="data-[state=open]:bg-destructive data-[state=open]:text-destructive-foreground"
		>
			<div class="flex items-center gap-2 transition-colors duration-200">
				<span class="inline-flex items-center">
					{#if props["data-state"] === "open"}
						{#if iconClose}
							{@render iconClose()}
						{:else}
							<span in:rotate={{ duration: 200 }}>
								<Icon
									lucide="XIcon"
									tabler="IconX"
									hugeicons="Cancel01Icon"
									phosphor="XIcon"
									remixicon="RiCloseLine"
									class="size-4"
								/>
							</span>
						{/if}
					{:else}
						{#if icon}
							{@render icon()}
						{:else}
							<span in:rotate={{ duration: 200 }}>
								<Icon
									lucide="PlusIcon"
									tabler="IconPlus"
									hugeicons="PlusSignIcon"
									phosphor="PlusIcon"
									remixicon="RiAddLine"
									class="size-4"
								/>
							</span>
						{/if}
					{/if}
				</span>
				{#if props["data-state"] === "open"}
					<span in:fade={{ duration: 150 }} class="text-sm">
						{#if labelClose}
							{@render labelClose()}
						{:else}
							Close
						{/if}
					</span>
				{:else if children}
					<span in:fade={{ duration: 150 }} class="text-sm">
						{@render children()}
					</span>
				{/if}
			</div>
		</Fab>
	{/snippet}
</PopoverPrimitive.Trigger>
