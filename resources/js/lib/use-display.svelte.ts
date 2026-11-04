import { MediaQuery } from 'svelte/reactivity';

export type DisplayQueries = {
    readonly smAndUp: MediaQuery;
    readonly smAndDown: MediaQuery;
    readonly mdAndUp: MediaQuery;
    readonly mdAndDown: MediaQuery;
    readonly lgAndUp: MediaQuery;
    readonly lgAndDown: MediaQuery;
    readonly xlAndUp: MediaQuery;
    readonly xlAndDown: MediaQuery;
    readonly '2xlAndUp': MediaQuery;
    readonly '2xlAndDown': MediaQuery;
};

const style =
    typeof document !== 'undefined'
        ? getComputedStyle(document.documentElement)
        : null;
const r = (name: string, fb: number) =>
    style
        ? parseInt(style.getPropertyValue(`--breakpoint-${name}`).trim(), 10) ||
          fb
        : fb;

const px = {
    sm: r('sm', 640),
    md: r('md', 768),
    lg: r('lg', 1024),
    xl: r('xl', 1280),
    '2xl': r('2xl', 1536),
};

const m = (mq: string, fb: boolean): MediaQuery => {
    try {
        return new MediaQuery(mq, fb);
    } catch {
        return { current: fb } as MediaQuery;
    }
};

export function useDisplay(): DisplayQueries {
    return {
        smAndUp: m(`(min-width: ${px.sm}px)`, false),
        smAndDown: m(`(max-width: ${px.sm - 1}px)`, true),
        mdAndUp: m(`(min-width: ${px.md}px)`, false),
        mdAndDown: m(`(max-width: ${px.md - 1}px)`, true),
        lgAndUp: m(`(min-width: ${px.lg}px)`, false),
        lgAndDown: m(`(max-width: ${px.lg - 1}px)`, true),
        xlAndUp: m(`(min-width: ${px.xl}px)`, false),
        xlAndDown: m(`(max-width: ${px.xl - 1}px)`, true),
        '2xlAndUp': m(`(min-width: ${px['2xl']}px)`, false),
        '2xlAndDown': m(`(max-width: ${px['2xl'] - 1}px)`, true),
    };
}
