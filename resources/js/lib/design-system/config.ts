import {
    generateM3Theme,
    M3_VARIANTS,
    M3_SEED_PRESETS,
    isValidHex,
    normalizeHex,
} from './m3-generator.js';
import type { M3Variant, TonalRamp, M3ThemeResult } from './m3-generator.js';
import { BASE_THEMES, THEMES } from './themes.js';
import type { BaseTheme, Theme } from './themes.js';

export {
    BASE_THEMES,
    THEMES,
    type BaseTheme,
    type Theme,
    generateM3Theme,
    type M3Variant,
    M3_VARIANTS,
    M3_SEED_PRESETS,
    type TonalRamp,
    type M3ThemeResult,
    isValidHex,
    normalizeHex,
};

export const STYLES = [
    {
        name: 'vega',
        title: 'Vega',
        description:
            'The classic shadcn/ui look. Clean, neutral, and familiar.',
    },
    {
        name: 'nova',
        title: 'Nova',
        description: 'Reduced padding and margins for compact layouts.',
    },
    {
        name: 'maia',
        title: 'Maia',
        description: 'Soft and rounded, with generous spacing.',
    },
    {
        name: 'lyra',
        title: 'Lyra',
        description: 'Boxy and sharp. Pairs well with mono fonts.',
    },
    {
        name: 'mira',
        title: 'Mira',
        description: 'Compact. Made for dense interfaces.',
    },
    {
        name: 'luma',
        title: 'Luma',
        description: 'Rounded geometry. Soft elevation. Breathable layouts.',
    },
    {
        name: 'sera',
        title: 'Sera',
        description: 'Editorial and typographic.',
    },
    {
        name: 'rhea',
        title: 'Rhea',
        description: 'Like Luma but compact.',
    },
] as const;

export type Style = (typeof STYLES)[number];
export type StyleName = Style['name'];
export type ThemeName = Theme['name'];
export type BaseColorName = BaseTheme['name'];

export const MENU_COLORS = [
    { value: 'default', label: 'Default' },
    { value: 'inverted', label: 'Inverted' },
] as const;

export type MenuColor = (typeof MENU_COLORS)[number];
export type MenuColorValue = MenuColor['value'];

export const FONT_SIZES = [
    { value: 75, label: '75%', size: '12px' },
    { value: 100, label: '100%', size: '16px' },
    { value: 125, label: '125%', size: '20px' },
    { value: 150, label: '150%', size: '24px' },
] as const;

export type FontSize = (typeof FONT_SIZES)[number];
export type FontSizeValue = FontSize['value'];

export const RADII = [
    { name: 'default', label: 'Default', value: '0.5rem' },
    { name: 'none', label: 'None', value: '0rem' },
    { name: 'small', label: 'Small', value: '0.45rem' },
    { name: 'medium', label: 'Medium', value: '0.625rem' },
    { name: 'large', label: 'Large', value: '0.875rem' },
] as const;

export type Radius = (typeof RADII)[number];
export type RadiusOverrideName = Radius['name'];
export type RadiusValue = RadiusOverrideName | null;

export const STYLE_PRESET_RADII: Record<StyleName, string> = {
    nova: '0.5rem',
    vega: '0.5rem',
    maia: '0.75rem',
    lyra: '0rem',
    mira: '0.375rem',
    luma: '0.75rem',
    sera: '0rem',
    rhea: '0.875rem',
};

export const ICON_LIBRARIES = [
    { name: 'lucide', label: 'Lucide' },
    { name: 'hugeicons', label: 'Hugeicons' },
    { name: 'tabler', label: 'Tabler' },
    { name: 'phosphor', label: 'Phosphor' },
    { name: 'remixicon', label: 'Remixicon' },
] as const;

export type IconLibraryName = (typeof ICON_LIBRARIES)[number]['name'];

export interface DesignSystemConfig {
    style: StyleName;
    sourceColor: string;
    variant: M3Variant;
    contrast: number;
    iconLibrary: IconLibraryName;
    font: string;
    fontHeading?: string;
    fontSize: FontSizeValue;
    menuColor: MenuColorValue;
    radius: RadiusValue;
    baseColor?: string;
    theme?: string;
    chartColor?: string;
}

export const DEFAULT_CONFIG: DesignSystemConfig = {
    style: 'nova',
    sourceColor: '#2563eb',
    variant: 'tonalSpot',
    contrast: 0.0,
    iconLibrary: 'lucide',
    font: 'inter',
    fontHeading: 'inherit',
    fontSize: 100,
    menuColor: 'default',
    radius: null,
    baseColor: 'neutral',
    theme: 'neutral',
    chartColor: 'neutral',
};

export function getBaseColor(name?: string) {
    return BASE_THEMES.find((color) => color.name === name);
}

export function getTheme(name?: string) {
    return THEMES.find((theme) => theme.name === name);
}

export function getStyle(name: StyleName) {
    return STYLES.find((style) => style.name === name);
}

export function buildRegistryTheme(config: DesignSystemConfig) {
    const sourceColor = config.sourceColor ?? '#2563eb';
    const variant = config.variant ?? 'tonalSpot';
    const contrast = config.contrast ?? 0.0;

    // Pure Material 3 Dynamic Scheme Generation
    const m3 = generateM3Theme({
        sourceColor,
        variant,
        contrast,
    });

    const lightVars: Record<string, string> = { ...m3.light };
    const darkVars: Record<string, string> = { ...m3.dark };
    const themeVars: Record<string, string> = {};

    // Canonical M3 Navigation Active Tokens (Tonal Container)
    lightVars['sidebar-accent-active'] = lightVars['primary-container'];
    lightVars['sidebar-accent-active-foreground'] =
        lightVars['on-primary-container'];
    darkVars['sidebar-accent-active'] = darkVars['primary-container'];
    darkVars['sidebar-accent-active-foreground'] =
        darkVars['on-primary-container'];

    // Apply radius: Dual Track (Track 1 = Preset / null, Track 2 = Override)
    const presetRadius = STYLE_PRESET_RADII[config.style] ?? '0.5rem';
    lightVars['radius-preset'] = presetRadius;
    darkVars['radius-preset'] = presetRadius;

    if (config.radius === null || config.radius === undefined) {
        lightVars.radius = presetRadius;
        darkVars.radius = presetRadius;
        delete lightVars['radius-override'];
        delete darkVars['radius-override'];
    } else {
        const radius = RADII.find((r) => r.name === config.radius);
        const radiusValue = radius?.value ?? '0.5rem';
        lightVars.radius = radiusValue;
        darkVars.radius = radiusValue;
        lightVars['radius-override'] = radiusValue;
        darkVars['radius-override'] = radiusValue;
    }

    return {
        name: `m3-${variant}-${sourceColor}`,
        cssVars: {
            light: lightVars,
            dark: darkVars,
            theme: themeVars,
        },
        ramps: m3.ramps,
    };
}
