<script lang="ts">
	import type { Snippet } from "svelte";
	import type { SVGAttributes } from "svelte/elements";
	import { lucideIconLoader } from "./icon-loader.js";

	interface Props extends SVGAttributes<SVGSVGElement> {
		name: string;
		placeholder?: Snippet;
		class?: string;
	}

	let { name, placeholder, class: className, ...restProps }: Props = $props();

	// svelte-ignore state_referenced_locally
	let IconComponent = $state<any>(lucideIconLoader.peek(name));

	$effect(() => {
		const current = lucideIconLoader.peek(name);

		if (current !== undefined) {
			IconComponent = current;
		} else {
			lucideIconLoader.load(name).then((res) => {
				IconComponent = res;
			});
		}
	});
</script>

{#if IconComponent}
	<IconComponent class={className} {...restProps} />
{:else if placeholder}
	{@render placeholder()}
{:else}
	<svg class={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {...restProps}></svg>
{/if}
