<script lang="ts">
	import type { Snippet } from "svelte";
	import { getDesignSystem } from "@/lib/design-system/index.js";
	import HugeiconsIcon from "./hugeicons-icon.svelte";
	import LucideIcon from "./lucide-icon.svelte";

	interface Props {
		lucide?: string;
		tabler?: string;
		hugeicons?: string;
		phosphor?: string;
		remixicon?: string;
		class?: string;
		placeholder?: Snippet;
		[key: string]: any;
	}

	let {
		lucide,
		tabler: _tabler,
		hugeicons,
		phosphor: _phosphor,
		remixicon: _remixicon,
		class: className,
		placeholder,
		...restProps
	}: Props = $props();

	const designSystem = getDesignSystem();
	const activeLibrary = $derived(designSystem?.iconLibrary ?? "lucide");

	const shouldUseHugeicons = $derived(
		activeLibrary === "hugeicons" && Boolean(hugeicons)
	);
</script>

{#if shouldUseHugeicons && hugeicons}
	<HugeiconsIcon name={hugeicons} class={className} {placeholder} {...restProps} />
{:else if lucide}
	<LucideIcon name={lucide} class={className} {placeholder} {...restProps} />
{:else if hugeicons}
	<HugeiconsIcon name={hugeicons} class={className} {placeholder} {...restProps} />
{:else if placeholder}
	{@render placeholder()}
{/if}
