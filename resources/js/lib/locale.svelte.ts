import { page } from '@inertiajs/svelte';
import { SvelteDate } from 'svelte/reactivity';

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

    const locale = useLocale();

    return new SvelteDate(date).toLocaleDateString(
        locale === 'id' ? 'id-ID' : 'en-GB',
        {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        },
    );
}

export function formatFullDateTime(date: string | null): string {
    if (!date) {
        return '';
    }

    const locale = useLocale();

    return new SvelteDate(date).toLocaleString(
        locale === 'id' ? 'id-ID' : 'en-GB',
        {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        },
    );
}

export function formatRelativeDate(
    date: string | null,
    relativeDaysLimit: number = 7,
): string {
    if (!date) {
        return '';
    }

    const targetDate = new SvelteDate(date);
    const target = targetDate.getTime();

    if (isNaN(target)) {
        return '';
    }

    const now = Date.now();
    const diffInSeconds = Math.round((target - now) / 1000);
    const diffInMinutes = Math.round(diffInSeconds / 60);
    const diffInHours = Math.round(diffInMinutes / 60);
    const diffInDays = Math.round(diffInHours / 24);

    if (diffInDays < -relativeDaysLimit || diffInDays > 1) {
        return formatDate(date);
    }

    const locale = useLocale();
    const rtf = new Intl.RelativeTimeFormat(
        locale === 'id' ? 'id-ID' : 'en-GB',
        { numeric: 'auto' },
    );

    if (Math.abs(diffInSeconds) < 60) {
        return locale === 'id' ? 'baru saja' : 'just now';
    }

    if (Math.abs(diffInMinutes) < 60) {
        return rtf.format(diffInMinutes, 'minute');
    }

    if (Math.abs(diffInHours) < 24) {
        return rtf.format(diffInHours, 'hour');
    }

    return rtf.format(diffInDays, 'day');
}
