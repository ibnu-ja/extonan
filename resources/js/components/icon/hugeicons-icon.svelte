<script lang="ts">
	import { HugeiconsIcon } from "@hugeicons/svelte";
	import type { ComponentProps, Snippet } from "svelte";
	import { hugeiconsIconLoader } from "./icon-loader.js";

	type BaseProps = Omit<ComponentProps<typeof HugeiconsIcon>, "icon">;

	interface Props extends BaseProps {
		name: string;
		placeholder?: Snippet;
		class?: string;
	}

	let { name, placeholder, class: className, ...restProps }: Props = $props();

	// svelte-ignore state_referenced_locally
	let iconData = $state<any>(hugeiconsIconLoader.peek(name));

	$effect(() => {
		const current = hugeiconsIconLoader.peek(name);

		if (current !== undefined) {
			iconData = current;
		} else {
			hugeiconsIconLoader.load(name).then((res) => {
				iconData = res;
			});
		}
	});
</script>

{#if iconData}
	<HugeiconsIcon icon={iconData} strokeWidth={2} class={className} {...restProps} />
{:else if placeholder}
	{@render placeholder()}
{:else}
	<svg class={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {...restProps}></svg>
{/if}
