<script lang="ts">
    import { Search, ArrowUpDown, Check } from 'lucide-svelte';
    import { Button } from '@/components/ui/button';
    import * as Command from '@/components/ui/command/index.js';
    import { Input } from '@/components/ui/input';
    import * as Popover from '@/components/ui/popover/index.js';

    let {
        search = $bindable(''),
        sort = '',
        sortOptions = [],
        onsortchange,
        onsearchchange,
    }: {
        search?: string;
        sort?: string;
        sortOptions?: { key: string; value: string }[];
        onsortchange?: (value: string) => void;
        onsearchchange?: (value: string) => void;
    } = $props();

    let sortOpen = $state(false);
</script>

<div class="flex items-center gap-2">
    <form
        class="relative flex-1"
        onsubmit={(e) => e.preventDefault()}
        role="search"
    >
        <Search
            class="text-muted-foreground absolute left-3 top-1/2 size-4 -translate-y-1/2"
        />
        <Input
            class="pl-9"
            placeholder="Search anime..."
            value={search}
            name="search"
            autocomplete="on"
            oninput={(e) =>
                onsearchchange?.((e.target as HTMLInputElement).value)}
        />
    </form>
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
                                <Check class="size-3 text-muted-foreground" />
                            {/if}
                        </Command.Item>
                    {/each}
                </Command.List>
            </Command.Root>
        </Popover.Content>
    </Popover.Root>
</div>
