<script lang="ts">
    import { Icon } from '@/components/icon';
    import { themeState } from '@/lib/theme.svelte';
    import type { Appearance } from '@/types';

    const { appearance, updateAppearance } = themeState();

    interface TabItem {
        value: Appearance;
        label: string;
        lucide: string;
        tabler: string;
        hugeicons: string;
        phosphor: string;
        remixicon: string;
    }

    const tabs: TabItem[] = [
        {
            value: 'light',
            label: 'Light',
            lucide: 'Sun',
            tabler: 'IconSun',
            hugeicons: 'Sun01Icon',
            phosphor: 'SunIcon',
            remixicon: 'RiSunLine',
        },
        {
            value: 'dark',
            label: 'Dark',
            lucide: 'Moon',
            tabler: 'IconMoon',
            hugeicons: 'Moon02Icon',
            phosphor: 'MoonIcon',
            remixicon: 'RiMoonLine',
        },
        {
            value: 'system',
            label: 'System',
            lucide: 'Monitor',
            tabler: 'IconDeviceDesktop',
            hugeicons: 'ComputerIcon',
            phosphor: 'DesktopIcon',
            remixicon: 'RiComputerLine',
        },
    ];

    function handleAppearanceChange(value: Appearance) {
        updateAppearance(value);
    }
</script>

<div class="inline-flex gap-1 rounded-[var(--radius)] bg-muted p-1">
    {#each tabs as tab (tab.value)}
        <button
            onclick={() => handleAppearanceChange(tab.value)}
            class="flex items-center rounded-[max(0px,calc(var(--radius)-2px))] px-3.5 py-1.5 transition-colors {appearance.value ===
            tab.value
                ? 'bg-background text-foreground shadow-xs font-medium'
                : 'text-muted-foreground hover:bg-background/50 hover:text-foreground'}"
        >
            <Icon
                lucide={tab.lucide}
                tabler={tab.tabler}
                hugeicons={tab.hugeicons}
                phosphor={tab.phosphor}
                remixicon={tab.remixicon}
                class="-ml-1 size-4"
            />
            <span class="ml-1.5 text-sm">{tab.label}</span>
        </button>
    {/each}
</div>
