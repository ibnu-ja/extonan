<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import logoFullSrcLight from '@/assets/logo/inline color.svg';
    import logoFullSrcDark from '@/assets/logo/inline white.svg';
    import logoSrcLight from '@/assets/logo/logo color.svg';
    import logoSrcDark from '@/assets/logo/logo white.svg';
    import { useDesignSystem } from '@/lib/design-system';
    import { themeState } from '@/lib/theme.svelte';
    import { cn, toUrl } from '@/lib/utils';
    import { home } from '@/routes';

    interface Props {
        class?: string;
        asLink?: boolean;
        href?: string;
    }

    let {
        class: className = '',
        asLink = false,
        href = toUrl(home()),
    }: Props = $props();

    const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
    const designSystem = useDesignSystem();
    const { resolvedAppearance } = themeState();

    const isGlobalDark = $derived(resolvedAppearance() === 'dark');
    const isInvertedMenu = $derived(designSystem.menuColor === 'inverted');
    const isDark = $derived(isGlobalDark || isInvertedMenu);
</script>

{#snippet logoImages()}
    {#if isDark}
        <img
            src={logoSrcDark}
            alt={appName}
            class="mx-auto h-8 w-auto group-data-[collapsible=icon]:block hidden"
        />
        <img
            src={logoFullSrcDark}
            alt={appName}
            class="mx-auto h-8 w-auto group-data-[collapsible=icon]:hidden"
        />
    {:else}
        <img
            src={logoSrcLight}
            alt={appName}
            class="mx-auto h-8 w-auto group-data-[collapsible=icon]:block hidden"
        />
        <img
            src={logoFullSrcLight}
            alt={appName}
            class="mx-auto h-8 w-auto group-data-[collapsible=icon]:hidden"
        />
    {/if}
{/snippet}

{#if asLink}
    <Link
        {href}
        class={cn('mx-auto flex items-center justify-center', className)}
    >
        {@render logoImages()}
    </Link>
{:else}
    <div class={cn('mx-auto flex items-center justify-center', className)}>
        {@render logoImages()}
    </div>
{/if}
