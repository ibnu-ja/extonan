<script lang="ts">
	import "m3-svelte/etc/layer";
	import type { IconifyIcon } from "@iconify/types";
	import type { InputHTMLAttributes } from "svelte/elements";
	import { Icon, Button } from "m3-svelte";

	type SearchProps = InputHTMLAttributes<HTMLInputElement> & {
		label?: string;
		prependIcon?: IconifyIcon;
		supportingText?: string;
		onPrependIconClick?: () => void;
		children?: any;
	};

	let {
		label = "Search",
		prependIcon,
		supportingText,
		onPrependIconClick,
		placeholder = label,
		class: className,
		children,
		...rest
	}: SearchProps = $props();

	let inputElement = $state<HTMLInputElement>();
	let isFocused = $state(false);
</script>

<div class="search-container">
	<div class="search-bar {className} m3-layer" class:focused={isFocused}>
		{#if prependIcon}
			<div class="icon-wrapper">
				<Icon icon={prependIcon} size={24} />
			</div>
		{/if}

		<input
			bind:this={inputElement}
			type="text"
			class="input-field"
			{placeholder}
			onfocus={() => (isFocused = true)}
			onblur={() => (isFocused = false)}
			{...rest}
		/>

		{#if children}
			{@render children()}
		{:else}
			<div class="icon-slot trailing"></div>
		{/if}
	</div>

	{#if supportingText}
		<div class="supporting-text">{supportingText}</div>
	{/if}
</div>

<style>
	@layer tokens {
		:root {
			--m3-search-container-height: 3.5rem;
			--m3-search-container-shape: var(--m3-shape-full);
			--m3-search-avatar-shape: var(--m3-shape-full);
			--m3-search-icon-size: 1.5rem;
			--m3-search-leading-space: 1rem;
			--m3-search-trailing-space: 1rem;
			--m3-search-icon-label-gap: 1rem;
			--m3-search-bar-motion-spring: cubic-bezier(0.17, 0.67, 0.83, 0.67);
		}
	}

	:where(.search-bar.m3-layer)::before {
		pointer-events: none;
	}

	.search-container {
		display: flex;
		flex-direction: column;
		gap: 0.375rem;
		width: 100%;
	}

	.search-bar {
		display: flex;
		align-items: center;
		gap: var(--m3-search-icon-label-gap);
		height: var(--m3-search-container-height);
		padding-left: var(--m3-search-leading-space);
		padding-right: var(--m3-search-trailing-space);
		background-color: var(--m3c-surface-container-high);
		border-radius: var(--m3-search-container-shape);
		border: 2px solid transparent;
		transition:
			background-color var(--m3-easing-fast),
			border-color var(--m3-easing-fast),
			box-shadow var(--m3-easing-fast);
		position: relative;
	}

	.layer {
		position: absolute;
		inset: 0;
		border-radius: inherit;
		pointer-events: none;
	}

	.search-bar:hover {
		background-color: color-mix(
			in srgb,
			var(--m3c-surface-container-high),
			var(--m3c-on-surface) 8%
		);
	}

	.search-bar.focused {
		border-color: var(--m3c-secondary);
		box-shadow: inset 0 0 0 3px rgba(99, 92, 113, 0.12);
	}

	.icon-slot {
		width: var(--m3-search-icon-size);
		height: var(--m3-search-icon-size);
		flex-shrink: 0;
	}

	.icon-wrapper {
		display: flex;
		align-items: center;
		justify-content: center;
		width: var(--m3-search-icon-size);
		height: var(--m3-search-icon-size);
		flex-shrink: 0;
		color: var(--m3c-on-surface-variant);
	}

	.input-field:focus {
		outline: none !important;
		border: none !important;
		box-shadow: none !important;
		-webkit-appearance: none;
		-moz-appearance: none;
		-webkit-focus-ring-color: transparent;
	}

	.input-field:focus-visible {
		outline: none !important;
		border: none !important;
		box-shadow: none !important;
		-webkit-appearance: none;
		-moz-appearance: none;
		-webkit-focus-ring-color: transparent;
	}

	.input-field {
		all: revert;
		flex: 1;
		height: 100%;
		border: none;
		border-radius: 0;
		background: transparent;
		font-family: var(--m3-font);
		font-size: 1rem;
		font-weight: 400;
		line-height: 1.5rem;
		letter-spacing: 0.5px;
		color: var(--m3c-on-surface);
		outline: none;
		padding: 0;
		pointer-events: auto;
		cursor: text;
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
	}

	.input-field::placeholder {
		color: var(--m3c-on-surface-variant);
	}

	.supporting-text {
		font-family: var(--m3-font);
		font-size: 0.75rem;
		font-weight: 400;
		line-height: 1.25rem;
		letter-spacing: 0.4px;
		color: var(--m3c-on-surface-variant);
		padding-left: var(--m3-search-leading-space);
		padding-right: var(--m3-search-trailing-space);
	}

	@media (prefers-color-scheme: dark) {
		.search-bar.focused {
			box-shadow: inset 0 0 0 3px rgba(204, 194, 219, 0.12);
		}
	}
</style>
