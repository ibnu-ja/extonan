import type { LinkComponentBaseProps } from '@inertiajs/core';
import type { Component, SvelteComponent } from 'svelte';
import type { LazyIcon } from '@/components/icon';

export type IconProps = {
    lucide?: LazyIcon;
    tabler?: LazyIcon;
    hugeicons?: LazyIcon;
    phosphor?: LazyIcon;
    remixicon?: LazyIcon;
    class?: string;
    [key: string]: any;
};

type NavIcon =
    | Component<{ class?: string }>
    | (new (...args: any[]) => SvelteComponent<{ class?: string }>)
    | IconProps;

export type BreadcrumbItem = {
    title: string;
    href: NonNullable<LinkComponentBaseProps['href']>;
};

export type NavItem = {
    title: string;
    href: NonNullable<LinkComponentBaseProps['href']>;
    icon?: NavIcon;
    isActive?: boolean;
    exactActive?: boolean;
    items?: NavItem[];
};
