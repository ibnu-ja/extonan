<script lang="ts">
    import { Search, ArrowUpDown, Check, X } from 'lucide-svelte';
    import { Button } from '@/components/ui/button';
    import * as Command from '@/components/ui/command/index.js';
    import * as Drawer from '@/components/ui/drawer/index.js';
    import * as InputGroup from '@/components/ui/input-group/index.js';
    import * as Popover from '@/components/ui/popover/index.js';
    import { useDisplay } from '@/lib/use-display.svelte';

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
            <Search class="text-muted-foreground size-4" />
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
                <InputGroup.Button variant="ghost" size="icon-xs" onclick={() => onclear?.()}>
                    <X class="size-3" />
                </InputGroup.Button>
            </InputGroup.Addon>
        {/if}
    </InputGroup.Root>
    {#if mdAndUp.current}
        <Popover.Root bind:open={sortOpen}>
            <Popover.Trigger>
                {#snippet child({ props })}
                    <Button variant="outline" size="icon-sm" {...props}>
                        <ArrowUpDown class="size-4" />
                    </Button>
                {/snippet}
            </Popover.Trigger>
            <Popover.Content class="w-56 p-0" align="end">
                <Command.Root>
                    <Command.List>
                        {#each sortOptions as opt (opt.key)}
                            <Command.Item
                                class={`${sort === opt.key ? 'bg-accent hover:bg-accent/80' : ''} px-4`}
                                onSelect={() => {
                                    onsortchange?.(opt.key);
                                    sortOpen = false;
                                }}
                            >
                                <span class="flex-1">{opt.value}</span>
                                {#if sort === opt.key}
                                    <Check
                                        class="size-3 text-muted-foreground"
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
                        <ArrowUpDown class="size-4" />
                    </Button>
                {/snippet}
            </Drawer.Trigger>
            <Drawer.Content>
                <div class="mt-4 border-t">
                    <Command.Root>
                        <Command.List>
                            {#each sortOptions as opt (opt.key)}
                                <Command.Item
                                    class={`${sort === opt.key ? 'bg-accent hover:bg-accent/80' : ''} px-4`}
                                    onSelect={() => {
                                        onsortchange?.(opt.key);
                                        sortOpen = false;
                                    }}
                                >
                                    <span class="flex-1">{opt.value}</span>
                                    {#if sort === opt.key}
                                        <Check
                                            class="size-3 text-muted-foreground"
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
