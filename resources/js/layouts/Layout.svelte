<script lang="ts">
    import type {Snippet} from 'svelte';
    import iconHome from '@ktibow/iconset-material-symbols/home';
    import {NavCMLX, NavCMLXItem} from 'm3-svelte';
    import { inertia, Link, page } from '@inertiajs/svelte';
    import type {IconifyIcon} from "@iconify/types";
    import { inertiaNav } from '@/lib/inertia-nav';


    interface Props {
        children?: Snippet;
    }

    interface NavPath {
        path: string;
        icon: IconifyIcon;
        label: string;
    }

    let {children}: Props = $props();

    const paths: NavPath[] = [
        {
            path: '/',
            icon: iconHome,
            label: 'Home',
        },
        {
            path: '/dashboard',
            icon: iconHome,
            label: 'Dashboard',
        },
    ];

    const normalizePath = (path: string) => {
        let normalized = String(path);
        if (normalized.endsWith('/') && normalized !== '/') {
            normalized = normalized.slice(0, -1);
        }
        return normalized || '/';
    };
</script>

<div class="container">
    <div class="sidebar">
        <NavCMLX variant="auto">
            {#each paths as {path, icon, label}}
                <NavCMLXItem
                    {@attach inertiaNav}
                    variant="auto"
                    href={normalizePath(path)}
                    selected={normalizePath(path) === normalizePath($page.url.pathname)}
                    {icon}
                    text={label}
                />
            {/each}
        </NavCMLX>
    </div>
    <div class="content">
        {@render children?.()}
    </div>
</div>

<style>
    .container {
        display: grid;
        min-height: 100dvh;
    }

    .sidebar {
        display: flex;
        position: sticky;
    }

    .content {
        display: flex;
        flex-direction: column;
        padding: 1rem;
    }

    @media (width < 52.5rem) {
        .container {
            grid-template-rows: 1fr auto;
        }

        .sidebar {
            flex-direction: column;
            bottom: 0;
            width: 100%;
            z-index: 3;
            grid-row: 2;
        }
    }

    @media (width >= 52.5rem) {
        .container {
            grid-template-columns: auto 1fr;
        }

        .sidebar {
            grid-column: 1;
            top: 0;
            left: 0;
            flex-direction: column;
            height: 100dvh;
        }

        .sidebar :global(nav) {
            position: sticky;
            top: 50%;
            translate: 0 -50%;
        }

        .content {
            padding: 1.5rem;
            grid-column: 2;
        }
    }
</style>
