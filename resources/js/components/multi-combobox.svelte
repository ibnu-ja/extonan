<script lang="ts">
    import { Check, ChevronsUpDown } from 'lucide-svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Command from '@/components/ui/command/index.js';
    import * as Drawer from '@/components/ui/drawer/index.js';
    import * as Popover from '@/components/ui/popover/index.js';
    import { useDisplay } from '@/lib/use-display.svelte';
    import { cn } from '@/lib/utils.js';

    type Item = { key?: string; value?: string; name?: string };

    let {
        label = '',
        items = [],
        selected = $bindable([]),
        placeholder = 'Search...',
        notFound = 'No results found.',
    }: {
        label?: string;
        items: Item[];
        selected: (Item | string)[];
        placeholder?: string;
        notFound?: string;
    } = $props();

    let norm = $derived(
        selected.map((s) => (typeof s === 'string' ? { name: s } : s)),
    );

    function itemKey(item: Item): string {
        return item.name ?? item.key ?? '';
    }

    function itemLabel(item: Item): string {
        return item.name ?? item.value ?? itemKey(item);
    }

    let open = $state(false);
    let search = $state('');

    const { mdAndUp } = useDisplay();

    const filtered = $derived(
        search
            ? items.filter((i) =>
                  itemLabel(i).toLowerCase().includes(search.toLowerCase()),
              )
            : items,
    );

    function isSelected(key: string): boolean {
        return norm.some((s) => s.name === key);
    }

    function toggle(key: string) {
        const item = items.find((i) => itemKey(i) === key);

        if (!item) {
            return;
        }

        if (isSelected(key)) {
            selected = selected.filter((s) => {
                const name = typeof s === 'string' ? s : s.name;

                return name !== key;
            });
        } else {
            selected = [...selected, item];
        }
    }
</script>

<div class="w-full">
    {#if mdAndUp.current}
        <Popover.Root bind:open>
            <Popover.Trigger>
                {#snippet child({ props })}
                    <Button
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        {...props}
                        class="w-full justify-between gap-2 text-base md:text-sm"
                    >
                        {#if norm.length === 0}
                            <span class="text-muted-foreground truncate">
                                {label}
                            </span>
                        {:else}
                            <div class="flex items-center gap-1 truncate">
                                <span class="truncate">
                                    {itemLabel(norm[0])}
                                </span>
                                {#if norm.length > 1}
                                    <Badge
                                        variant="secondary"
                                        size="xs"
                                        class="shrink-0"
                                    >
                                        +{norm.length - 1}
                                    </Badge>
                                {/if}
                            </div>
                        {/if}
                        <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
                    </Button>
                {/snippet}
            </Popover.Trigger>
            <Popover.Content
                class="w-(--bits-popover-anchor-width) min-w-64 p-0"
                align="start"
                side="bottom"
            >
                <Command.Root>
                    <Command.Input bind:value={search} {placeholder} />
                    <Command.List class="max-h-72 overflow-y-auto">
                        {#if filtered.length === 0}
                            <Command.Empty>{notFound}</Command.Empty>
                        {/if}
                        {#each filtered as item (itemKey(item))}
                            <Command.Item
                                class={cn(
                                    'rounded-none px-4',
                                    isSelected(itemKey(item)) &&
                                        'bg-green-500/15',
                                )}
                                onSelect={() => toggle(itemKey(item))}
                            >
                                <div class="flex w-full items-center gap-2">
                                    <div
                                        class="flex size-4 items-center justify-center"
                                    >
                                        {#if isSelected(itemKey(item))}
                                            <Check class="size-4" />
                                        {/if}
                                    </div>
                                    <span class="flex-1">
                                        {itemLabel(item)}
                                    </span>
                                </div>
                            </Command.Item>
                        {/each}
                    </Command.List>
                </Command.Root>
            </Popover.Content>
        </Popover.Root>
    {:else}
        <Drawer.Root bind:open>
            <Drawer.Trigger>
                {#snippet child({ props })}
                    <Button
                        variant="outline"
                        role="combobox"
                        aria-expanded={open}
                        {...props}
                        class="w-full justify-between gap-2 text-base md:text-sm"
                    >
                        {#if norm.length === 0}
                            <span class="text-muted-foreground truncate">
                                {label}
                            </span>
                        {:else}
                            <div class="flex items-center gap-1 truncate">
                                <span class="truncate"
                                    >{itemLabel(norm[0])}</span
                                >
                                {#if norm.length > 1}
                                    <Badge
                                        variant="secondary"
                                        size="xs"
                                        class="shrink-0"
                                    >
                                        +{norm.length - 1}
                                    </Badge>
                                {/if}
                            </div>
                        {/if}
                        <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
                    </Button>
                {/snippet}
            </Drawer.Trigger>
            <Drawer.Content>
                <div class="mt-4 border-t">
                    <Command.Root>
                        <Command.Input bind:value={search} {placeholder} />
                        <Command.List>
                            {#if filtered.length === 0}
                                <Command.Empty>{notFound}</Command.Empty>
                            {/if}
                            {#each filtered as item (itemKey(item))}
                                <Command.Item
                                    class={cn(
                                        'rounded-none px-4',
                                        isSelected(itemKey(item)) &&
                                            'bg-green-500/15',
                                    )}
                                    onSelect={() => toggle(itemKey(item))}
                                >
                                    <div class="flex w-full items-center gap-2">
                                        <div
                                            class="flex size-4 items-center justify-center"
                                        >
                                            {#if isSelected(itemKey(item))}
                                                <Check class="size-4" />
                                            {/if}
                                        </div>
                                        <span class="flex-1">
                                            {itemLabel(item)}
                                        </span>
                                    </div>
                                </Command.Item>
                            {/each}
                        </Command.List>
                    </Command.Root>
                </div>
            </Drawer.Content>
        </Drawer.Root>
    {/if}
</div>
