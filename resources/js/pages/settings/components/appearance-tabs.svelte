<script lang="ts">
    import { Icon } from '@/components/icon';
    import * as Tabs from '@/components/ui/tabs';
    import { themeState } from '@/lib/theme.svelte';
    import type { Appearance } from '@/types';

    const { appearance, updateAppearance } = themeState();

    const tabs = [
        {
            value: 'light',
            label: 'Light',
            lucide: () => import('lucide-svelte/icons/sun'),
            tabler: () => import('@tabler/icons-svelte/icons/sun'),
            hugeicons: () => import('@hugeicons/core-free-icons/Sun01Icon'),
            phosphor: () => import('phosphor-svelte/lib/SunIcon'),
            remixicon: () => import('remixicon-svelte/icons/sun-line'),
        },
        {
            value: 'dark',
            label: 'Dark',
            lucide: () => import('lucide-svelte/icons/moon'),
            tabler: () => import('@tabler/icons-svelte/icons/moon'),
            hugeicons: () => import('@hugeicons/core-free-icons/Moon02Icon'),
            phosphor: () => import('phosphor-svelte/lib/MoonIcon'),
            remixicon: () => import('remixicon-svelte/icons/moon-line'),
        },
        {
            value: 'system',
            label: 'System',
            lucide: () => import('lucide-svelte/icons/monitor'),
            tabler: () => import('@tabler/icons-svelte/icons/device-desktop'),
            hugeicons: () => import('@hugeicons/core-free-icons/ComputerIcon'),
            phosphor: () => import('phosphor-svelte/lib/DesktopIcon'),
            remixicon: () => import('remixicon-svelte/icons/computer-line'),
        },
    ] as const;

    function handleAppearanceChange(value: string | undefined) {
        if (value) {
            updateAppearance(value as Appearance);
        }
    }
</script>

<Tabs.Root value={appearance.value} onValueChange={handleAppearanceChange}>
    <Tabs.List class="h-9 p-1">
        {#each tabs as tab (tab.value)}
            <Tabs.Trigger value={tab.value} class="gap-1.5 px-3 py-1 text-xs">
                <Icon
                    lucide={tab.lucide}
                    tabler={tab.tabler}
                    hugeicons={tab.hugeicons}
                    phosphor={tab.phosphor}
                    remixicon={tab.remixicon}
                    class="size-3.5"
                />
                <span>{tab.label}</span>
            </Tabs.Trigger>
        {/each}
    </Tabs.List>
</Tabs.Root>
