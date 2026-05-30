<script lang="ts">
    import { Check, X } from 'lucide-svelte';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Command from '@/components/ui/command/index.js';
    import * as Popover from '@/components/ui/popover/index.js';

    let {
        label = '',
        items = [],
        selectedIn = [],
        selectedNotIn = [],
        activeKey = '',
        singleSelect = false,
        icon: Icon,
        onselect,
    }: {
        label?: string;
        items: string[];
        selectedIn?: string[];
        selectedNotIn?: string[];
        activeKey?: string;
        singleSelect?: boolean;
        icon?: any;
        onselect?: (item: string, mode: 'in' | 'notIn' | 'none') => void;
    } = $props();

    let open = $state(false);
    let search = $state('');

    const filtered = $derived(
        search
            ? items.filter((i) =>
                  i.toLowerCase().includes(search.toLowerCase()),
              )
            : items,
    );

    const activeCount = $derived(selectedIn.length + selectedNotIn.length);

    function getItemMode(item: string): 'in' | 'notIn' | 'none' {
        if (selectedIn.includes(item)) {
            return 'in';
        }

        if (selectedNotIn.includes(item)) {
            return 'notIn';
        }

        return 'none';
    }

    function cycleMode(item: string) {
        const mode = getItemMode(item);

        if (mode === 'none') {
            onselect?.(item, 'in');
        } else if (mode === 'in') {
            onselect?.(item, 'notIn');
        } else {
            onselect?.(item, 'none');
        }
    }
</script>

<Popover.Root bind:open>
    <Popover.Trigger>
        {#snippet child({ props })}
            <Button variant="outline" size="sm" {...props} class="gap-1">
                {#if Icon}<Icon class="size-4 shrink-0" />{/if}
                {singleSelect && activeKey ? activeKey : label}
                {#if !singleSelect && activeCount > 0}
                    <Badge
                        variant="secondary"
                        class="ml-1 size-5 rounded-full p-0 text-xs"
                        >{activeCount}</Badge
                    >
                {/if}
            </Button>
        {/snippet}
    </Popover.Trigger>
    <Popover.Content class="w-64 p-0" align="start">
        <Command.Root>
            <Command.Input
                bind:value={search}
                placeholder={`Search ${label.toLowerCase()}...`}
            />
            <Command.List>
                {#if filtered.length === 0}
                    <Command.Empty
                        >No {label.toLowerCase()} found.</Command.Empty
                    >
                {/if}
                {#each filtered as item (item)}
                    <Command.Item
                        class={`${getItemMode(item) === 'in' ? 'bg-emerald-500/10 hover:bg-emerald-500/20 aria-selected:bg-emerald-500/20' : getItemMode(item) === 'notIn' ? 'bg-red-500/10 hover:bg-red-500/20 aria-selected:bg-red-500/20' : ''} rounded-none px-4`}
                        onSelect={() => cycleMode(item)}
                    >
                        <div class="flex w-full items-center gap-2">
                            {#if getItemMode(item) === 'in'}
                                <Check class="size-4 text-emerald-500" />
                            {:else if getItemMode(item) === 'notIn'}
                                <X class="size-4 text-red-500" />
                            {/if}
                            <span class="flex-1">{item}</span>
                        </div>
                    </Command.Item>
                {/each}
            </Command.List>
        </Command.Root>
    </Popover.Content>
</Popover.Root>
