<script lang="ts">
    import { HugeiconsIcon } from '@hugeicons/svelte';
    import type { Snippet } from 'svelte';
    import { getDesignSystem } from '@/lib/design-system/index.js';
    import { cn } from '@/lib/utils.js';

    export type LazyIcon = () => Promise<any>;

    interface Props {
        lucide?: LazyIcon;
        tabler?: LazyIcon;
        hugeicons?: LazyIcon;
        phosphor?: LazyIcon;
        remixicon?: LazyIcon;
        class?: string;
        placeholder?: Snippet;
        [key: string]: any;
    }

    let {
        lucide,
        tabler,
        hugeicons,
        phosphor,
        remixicon,
        class: className,
        placeholder,
        ...restProps
    }: Props = $props();

    const designSystem = getDesignSystem();
    const activeLibrary = $derived(designSystem?.iconLibrary ?? 'lucide');

    const activeLoader = $derived(
        activeLibrary === 'tabler' && tabler
            ? tabler
            : activeLibrary === 'hugeicons' && hugeicons
              ? hugeicons
              : activeLibrary === 'phosphor' && phosphor
                ? phosphor
                : activeLibrary === 'remixicon' && remixicon
                  ? remixicon
                  : (lucide ?? tabler ?? hugeicons ?? phosphor ?? remixicon),
    );

    let LoadedComponent = $state<any>(null);
    let isHugeiconsObject = $state(false);

    $effect(() => {
        const loader = activeLoader;

        if (!loader || typeof loader !== 'function') {
            LoadedComponent = null;
            isHugeiconsObject = false;

            return;
        }

        loader()
            .then((mod) => {
                const res = mod?.default ?? mod;

                if (
                    Array.isArray(res) ||
                    (res &&
                        typeof res === 'object' &&
                        (res.svg || res.name) &&
                        !res.$$render)
                ) {
                    LoadedComponent = res;
                    isHugeiconsObject = true;
                } else if (typeof res === 'function') {
                    LoadedComponent = res;
                    isHugeiconsObject = false;
                } else {
                    LoadedComponent = null;
                    isHugeiconsObject = false;
                }
            })
            .catch(() => {
                LoadedComponent = null;
                isHugeiconsObject = false;
            });
    });
</script>

{#if LoadedComponent}
    {#if isHugeiconsObject}
        <HugeiconsIcon
            icon={LoadedComponent}
            class={cn('size-4', className)}
            {...restProps}
        />
    {:else if typeof LoadedComponent === 'function'}
        {@const Comp = LoadedComponent}
        <Comp class={cn('size-4', className)} {...restProps} />
    {/if}
{:else if placeholder}
    {@render placeholder()}
{:else}
    <svg
        class={cn('size-4', className)}
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true"
        {...restProps}
    ></svg>
{/if}
