import type { Appearance, ResolvedAppearance } from '@/types';

export type { Appearance, ResolvedAppearance };

export type ThemeState = {
    appearance: {
        value: Appearance;
    };
    resolvedAppearance: () => ResolvedAppearance;
    updateAppearance: (value: Appearance) => void;
};

const appearance = $state<{ value: Appearance }>({ value: 'system' });
let systemDark = $state(false);
let themeChangeMediaQuery: MediaQueryList | null = null;

const prefersDark = (): boolean => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const isDarkMode = (value: Appearance): boolean => {
    return (
        value === 'dark' ||
        (value === 'system' &&
            (typeof window !== 'undefined' ? systemDark : false))
    );
};

const getResolvedAppearance = (): ResolvedAppearance => {
    return isDarkMode(appearance.value) ? 'dark' : 'light';
};

const setCookie = (name: string, value: string, days = 365): void => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const applyTheme = (value: Appearance): void => {
    if (typeof document === 'undefined') {
        return;
    }

    const isDark = isDarkMode(value);
    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
};

const getStoredAppearance = (): Appearance => {
    if (typeof window === 'undefined') {
        return 'system';
    }

    const stored = localStorage.getItem('appearance');

    if (stored === 'light' || stored === 'dark' || stored === 'system') {
        return stored;
    }

    const match = /(?:^|;\s*)appearance=([^;]+)/.exec(document.cookie);

    if (
        match &&
        (match[1] === 'light' || match[1] === 'dark' || match[1] === 'system')
    ) {
        return match[1] as Appearance;
    }

    return 'system';
};

const handleSystemThemeChange = (e: MediaQueryListEvent): void => {
    systemDark = e.matches;
    applyTheme(appearance.value);
};

const detachThemeChangeListener = (): void => {
    if (!themeChangeMediaQuery) {
        return;
    }

    themeChangeMediaQuery.removeEventListener(
        'change',
        handleSystemThemeChange,
    );
    themeChangeMediaQuery = null;
};

export function initializeTheme(): () => void {
    if (typeof window === 'undefined') {
        return () => {};
    }

    systemDark = prefersDark();
    const stored = getStoredAppearance();
    appearance.value = stored;

    if (!localStorage.getItem('appearance')) {
        localStorage.setItem('appearance', stored);
    }

    setCookie('appearance', stored);
    applyTheme(stored);

    detachThemeChangeListener();
    themeChangeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    themeChangeMediaQuery.addEventListener('change', handleSystemThemeChange);

    return detachThemeChangeListener;
}

export function updateAppearance(value: Appearance): void {
    appearance.value = value;

    if (typeof window !== 'undefined') {
        localStorage.setItem('appearance', value);
    }

    setCookie('appearance', value);
    applyTheme(value);
}

export function themeState(): ThemeState {
    return {
        appearance,
        resolvedAppearance: getResolvedAppearance,
        updateAppearance,
    };
}
