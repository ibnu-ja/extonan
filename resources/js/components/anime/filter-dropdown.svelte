<script lang="ts">
    import { Icon } from '@/components/icon';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Command from '@/components/ui/command';
    import * as Drawer from '@/components/ui/drawer';
    import * as Popover from '@/components/ui/popover';
    import { useDisplay } from '@/lib/use-display.svelte.js';
    import { cn } from '@/lib/utils.js';

    let {
        label = '',
        items = [],
        selectedIn = [],
        selectedNotIn = [],
        activeKey = '',
        singleSelect = false,
        sortActive = false,
        clearable = true,
        icon: iconComponent,
        onselect,
        onclear,
    }: {
        label?: string;
        items: string[];
        selectedIn?: string[];
        selectedNotIn?: string[];
        activeKey?: string;
        singleSelect?: boolean;
        sortActive?: boolean;
        clearable?: boolean;
        icon?: any;
        onselect?: (item: string, mode: 'in' | 'notIn' | 'none') => void;
        onclear?: () => void;
    } = $props();

    let open = $state(false);
    let search = $state('');
    let sorted = $state<string[]>([]);

    const { mdAndUp } = useDisplay();

    $effect(() => {
        if (open) {
            return;
        }

        if (sortActive && items.length > 0) {
            const active = items.filter(
                (i) => selectedIn.includes(i) || selectedNotIn.includes(i),
            );
            const inactive = items.filter(
                (i) => !selectedIn.includes(i) && !selectedNotIn.includes(i),
            );
            sorted = [...active, ...inactive];
        } else {
            sorted = items;
        }
    });

    const filtered = $derived(
        search
            ? sorted.filter((i) =>
                  i.toLowerCase().includes(search.toLowerCase()),
              )
            : sorted,
    );

    const activeCount = $derived(
        singleSelect
            ? activeKey
                ? 1
                : 0
            : selectedIn.length + selectedNotIn.length,
    );

    function getItemMode(item: string): 'in' | 'notIn' | 'none' {
        if (singleSelect) {
            return activeKey === item ? 'in' : 'none';
        }

        if (selectedIn.includes(item)) {
            return 'in';
        }

        if (selectedNotIn.includes(item)) {
            return 'notIn';
        }

        return 'none';
    }

    function cycleMode(item: string) {
        if (singleSelect) {
            const current = activeKey === item;
            onselect?.(item, current ? 'none' : 'in');
            open = false;

            return;
        }

        const current = getItemMode(item);
        const next: 'in' | 'notIn' | 'none' =
            current === 'none' ? 'in' : current === 'in' ? 'notIn' : 'none';
        onselect?.(item, next);
    }
</script>

