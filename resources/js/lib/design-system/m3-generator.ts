import {
    argbFromHex,
    hexFromArgb,
    Hct,
    SchemeTonalSpot,
    SchemeExpressive,
    SchemeVibrant,
    SchemeFidelity,
    SchemeContent,
    SchemeNeutral,
    SchemeMonochrome,
} from '@material/material-color-utilities';
import type { DynamicScheme } from '@material/material-color-utilities';

export type M3Variant =
    | 'tonalSpot'
    | 'expressive'
    | 'vibrant'
    | 'fidelity'
    | 'content'
    | 'neutral'
    | 'monochrome';

export interface M3VariantInfo {
    value: M3Variant;
    label: string;
    description: string;
}

export const M3_VARIANTS: M3VariantInfo[] = [
    {
        value: 'tonalSpot',
        label: 'Tonal Spot',
        description: 'Default Material 3. Calm, balanced, and harmonized.',
    },
    {
        value: 'expressive',
        label: 'Expressive',
        description: 'Colorful tertiary accents and playful, lively contrast.',
    },
    {
        value: 'vibrant',
        label: 'Vibrant',
        description: 'High chroma and vivid saturation for maximum impact.',
    },
    {
        value: 'fidelity',
        label: 'Fidelity',
        description: 'Strict adherence to the source color hue and tone.',
    },
    {
        value: 'content',
        label: 'Content',
        description: 'Optimized for readability and content-forward UI.',
    },
    {
        value: 'neutral',
        label: 'Neutral',
        description: 'Minimal saturation, subtle and elegant monochrome hues.',
    },
    {
        value: 'monochrome',
        label: 'Monochrome',
        description: 'Pure grayscale tones without hue tinting.',
    },
];

export interface M3SeedPreset {
    name: string;
    hex: string;
    label: string;
}

export const M3_SEED_PRESETS: M3SeedPreset[] = [
    { name: 'tonan-blue', hex: '#2563eb', label: 'Tonan Blue' },
    { name: 'royal-indigo', hex: '#4f46e5', label: 'Royal Indigo' },
    { name: 'deep-violet', hex: '#7c3aed', label: 'Deep Violet' },
    { name: 'sakura-pink', hex: '#db2777', label: 'Sakura Pink' },
    { name: 'coral-rose', hex: '#e11d48', label: 'Coral Rose' },
    { name: 'sunset-amber', hex: '#d97706', label: 'Sunset Amber' },
    { name: 'emerald-green', hex: '#059669', label: 'Emerald Green' },
    { name: 'ocean-cyan', hex: '#0891b2', label: 'Ocean Cyan' },
    { name: 'slate-minimal', hex: '#475569', label: 'Slate Minimal' },
];

export interface TonalRamp {
    tone: number;
    hex: string;
}

export interface M3ThemeOptions {
    sourceColor: string;
    variant?: M3Variant;
    contrast?: number; // -1.0 to 1.0, default 0.0
}

export interface M3ThemeResult {
    light: Record<string, string>;
    dark: Record<string, string>;
    ramps: {
        primary: TonalRamp[];
        secondary: TonalRamp[];
        tertiary: TonalRamp[];
        neutral: TonalRamp[];
        neutralVariant: TonalRamp[];
        error: TonalRamp[];
    };
}

const SCALE_TONES = [
    { step: '50', tone: 98 },
    { step: '100', tone: 95 },
    { step: '200', tone: 90 },
    { step: '300', tone: 80 },
    { step: '400', tone: 70 },
    { step: '500', tone: 60 },
    { step: '600', tone: 50 },
    { step: '700', tone: 40 },
    { step: '800', tone: 30 },
    { step: '900', tone: 20 },
    { step: '950', tone: 10 },
];

const VISUALIZER_TONES = [10, 20, 30, 40, 50, 60, 70, 80, 90, 95, 98];

function createScheme(
    hct: Hct,
    isDark: boolean,
    contrast: number,
    variant: M3Variant,
): DynamicScheme {
    switch (variant) {
        case 'expressive':
            return new SchemeExpressive(hct, isDark, contrast);
        case 'vibrant':
            return new SchemeVibrant(hct, isDark, contrast);
        case 'fidelity':
            return new SchemeFidelity(hct, isDark, contrast);
        case 'content':
            return new SchemeContent(hct, isDark, contrast);
        case 'neutral':
            return new SchemeNeutral(hct, isDark, contrast);
        case 'monochrome':
            return new SchemeMonochrome(hct, isDark, contrast);
        case 'tonalSpot':
        default:
            return new SchemeTonalSpot(hct, isDark, contrast);
    }
}

export function isValidHex(hex: string): boolean {
    return /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(hex.trim());
}

export function normalizeHex(hex: string): string {
    const trimmed = hex.trim();

    if (!trimmed.startsWith('#')) {
        return `#${trimmed}`;
    }

    return trimmed;
}

