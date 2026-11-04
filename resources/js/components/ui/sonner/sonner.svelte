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
			lucide={() => import("lucide-svelte/icons/loader-circle")}
			tabler={() => import("@tabler/icons-svelte/icons/loader")}
			hugeicons={() => import("@hugeicons/core-free-icons/Loading03Icon")}
			phosphor={() => import("phosphor-svelte/lib/SpinnerIcon")}
			remixicon={() => import("remixicon-svelte/icons/loader-line")}
			class="size-4 animate-spin"
		/>
	{/snippet}
	{#snippet successIcon()}
		<Icon
			lucide={() => import("lucide-svelte/icons/circle-check")}
			tabler={() => import("@tabler/icons-svelte/icons/circle-check")}
			hugeicons={() => import("@hugeicons/core-free-icons/CheckmarkCircle02Icon")}
			phosphor={() => import("phosphor-svelte/lib/CheckCircleIcon")}
			remixicon={() => import("remixicon-svelte/icons/checkbox-circle-line")}
			class="size-4"
		/>
	{/snippet}
	{#snippet errorIcon()}
		<Icon
			lucide={() => import("lucide-svelte/icons/octagon-x")}
			tabler={() => import("@tabler/icons-svelte/icons/alert-octagon")}
			hugeicons={() => import("@hugeicons/core-free-icons/MultiplicationSignCircleIcon")}
			phosphor={() => import("phosphor-svelte/lib/XCircleIcon")}
			remixicon={() => import("remixicon-svelte/icons/error-warning-line")}
			class="size-4"
		/>
	{/snippet}
	{#snippet infoIcon()}
		<Icon
			lucide={() => import("lucide-svelte/icons/info")}
			tabler={() => import("@tabler/icons-svelte/icons/info-circle")}
			hugeicons={() => import("@hugeicons/core-free-icons/InformationCircleIcon")}
			phosphor={() => import("phosphor-svelte/lib/InfoIcon")}
			remixicon={() => import("remixicon-svelte/icons/information-line")}
			class="size-4"
		/>
	{/snippet}
	{#snippet warningIcon()}
		<Icon
			lucide={() => import("lucide-svelte/icons/triangle-alert")}
			tabler={() => import("@tabler/icons-svelte/icons/alert-triangle")}
			hugeicons={() => import("@hugeicons/core-free-icons/Alert02Icon")}
			phosphor={() => import("phosphor-svelte/lib/WarningIcon")}
			remixicon={() => import("remixicon-svelte/icons/close-circle-line")}
			class="size-4"
		/>
	{/snippet}
</Sonner>
