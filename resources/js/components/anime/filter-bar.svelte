<script lang="ts">
    import { Icon } from '@/components/icon';
    import { Button } from '@/components/ui/button';
    import * as Command from '@/components/ui/command';
    import * as Drawer from '@/components/ui/drawer';
    import * as InputGroup from '@/components/ui/input-group';
    import * as Popover from '@/components/ui/popover';
    import { useDisplay } from '@/lib/use-display.svelte.js';

    let {
        search = $bindable(''),
        sort = '',
        sortOptions = [],
        clearable = true,
        hasActiveFilters = false,
        onsortchange,
        onsearchchange,
        onclear,
    }: {
        search?: string | null;
        sort?: string;
        sortOptions?: { key: string; value: string }[];
        clearable?: boolean;
        hasActiveFilters?: boolean;
        onsortchange?: (value: string) => void;
        onsearchchange?: (value: string) => void;
        onclear?: () => void;
    } = $props();

    let sortOpen = $state(false);

    const { mdAndUp } = useDisplay();
</script>

<div class="flex items-center gap-2">
    <InputGroup.Root class="flex-1">
        <InputGroup.Addon>
            <Icon
                lucide={() => import('lucide-svelte/icons/search')}
                tabler={() => import('@tabler/icons-svelte/icons/search')}
                hugeicons={() =>
                    import('@hugeicons/core-free-icons/Search01Icon')}
                phosphor={() =>
                    import('phosphor-svelte/lib/MagnifyingGlassIcon')}
                remixicon={() => import('remixicon-svelte/icons/search-line')}
                class="text-muted-foreground size-4"
            />
        </InputGroup.Addon>
        <InputGroup.Input
            placeholder="Search anime..."
            bind:value={search}
            name="search"
            autocomplete="on"
            oninput={(e) =>
                onsearchchange?.((e.target as HTMLInputElement).value)}
        />
        {#if clearable && hasActiveFilters}
            <InputGroup.Addon align="inline-end">
                <InputGroup.Button
                    variant="ghost"
                    size="icon-xs"
                    onclick={() => onclear?.()}
                >
                    <Icon
                        lucide={() => import('lucide-svelte/icons/x')}
                        tabler={() => import('@tabler/icons-svelte/icons/x')}
                        hugeicons={() =>
                            import('@hugeicons/core-free-icons/Cancel01Icon')}
                        phosphor={() => import('phosphor-svelte/lib/XIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/close-line')}
                        class="size-3"
                    />
                </InputGroup.Button>
            </InputGroup.Addon>
        {/if}
    </InputGroup.Root>
    {#if mdAndUp.current}
        <Popover.Root bind:open={sortOpen}>
            <Popover.Trigger>
                {#snippet child({ props })}
                    <Button variant="outline" size="icon-sm" {...props}>
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/arrow-up-down')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/arrows-sort')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Sorting01Icon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/ArrowsDownUpIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/arrow-up-down-line')}
                            class="size-4"
                        />
                    </Button>
                {/snippet}
            </Popover.Trigger>
            <Popover.Content class="w-56 p-0" align="end">
                <Command.Root>
                    <Command.List>
                        {#each sortOptions as opt (opt.key)}
                            <Command.Item
                                class={`${sort === opt.key ? 'bg-accent text-accent-foreground font-medium hover:bg-accent/80' : ''} px-4`}
                                onSelect={() => {
                                    onsortchange?.(opt.key);
                                    sortOpen = false;
                                }}
                            >
                                <span class="flex-1">{opt.value}</span>
                                {#if sort === opt.key}
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/check')}
                                        tabler={() =>
                                            import('@tabler/icons-svelte/icons/check')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/Tick02Icon')}
                                        phosphor={() =>
                                            import('phosphor-svelte/lib/CheckIcon')}
                                        remixicon={() =>
                                            import('remixicon-svelte/icons/check-line')}
                                        class="size-3 text-accent-foreground"
                                    />
                                {/if}
                            </Command.Item>
                        {/each}
                    </Command.List>
                </Command.Root>
            </Popover.Content>
        </Popover.Root>
    {:else}
        <Drawer.Root bind:open={sortOpen}>
            <Drawer.Trigger>
                {#snippet child({ props })}
                    <Button variant="outline" size="icon-sm" {...props}>
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/arrow-up-down')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/arrows-sort')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Sorting01Icon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/ArrowsDownUpIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/arrow-up-down-line')}
                            class="size-4"
                        />
                    </Button>
                {/snippet}
            </Drawer.Trigger>
            <Drawer.Content>
                <div class="mt-4 border-t">
                    <Command.Root>
                        <Command.List>
                            {#each sortOptions as opt (opt.key)}
                                <Command.Item
                                    class={`${sort === opt.key ? 'bg-accent text-accent-foreground font-medium hover:bg-accent/80' : ''} px-4`}
                                    onSelect={() => {
                                        onsortchange?.(opt.key);
                                        sortOpen = false;
                                    }}
                                >
                                    <span class="flex-1">{opt.value}</span>
                                    {#if sort === opt.key}
                                        <Icon
                                            lucide={() =>
                                                import('lucide-svelte/icons/check')}
                                            tabler={() =>
                                                import('@tabler/icons-svelte/icons/check')}
                                            hugeicons={() =>
                                                import('@hugeicons/core-free-icons/Tick02Icon')}
                                            phosphor={() =>
                                                import('phosphor-svelte/lib/CheckIcon')}
                                            remixicon={() =>
                                                import('remixicon-svelte/icons/check-line')}
                                            class="size-3 text-accent-foreground"
                                        />
                                    {/if}
                                </Command.Item>
                            {/each}
                        </Command.List>
                    </Command.Root>
                </div>
            </Drawer.Content>
        </Drawer.Root>
    {/if}
</div>