export function generateM3Theme(options: M3ThemeOptions): M3ThemeResult {
    const rawHex = options.sourceColor || '#2563eb';
    const hex = isValidHex(rawHex) ? normalizeHex(rawHex) : '#2563eb';
    const variant = options.variant ?? 'tonalSpot';
    const contrast = Math.min(1.0, Math.max(-1.0, options.contrast ?? 0.0));

    const argb = argbFromHex(hex);
    const hct = Hct.fromInt(argb);

    const sLight = createScheme(hct, false, contrast, variant);
    const sDark = createScheme(hct, true, contrast, variant);

    function buildVars(scheme: DynamicScheme): Record<string, string> {
        const vars: Record<string, string> = {
            // 1. Shadcn Core Semantic Bridge
            background: hexFromArgb(scheme.surface),
            foreground: hexFromArgb(scheme.onSurface),
            card: hexFromArgb(scheme.surfaceContainerLow),
            'card-foreground': hexFromArgb(scheme.onSurface),
            popover: hexFromArgb(scheme.surfaceContainer),
            'popover-foreground': hexFromArgb(scheme.onSurface),
            primary: hexFromArgb(scheme.primary),
            'primary-foreground': hexFromArgb(scheme.onPrimary),
            secondary: hexFromArgb(scheme.secondaryContainer),
            'secondary-foreground': hexFromArgb(scheme.onSecondaryContainer),
            muted: hexFromArgb(scheme.surfaceContainerHigh),
            'muted-foreground': hexFromArgb(scheme.onSurfaceVariant),
            accent: hexFromArgb(scheme.surfaceContainerHighest),
            'accent-foreground': hexFromArgb(scheme.onSurface),
            destructive: hexFromArgb(scheme.error),
            'destructive-foreground': hexFromArgb(scheme.onError),
            border: hexFromArgb(scheme.outlineVariant),
            input: hexFromArgb(scheme.outlineVariant),
            ring: hexFromArgb(scheme.primary),

            // 2. Charts (harmonious M3 tertiary & secondary accents)
            'chart-1': hexFromArgb(scheme.primary),
            'chart-2': hexFromArgb(scheme.tertiary),
            'chart-3': hexFromArgb(scheme.secondary),
            'chart-4': hexFromArgb(scheme.primaryContainer),
            'chart-5': hexFromArgb(scheme.tertiaryContainer),

            // 3. Sidebar Semantic Tokens
            sidebar: hexFromArgb(scheme.surfaceContainerLow),
            'sidebar-foreground': hexFromArgb(scheme.onSurface),
            'sidebar-primary': hexFromArgb(scheme.primary),
            'sidebar-primary-foreground': hexFromArgb(scheme.onPrimary),
            'sidebar-accent': hexFromArgb(scheme.surfaceContainerHigh),
            'sidebar-accent-foreground': hexFromArgb(scheme.onSurface),
            'sidebar-border': hexFromArgb(scheme.outlineVariant),
            'sidebar-ring': hexFromArgb(scheme.primary),

            // 4. M3 Accent Containers & On-Tokens
            'primary-container': hexFromArgb(scheme.primaryContainer),
            'on-primary-container': hexFromArgb(scheme.onPrimaryContainer),
            'secondary-container': hexFromArgb(scheme.secondaryContainer),
            'on-secondary-container': hexFromArgb(scheme.onSecondaryContainer),
            'tertiary-container': hexFromArgb(scheme.tertiaryContainer),
            'on-tertiary-container': hexFromArgb(scheme.onTertiaryContainer),
            'error-container': hexFromArgb(scheme.errorContainer),
            'on-error-container': hexFromArgb(scheme.onErrorContainer),

            // 5. M3 Surface Elevation & Container Tokens
            'surface-container-lowest': hexFromArgb(
                scheme.surfaceContainerLowest,
            ),
            'surface-container-low': hexFromArgb(scheme.surfaceContainerLow),
            'surface-container': hexFromArgb(scheme.surfaceContainer),
            'surface-container-high': hexFromArgb(scheme.surfaceContainerHigh),
            'surface-container-highest': hexFromArgb(
                scheme.surfaceContainerHighest,
            ),
            'surface-dim': hexFromArgb(scheme.surfaceDim),
            'surface-bright': hexFromArgb(scheme.surfaceBright),
            surface: hexFromArgb(scheme.surface),
            'on-surface': hexFromArgb(scheme.onSurface),
            'on-surface-variant': hexFromArgb(scheme.onSurfaceVariant),
            outline: hexFromArgb(scheme.outline),
            'outline-variant': hexFromArgb(scheme.outlineVariant),
        };

        // 5. Tailwind Numeric Scales (50..950)
        for (const { step, tone } of SCALE_TONES) {
            vars[`surface-${step}`] = hexFromArgb(
                scheme.neutralPalette.tone(tone),
            );
            vars[`primary-${step}`] = hexFromArgb(
                scheme.primaryPalette.tone(tone),
            );
            vars[`secondary-${step}`] = hexFromArgb(
                scheme.secondaryPalette.tone(tone),
            );
            vars[`tertiary-${step}`] = hexFromArgb(
                scheme.tertiaryPalette.tone(tone),
            );
        }

        return vars;
    }

    function buildRamp(palette: DynamicScheme['primaryPalette']): TonalRamp[] {
        return VISUALIZER_TONES.map((tone) => ({
            tone,
            hex: hexFromArgb(palette.tone(tone)),
        }));
    }

    return {
        light: buildVars(sLight),
        dark: buildVars(sDark),
        ramps: {
            primary: buildRamp(sLight.primaryPalette),
            secondary: buildRamp(sLight.secondaryPalette),
            tertiary: buildRamp(sLight.tertiaryPalette),
            neutral: buildRamp(sLight.neutralPalette),
            neutralVariant: buildRamp(sLight.neutralVariantPalette),
            error: buildRamp(sLight.errorPalette),
        },
    };
}
