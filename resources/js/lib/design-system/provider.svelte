<script lang="ts">
	import { onDestroy  } from "svelte";
import type {Snippet} from "svelte";
	import { buildRegistryTheme } from "./config.js";
	import { syncMenuColor } from "./menu-color.js";
	import { setDesignSystem  } from "./state.svelte.js";
import type {DesignSystemState} from "./state.svelte.js";

	interface Props {
		children: Snippet;
		state?: DesignSystemState;
	}

	let { children, state }: Props = $props();

	// svelte-ignore state_referenced_locally
	const designSystem = setDesignSystem(state);

	const MANAGED_BODY_PREFIXES = ["style-", "base-color-"] as const;

	function removeManagedClasses(body: HTMLElement) {
		for (const className of Array.from(body.classList)) {
			if (MANAGED_BODY_PREFIXES.some((prefix) => className.startsWith(prefix))) {
				body.classList.remove(className);
			}
		}
	}

	let stopMenuSync: (() => void) | null = null;
	const STYLE_ELEMENT_ID = "extonan-design-system-vars";

	$effect(() => {
		if (typeof window === "undefined" || typeof document === "undefined") {
return;
}

		const body = document.body;
		removeManagedClasses(body);
		body.classList.add(`style-${designSystem.style}`);
		body.classList.add(`base-color-${designSystem.baseColor}`);

		// Build theme variables
		const registryTheme = buildRegistryTheme(designSystem.toConfig());

		let styleEl = document.getElementById(STYLE_ELEMENT_ID) as HTMLStyleElement | null;

		if (!styleEl) {
			styleEl = document.createElement("style");
			styleEl.id = STYLE_ELEMENT_ID;
			document.head.appendChild(styleEl);
		}

		const { light, dark, theme } = registryTheme.cssVars;
		let css = ":root {\n";

		if (theme) {
			for (const [k, v] of Object.entries(theme)) {
				if (v) {
css += `  --${k}: ${v};\n`;
}
			}
		}

		if (light) {
			for (const [k, v] of Object.entries(light)) {
				if (v) {
css += `  --${k}: ${v};\n`;
}
			}

			css += `  --sidebar-background: var(--sidebar);\n`;
		}

		css += "}\n\n.dark {\n";

		if (dark) {
			for (const [k, v] of Object.entries(dark)) {
				if (v) {
css += `  --${k}: ${v};\n`;
}
			}

			css += `  --sidebar-background: var(--sidebar);\n`;
		}

		css += "}\n";

		styleEl.textContent = css;

		// Sync menu colors
		stopMenuSync?.();
		stopMenuSync = syncMenuColor(designSystem.menuColor);
	});

	onDestroy(() => {
		stopMenuSync?.();
		stopMenuSync = null;
	});
</script>

{@render children()}
