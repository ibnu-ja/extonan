import { decodePreset, isValidPreset } from 'shadcn-svelte/preset';
import { getContext, setContext } from 'svelte';
import { DEFAULT_CONFIG } from './config.js';
import type {
    DesignSystemConfig,
    FontSizeValue,
    IconLibraryName,
    MenuColorValue,
    RadiusValue,
    StyleName,
    M3Variant,
} from './config.js';

const STORAGE_KEY = 'extonan_design_system';
const COOKIE_KEY = 'theme_preset';
const CONTEXT_KEY = Symbol.for('extonan.design-system');

function getCookie(name: string): string | null {
    if (typeof document === 'undefined') {
        return null;
    }

    const match = document.cookie.match(
        new RegExp(`(?:^|;\\s*)${name}=([^;]+)`),
    );

    return match ? decodeURIComponent(match[1]) : null;
}

function setCookie(name: string, value: string, days = 365) {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${encodeURIComponent(value)};path=/;max-age=${maxAge};SameSite=Lax`;
}

function removeCookie(name: string) {
    if (typeof document === 'undefined') {
        return;
    }

    document.cookie = `${name}=;path=/;max-age=0;SameSite=Lax`;
}

interface SerializedPreset {
    s: StyleName;
    c: string;
    v: M3Variant;
    k: number;
    i: IconLibraryName;
    f: string;
    fh?: string;
    fs?: FontSizeValue;
    m: MenuColorValue;
    r: RadiusValue;
    b?: string;
    t?: string;
    ch?: string;
}

export function encodeDesignSystemPreset(config: DesignSystemConfig): string {
    const payload: SerializedPreset = {
        s: config.style,
        c: config.sourceColor,
        v: config.variant,
        k: config.contrast,
        i: config.iconLibrary,
        f: config.font,
        fh: config.fontHeading,
        fs: config.fontSize,
        m: config.menuColor,
        r: config.radius,
        b: config.baseColor,
        t: config.theme,
        ch: config.chartColor,
    };

    try {
        const json = JSON.stringify(payload);
        const b64 = btoa(unescape(encodeURIComponent(json)))
            .replace(/\+/g, '-')
            .replace(/\//g, '_')
            .replace(/=+$/, '');

        return `ex_${b64}`;
    } catch {
        return '';
    }
}

export function decodeDesignSystemPreset(
    presetString: string,
): Partial<DesignSystemConfig> | null {
    if (!presetString || typeof presetString !== 'string') {
        return null;
    }

    const str = presetString.trim();

    // 1. Comprehensive Extonan format (ex_...)
    if (str.startsWith('ex_')) {
        try {
            let b64 = str.slice(3).replace(/-/g, '+').replace(/_/g, '/');

            while (b64.length % 4 !== 0) {
                b64 += '=';
            }

            const json = decodeURIComponent(escape(atob(b64)));
            const p = JSON.parse(json) as Partial<SerializedPreset>;

            let resolvedFs: FontSizeValue | undefined;

            if (typeof p.fs === 'number') {
                if (
                    p.fs === 75 ||
                    p.fs === 100 ||
                    p.fs === 125 ||
                    p.fs === 150
                ) {
                    resolvedFs = p.fs as FontSizeValue;
                } else {
                    const clamped = Math.min(150, Math.max(75, p.fs));
                    resolvedFs = (Math.round(clamped / 25) *
                        25) as FontSizeValue;
                }
            } else if ((p.fs as unknown) === 'sm') {
                resolvedFs = 75;
            } else if ((p.fs as unknown) === 'lg') {
                resolvedFs = 125;
            } else if ((p.fs as unknown) === 'default') {
                resolvedFs = 100;
            }

            return {
                style: p.s,
                sourceColor: p.c,
                variant: p.v,
                contrast: typeof p.k === 'number' ? p.k : undefined,
                iconLibrary: p.i,
                font: p.f,
                fontHeading: p.fh,
                fontSize: resolvedFs,
                menuColor: p.m === 'inverted' ? 'inverted' : 'default',
                radius: p.r,
                baseColor: p.b,
                theme: p.t,
                chartColor: p.ch,
            };
        } catch {
            return null;
        }
    }

    // 2. Direct JSON format
    if (str.startsWith('{') && str.endsWith('}')) {
        try {
            return JSON.parse(str) as Partial<DesignSystemConfig>;
        } catch {
            // continue
        }
    }

    // 3. Fallback to upstream shadcn-svelte preset
    try {
        if (isValidPreset(str)) {
            const decoded = decodePreset(str);

            if (decoded) {
                return decoded as unknown as Partial<DesignSystemConfig>;
            }
        }
    } catch {
        // ignore
    }

    return null;
}

export function isValidDesignSystemPreset(presetString: string): boolean {
    return decodeDesignSystemPreset(presetString) !== null;
}

export class DesignSystemState {
    style = $state<StyleName>(DEFAULT_CONFIG.style);
    sourceColor = $state<string>(DEFAULT_CONFIG.sourceColor);
    variant = $state<M3Variant>(DEFAULT_CONFIG.variant);
    contrast = $state<number>(DEFAULT_CONFIG.contrast);
    baseColor = $state<string>(DEFAULT_CONFIG.baseColor ?? 'neutral');
    theme = $state<string>(DEFAULT_CONFIG.theme ?? 'neutral');
    chartColor = $state<string>(DEFAULT_CONFIG.chartColor ?? 'neutral');
    iconLibrary = $state<IconLibraryName>(DEFAULT_CONFIG.iconLibrary);
    font = $state<string>(DEFAULT_CONFIG.font);
    fontHeading = $state<string>(DEFAULT_CONFIG.fontHeading ?? 'inherit');
    fontSize = $state<FontSizeValue>(DEFAULT_CONFIG.fontSize);
    menuColor = $state<MenuColorValue>(DEFAULT_CONFIG.menuColor);
    radius = $state<RadiusValue>(DEFAULT_CONFIG.radius);

    constructor() {
        this.init();
    }

    private init() {
        if (typeof window === 'undefined') {
            return;
        }

        // 1. Try URL preset param
        const urlParams = new URLSearchParams(window.location.search);
        const presetParam = urlParams.get('preset');

        if (presetParam && isValidDesignSystemPreset(presetParam)) {
            const decoded = decodeDesignSystemPreset(presetParam);

            if (decoded) {
                this.applyConfig(decoded);

                return;
            }
        }

        // 2. Try Cookie theme_preset
        const cookiePreset = getCookie(COOKIE_KEY);

        if (cookiePreset && isValidDesignSystemPreset(cookiePreset)) {
            const decoded = decodeDesignSystemPreset(cookiePreset);

            if (decoded) {
                this.applyConfig(decoded);

                return;
            }
        }

        // 3. Try localStorage
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
            sourceColor: this.sourceColor,
            variant: this.variant,
            contrast: this.contrast,
            baseColor: this.baseColor,
            theme: this.theme,
            chartColor: this.chartColor,
            iconLibrary: this.iconLibrary,
            font: this.font,
            fontHeading: this.fontHeading,
            fontSize: this.fontSize,
            menuColor: this.menuColor,
            radius: this.radius,
        };
    }

    applyConfig(config: Partial<DesignSystemConfig>) {
        if (config.style) {
            this.style = config.style as StyleName;
        }

        if (config.sourceColor) {
            this.sourceColor = config.sourceColor;
        }

        if (config.variant) {
            this.variant = config.variant as M3Variant;
        }

        if (typeof config.contrast === 'number') {
            this.contrast = config.contrast;
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

        if (config.fontSize) {
            this.fontSize = config.fontSize as FontSizeValue;
        }

        if (config.menuColor) {
            this.menuColor =
                config.menuColor === 'inverted' ? 'inverted' : 'default';
        }

        if ('radius' in config) {
            this.radius = config.radius as RadiusValue;
        }

        this.persist();
    }

    applyPreset(presetString: string): boolean {
        const decoded = decodeDesignSystemPreset(presetString);

        if (!decoded) {
            return false;
        }

        this.applyConfig(decoded);

        return true;
    }

    get preset(): string {
        try {
            return encodeDesignSystemPreset(this.toConfig());
        } catch {
            return '';
        }
    }

    persist() {
        if (typeof window === 'undefined') {
            return;
        }

        const config = this.toConfig();

        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(config));
        } catch {
            // ignore storage quota errors
        }

        try {
            const token = this.preset;

            if (token) {
                setCookie(COOKIE_KEY, token);
            }
        } catch {
            // ignore cookie errors
        }
    }

    reset() {
        if (typeof window !== 'undefined') {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch {
                // ignore
            }

            removeCookie(COOKIE_KEY);
        }

        this.applyConfig(DEFAULT_CONFIG);
    }
}

export function setDesignSystem(
    state: DesignSystemState = new DesignSystemState(),
): DesignSystemState {
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
