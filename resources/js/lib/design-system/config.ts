import { BASE_THEMES, THEMES } from "./themes.js";
import type { BaseTheme, Theme } from "./themes.js";

export { BASE_THEMES, THEMES, type BaseTheme, type Theme };

export const STYLES = [
	{
		name: "vega",
		title: "Vega",
		description: "The classic shadcn/ui look. Clean, neutral, and familiar.",
	},
	{
		name: "nova",
		title: "Nova",
		description: "Reduced padding and margins for compact layouts.",
	},
	{
		name: "maia",
		title: "Maia",
		description: "Soft and rounded, with generous spacing.",
	},
	{
		name: "lyra",
		title: "Lyra",
		description: "Boxy and sharp. Pairs well with mono fonts.",
	},
	{
		name: "mira",
		title: "Mira",
		description: "Compact. Made for dense interfaces.",
	},
	{
		name: "luma",
		title: "Luma",
		description: "Rounded geometry. Soft elevation. Breathable layouts.",
	},
	{
		name: "sera",
		title: "Sera",
		description: "Editorial and typographic.",
	},
	{
		name: "rhea",
		title: "Rhea",
		description: "Like Luma but compact.",
	},
] as const;

export type Style = (typeof STYLES)[number];
export type StyleName = Style["name"];
export type ThemeName = Theme["name"];
export type BaseColorName = BaseTheme["name"];

export const MENU_ACCENTS = [
	{ value: "subtle", label: "Subtle" },
	{ value: "bold", label: "Bold" },
] as const;

export type MenuAccent = (typeof MENU_ACCENTS)[number];
export type MenuAccentValue = MenuAccent["value"];

export const MENU_COLORS = [
	{ value: "default", label: "Default" },
	{ value: "inverted", label: "Inverted" },
	{ value: "default-translucent", label: "Default Translucent" },
	{ value: "inverted-translucent", label: "Inverted Translucent" },
] as const;

export type MenuColor = (typeof MENU_COLORS)[number];
export type MenuColorValue = MenuColor["value"];

export const RADII = [
	{ name: "default", label: "Default", value: "0.5rem" },
	{ name: "none", label: "None", value: "0rem" },
	{ name: "small", label: "Small", value: "0.45rem" },
	{ name: "medium", label: "Medium", value: "0.625rem" },
	{ name: "large", label: "Large", value: "0.875rem" },
] as const;

export type Radius = (typeof RADII)[number];
export type RadiusValue = Radius["name"];

export const ICON_LIBRARIES = [
	{ name: "lucide", label: "Lucide" },
	{ name: "hugeicons", label: "Hugeicons" },
	{ name: "tabler", label: "Tabler" },
	{ name: "phosphor", label: "Phosphor" },
	{ name: "remixicon", label: "Remixicon" },
] as const;

export type IconLibraryName = (typeof ICON_LIBRARIES)[number]["name"];

export interface DesignSystemConfig {
	style: StyleName;
	baseColor: string;
	theme: string;
	chartColor: string;
	iconLibrary: IconLibraryName;
	font: string;
	fontHeading?: string;
	menuAccent: MenuAccentValue;
	menuColor: MenuColorValue;
	radius: RadiusValue;
}

export const DEFAULT_CONFIG: DesignSystemConfig = {
	style: "nova",
	baseColor: "neutral",
	theme: "neutral",
	chartColor: "neutral",
	iconLibrary: "lucide",
	font: "inter",
	fontHeading: "inherit",
	menuAccent: "subtle",
	menuColor: "default",
	radius: "large",
};

export function getBaseColor(name: string) {
	return BASE_THEMES.find((color) => color.name === name);
}

export function getTheme(name: string) {
	return THEMES.find((theme) => theme.name === name);
}

export function getStyle(name: StyleName) {
	return STYLES.find((style) => style.name === name);
}

export function buildRegistryTheme(config: DesignSystemConfig) {
	const baseColor = getBaseColor(config.baseColor) ?? BASE_THEMES[0];
	const theme = getTheme(config.theme) ?? THEMES[0];

	const lightVars: Record<string, string> = {
		...(baseColor.cssVars?.light as Record<string, string>),
		...(theme.cssVars?.light as Record<string, string>),
	};
	const darkVars: Record<string, string> = {
		...(baseColor.cssVars?.dark as Record<string, string>),
		...(theme.cssVars?.dark as Record<string, string>),
	};
	const themeVars: Record<string, string> = {};

	// Apply chart color override
	const chartTheme = getTheme(config.chartColor);

	if (chartTheme) {
		const chartLight = chartTheme.cssVars?.light as Record<string, string>;
		const chartDark = chartTheme.cssVars?.dark as Record<string, string>;

		for (let i = 1; i <= 5; i++) {
			const key = `chart-${i}`;

			if (chartLight?.[key]) {
lightVars[key] = chartLight[key];
}

			if (chartDark?.[key]) {
darkVars[key] = chartDark[key];
}
		}
	}

	// Apply menu accent
	if (config.menuAccent === "bold") {
		lightVars.accent = lightVars.primary;
		lightVars["accent-foreground"] = lightVars["primary-foreground"];
		darkVars.accent = darkVars.primary;
		darkVars["accent-foreground"] = darkVars["primary-foreground"];
		lightVars["sidebar-accent"] = lightVars.primary;
		lightVars["sidebar-accent-foreground"] = lightVars["primary-foreground"];
		darkVars["sidebar-accent"] = darkVars.primary;
		darkVars["sidebar-accent-foreground"] = darkVars["primary-foreground"];
	}

	// Apply radius
	if (config.radius && config.radius !== "default") {
		const radius = RADII.find((r) => r.name === config.radius);

		if (radius?.value) {
			lightVars.radius = radius.value;
		}
	}

	return {
		name: `${config.baseColor}-${config.theme}`,
		cssVars: {
			light: lightVars,
			dark: darkVars,
			theme: themeVars,
		},
	};
}
