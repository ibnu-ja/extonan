<script lang="ts">
    import { Check, ChevronsUpDown, X } from 'lucide-svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Command from '@/components/ui/command/index.js';
    import * as Drawer from '@/components/ui/drawer/index.js';
    import * as Popover from '@/components/ui/popover/index.js';
    import type { LabelValue } from '@/lib/use-anime.svelte';
    import { useDisplay } from '@/lib/use-display.svelte';
    import { cn } from '@/lib/utils.js';

    let {
        label = '',
        items = [],
        selected = $bindable([]),
        placeholder = 'Search...',
        notFound = 'No results found.',
    }: {
        label?: string;
        items: LabelValue[];
        selected: string[];
        placeholder?: string;
        notFound?: string;
    } = $props();

    let open = $state(false);
    let search = $state('');

    const { mdAndUp } = useDisplay();

    const filtered = $derived(
        search
            ? items.filter((i) =>
                  i.key.toLowerCase().includes(search.toLowerCase()),
              )
            : items,
    );

    function isSelected(key: string): boolean {
        return selected.includes(key);
    }

    function toggle(key: string) {
        if (isSelected(key)) {
            selected = selected.filter((s) => s !== key);
        } else {
            selected = [...selected, key];
        }
    }
</script>

<div class="multi-combobox">
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
                        <div
                            class="flex flex-1 items-center gap-1 overflow-hidden"
                        >
                            {#if selected.length === 0}
                                <span class="text-muted-foreground truncate"
                                    >{label}</span
                                >
                            {:else}
                                {#each selected.slice(0, 2) as key (key)}
                                    <Badge
                                        variant="secondary"
                                        class="shrink-0 gap-1 max-w-28"
                                    >
                                        <span class="truncate"
                                            >{items.find((i) => i.key === key)
                                                ?.value ?? key}</span
                                        >
                                        <button
                                            type="button"
                                            class="ml-0.5 shrink-0 rounded-full outline-hidden hover:bg-muted-foreground/20"
                                            onclick={() => toggle(key)}
                                        >
                                            <X class="size-3" />
                                        </button>
                                    </Badge>
                                {/each}
                                {#if selected.length > 2}
                                    <Badge variant="secondary" class="shrink-0"
                                        >+{selected.length - 2}</Badge
                                    >
                                {/if}
                            {/if}
                        </div>
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
                        {#each filtered as item (item.key)}
                            <Command.Item
                                class={cn(
                                    'rounded-none px-4',
                                    isSelected(item.key) && 'bg-green-500/15',
                                )}
                                onSelect={() => toggle(item.key)}
                            >
                                <div class="flex w-full items-center gap-2">
                                    <div
                                        class="flex size-4 items-center justify-center"
                                    >
                                        {#if isSelected(item.key)}
                                            <Check class="size-4" />
                                        {/if}
                                    </div>
                                    <span class="flex-1">{item.value}</span>
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
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    class="w-full justify-between gap-2 text-base md:text-sm"
                >
                    <div class="flex flex-1 items-center gap-1 overflow-hidden">
                        {#if selected.length === 0}
                            <span class="text-muted-foreground truncate"
                                >{label}</span
                            >
                        {:else}
                            {#each selected.slice(0, 2) as key (key)}
                                <Badge
                                    variant="secondary"
                                    class="shrink-0 gap-1 max-w-28"
                                >
                                    <span class="truncate"
                                        >{items.find((i) => i.key === key)
                                            ?.value ?? key}</span
                                    >
                                    <button
                                        type="button"
                                        class="ml-0.5 shrink-0 rounded-full outline-hidden hover:bg-muted-foreground/20"
                                        onclick={() => toggle(key)}
                                    >
                                        <X class="size-3" />
                                    </button>
                                </Badge>
                            {/each}
                            {#if selected.length > 2}
                                <Badge variant="secondary" class="shrink-0"
                                    >+{selected.length - 2}</Badge
                                >
                            {/if}
                        {/if}
                    </div>
                    <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
                </Button>
            </Drawer.Trigger>
            <Drawer.Content>
                <div class="mt-4 border-t">
                    <Command.Root>
                        <Command.Input bind:value={search} {placeholder} />
                        <Command.List>
                            {#if filtered.length === 0}
                                <Command.Empty>{notFound}</Command.Empty>
                            {/if}
                            {#each filtered as item (item.key)}
                                <Command.Item
                                    class={cn(
                                        'rounded-none px-4',
                                        isSelected(item.key) &&
                                            'bg-green-500/15',
                                    )}
                                    onSelect={() => toggle(item.key)}
                                >
                                    <div class="flex w-full items-center gap-2">
                                        <div
                                            class="flex size-4 items-center justify-center"
                                        >
                                            {#if isSelected(item.key)}
                                                <Check class="size-4" />
                                            {/if}
                                        </div>
                                        <span class="flex-1">{item.value}</span>
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