{#if mdAndUp.current}
    <Popover.Root bind:open>
        <Popover.Trigger>
            {#snippet child({ props })}
                <Button variant="outline" size="sm" {...props} class="gap-1">
                    {#if iconComponent}
                        {@const IconComp = iconComponent}
                        <IconComp class="size-4 shrink-0" />
                    {/if}
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
                <div class="relative">
                    <Command.Input
                        bind:value={search}
                        placeholder={`Search ${label.toLowerCase()}...`}
                    />
                    {#if clearable && activeCount > 0}
                        <Button
                            variant="ghost"
                            size="icon-xs"
                            class="absolute right-1 top-1/2 -translate-y-1/2"
                            onclick={() => onclear?.()}
                        >
                            <Icon
                                lucide="XIcon"
                                tabler="IconX"
                                hugeicons="Cancel01Icon"
                                phosphor="XIcon"
                                remixicon="RiCloseLine"
                                class="size-3"
                            />
                        </Button>
                    {/if}
                </div>
                <Command.List>
                    {#if filtered.length === 0}
                        <Command.Empty
                            >No {label.toLowerCase()} found.</Command.Empty
                        >
                    {/if}
                    {#each filtered as item (item)}
                        <Command.Item
                            class={cn(
                                'px-4',
                                getItemMode(item) === 'in'
                                    ? 'bg-primary/10 text-primary font-medium hover:bg-primary/20 aria-selected:bg-primary/20'
                                    : getItemMode(item) === 'notIn'
                                      ? 'bg-destructive/10 text-destructive font-medium hover:bg-destructive/20 aria-selected:bg-destructive/20'
                                      : '',
                            )}
                            onSelect={() => cycleMode(item)}
                        >
                            <div class="flex w-full items-center gap-2">
                                {#if getItemMode(item) === 'in'}
                                    <Icon
                                        lucide="CheckIcon"
                                        tabler="IconCheck"
                                        hugeicons="Tick02Icon"
                                        phosphor="CheckIcon"
                                        remixicon="RiCheckLine"
                                        class="size-4 text-primary"
                                    />
                                {:else if getItemMode(item) === 'notIn'}
                                    <Icon
                                        lucide="XIcon"
                                        tabler="IconX"
                                        hugeicons="Cancel01Icon"
                                        phosphor="XIcon"
                                        remixicon="RiCloseLine"
                                        class="size-4 text-destructive"
                                    />
                                {/if}
                                <span class="flex-1">{item}</span>
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
                <Button variant="outline" size="sm" {...props} class="gap-1">
                    {#if iconComponent}
                        {@const IconComp = iconComponent}
                        <IconComp class="size-4 shrink-0" />
                    {/if}
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
        </Drawer.Trigger>
        <Drawer.Content>
            <div class="mt-4 border-t">
                <Command.Root>
                    <div class="relative">
                        <Command.Input
                            bind:value={search}
                            placeholder={`Search ${label.toLowerCase()}...`}
                        />
                        {#if clearable && activeCount > 0}
                            <Button
                                variant="ghost"
                                size="icon-xs"
                                class="absolute right-1 top-1/2 -translate-y-1/2"
                                onclick={() => onclear?.()}
                            >
                                <Icon
                                    lucide="XIcon"
                                    tabler="IconX"
                                    hugeicons="Cancel01Icon"
                                    phosphor="XIcon"
                                    remixicon="RiCloseLine"
                                    class="size-3"
                                />
                            </Button>
                        {/if}
                    </div>
                    <Command.List>
                        {#if filtered.length === 0}
                            <Command.Empty
                                >No {label.toLowerCase()} found.</Command.Empty
                            >
                        {/if}
                        {#each filtered as item (item)}
                            <Command.Item
                                class={cn(
                                    'px-4',
                                    getItemMode(item) === 'in'
                                        ? 'bg-primary/10 text-primary font-medium hover:bg-primary/20 aria-selected:bg-primary/20'
                                        : getItemMode(item) === 'notIn'
                                          ? 'bg-destructive/10 text-destructive font-medium hover:bg-destructive/20 aria-selected:bg-destructive/20'
                                          : '',
                                )}
                                onSelect={() => cycleMode(item)}
                            >
                                <div class="flex w-full items-center gap-2">
                                    {#if getItemMode(item) === 'in'}
                                        <Icon
                                            lucide="CheckIcon"
                                            tabler="IconCheck"
                                            hugeicons="Tick02Icon"
                                            phosphor="CheckIcon"
                                            remixicon="RiCheckLine"
                                            class="size-4 text-primary"
                                        />
                                    {:else if getItemMode(item) === 'notIn'}
                                        <Icon
                                            lucide="XIcon"
                                            tabler="IconX"
                                            hugeicons="Cancel01Icon"
                                            phosphor="XIcon"
                                            remixicon="RiCloseLine"
                                            class="size-4 text-destructive"
                                        />
                                    {/if}
                                    <span class="flex-1">{item}</span>
                                </div>
                            </Command.Item>
                        {/each}
                    </Command.List>
                </Command.Root>
            </div>
        </Drawer.Content>
    </Drawer.Root>
{/if}
