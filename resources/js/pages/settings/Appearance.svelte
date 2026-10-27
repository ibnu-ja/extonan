<script module lang="ts">
    import { edit as editAppearance } from '@/routes/appearance';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Appearance settings',
                href: editAppearance(),
            },
        ],
    };
</script>

<script lang="ts">
    import AppHead from '@/components/app-head.svelte';
    import Heading from '@/components/heading.svelte';
    import { Separator } from '@/components/ui/separator';
    import AppearanceTabs from './components/appearance-tabs.svelte';
    import ThemeCustomizer from './components/theme-customizer.svelte';
</script>

<AppHead title="Appearance settings" />

<h1 class="sr-only">Appearance settings</h1>

<div class="space-y-6">
    <div
        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
    >
        <Heading
            variant="small"
            title="Theme mode"
            description="Select between light, dark, and system themes"
        />
        <AppearanceTabs />
    </div>
    <Separator />
    <ThemeCustomizer />
</div>
