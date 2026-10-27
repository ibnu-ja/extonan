<script lang="ts">
	import { Toaster as Sonner, type ToasterProps as SonnerProps } from "svelte-sonner";
	import { mode } from "mode-watcher";
	import { Icon } from "@/components/icon/index.js";

	let { ...restProps }: SonnerProps = $props();
</script>

<Sonner
	theme={mode.current}
	class="toaster group"
	style="--normal-bg: var(--color-popover); --normal-text: var(--color-popover-foreground); --normal-border: var(--color-border);"
	{...restProps}
>
	{#snippet loadingIcon()}
		<Icon
			lucide="Loader2Icon"
			tabler="IconLoader"
			hugeicons="Loading03Icon"
			phosphor="SpinnerIcon"
			remixicon="RiLoaderLine"
			class="size-4 animate-spin"
		/>
	{/snippet}
	{#snippet successIcon()}
		<Icon
			lucide="CircleCheckIcon"
			tabler="IconCircleCheck"
			hugeicons="CheckmarkCircle02Icon"
			phosphor="CheckCircleIcon"
			remixicon="RiCheckboxCircleLine"
			class="size-4"
		/>
	{/snippet}
	{#snippet errorIcon()}
		<Icon
			lucide="OctagonXIcon"
			tabler="IconAlertOctagon"
			hugeicons="MultiplicationSignCircleIcon"
			phosphor="XCircleIcon"
			remixicon="RiErrorWarningLine"
			class="size-4"
		/>
	{/snippet}
	{#snippet infoIcon()}
		<Icon
			lucide="InfoIcon"
			tabler="IconInfoCircle"
			hugeicons="InformationCircleIcon"
			phosphor="InfoIcon"
			remixicon="RiInformationLine"
			class="size-4"
		/>
	{/snippet}
	{#snippet warningIcon()}
		<Icon
			lucide="TriangleAlertIcon"
			tabler="IconAlertTriangle"
			hugeicons="Alert02Icon"
			phosphor="WarningIcon"
			remixicon="RiCloseCircleLine"
			class="size-4"
		/>
	{/snippet}
</Sonner>
