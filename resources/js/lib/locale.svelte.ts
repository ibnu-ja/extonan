import { page } from '@inertiajs/svelte';

type Translatable = Record<string, string | null>;

export function useLocale(): string {
    return (page.props.locale as string) ?? 'en';
}

export function t(
    translations: Translatable,
    fallbackLanguage: string = 'en',
): string {
    const locale = useLocale();

    return (
        translations[locale] ||
        translations[fallbackLanguage] ||
        translations.en ||
        translations.romaji ||
        translations.native ||
        ''
    );
}

export function formatDate(date: string | null): string {
    if (!date) {
        return '';
    }

    return new Date(date).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
