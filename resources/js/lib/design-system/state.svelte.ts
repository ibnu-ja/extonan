import { decodePreset, encodePreset, isValidPreset  } from "shadcn-svelte/preset";
import type {PresetConfig} from "shadcn-svelte/preset";
import { getContext, setContext } from "svelte";
import {
	DEFAULT_CONFIG
	
	
	
	
	
	
} from "./config.js";
import type {DesignSystemConfig, IconLibraryName, MenuAccentValue, MenuColorValue, RadiusValue, StyleName} from "./config.js";

const STORAGE_KEY = "extonan_design_system";
const CONTEXT_KEY = Symbol.for("extonan.design-system");

function normalizeConfig(config: PresetConfig): PresetConfig {
	if ((config.style === "lyra" || config.style === "sera") && config.radius !== "none") {
		return { ...config, radius: "none" };
	}

	if (config.style === "rhea" && config.radius === "large") {
		return { ...config, radius: "default" };
	}

	return config;
}

export class DesignSystemState {
	style = $state<StyleName>(DEFAULT_CONFIG.style);
	baseColor = $state<string>(DEFAULT_CONFIG.baseColor);
	theme = $state<string>(DEFAULT_CONFIG.theme);
	chartColor = $state<string>(DEFAULT_CONFIG.chartColor);
	iconLibrary = $state<IconLibraryName>(DEFAULT_CONFIG.iconLibrary);
	font = $state<string>(DEFAULT_CONFIG.font);
	fontHeading = $state<string>(DEFAULT_CONFIG.fontHeading ?? "inherit");
	menuAccent = $state<MenuAccentValue>(DEFAULT_CONFIG.menuAccent);
	menuColor = $state<MenuColorValue>(DEFAULT_CONFIG.menuColor);
	radius = $state<RadiusValue>(DEFAULT_CONFIG.radius);

	constructor() {
		this.init();
	}

	private init() {
		if (typeof window === "undefined") {
return;
}

		// 1. Try URL preset param
		const urlParams = new URLSearchParams(window.location.search);
		const presetParam = urlParams.get("preset");

		if (presetParam && isValidPreset(presetParam)) {
			const decoded = decodePreset(presetParam);

			if (decoded) {
				this.applyConfig(normalizeConfig(decoded));

				return;
			}
		}

		// 2. Try localStorage
		try {
			const stored = localStorage.getItem(STORAGE_KEY);

			if (stored) {
				const parsed = JSON.parse(stored);
				this.applyConfig(parsed);
			}
		} catch {
			// Fallback to default
		}
	}

	toConfig(): DesignSystemConfig {
		return {
			style: this.style,
			baseColor: this.baseColor,
			theme: this.theme,
			chartColor: this.chartColor,
			iconLibrary: this.iconLibrary,
			font: this.font,
			fontHeading: this.fontHeading,
			menuAccent: this.menuAccent,
			menuColor: this.menuColor,
			radius: this.radius,
		};
	}

	applyConfig(config: Partial<DesignSystemConfig>) {
		if (config.style) {
this.style = config.style as StyleName;
}

		if (config.baseColor) {
this.baseColor = config.baseColor;
}

		if (config.theme) {
this.theme = config.theme;
}

		if (config.chartColor) {
this.chartColor = config.chartColor;
}

		if (config.iconLibrary) {
this.iconLibrary = config.iconLibrary as IconLibraryName;
}

		if (config.font) {
this.font = config.font;
}

		if (config.fontHeading) {
this.fontHeading = config.fontHeading;
}

		if (config.menuAccent) {
this.menuAccent = config.menuAccent as MenuAccentValue;
}

		if (config.menuColor) {
this.menuColor = config.menuColor as MenuColorValue;
}

		if (config.radius) {
this.radius = config.radius as RadiusValue;
}

		this.persist();
	}

	applyPreset(presetString: string): boolean {
		if (!isValidPreset(presetString)) {
return false;
}

		const decoded = decodePreset(presetString);

		if (!decoded) {
return false;
}

		this.applyConfig(normalizeConfig(decoded));

		return true;
	}

	get preset(): string {
		try {
			return encodePreset(normalizeConfig(this.toConfig() as unknown as PresetConfig));
		} catch {
			return "";
		}
	}

	persist() {
		if (typeof window === "undefined") {
return;
}

		try {
			localStorage.setItem(STORAGE_KEY, JSON.stringify(this.toConfig()));
		} catch {
			// ignore storage quota errors
		}
	}

	reset() {
		this.applyConfig(DEFAULT_CONFIG);
	}
}

export function setDesignSystem(state: DesignSystemState = new DesignSystemState()): DesignSystemState {
	setContext(CONTEXT_KEY, state);

	return state;
}

export function getDesignSystem(): DesignSystemState {
	const state = getContext<DesignSystemState | undefined>(CONTEXT_KEY);

	if (!state) {
		return new DesignSystemState();
	}

	return state;
}

export const useDesignSystem = getDesignSystem;
