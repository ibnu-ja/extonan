<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import {
        ChevronLeft,
        ChevronRight,
        ChevronsLeft,
        ChevronsRight,
    } from 'lucide-svelte';
    import { Button } from '@/components/ui/button';
    import * as ButtonGroup from '@/components/ui/button-group';
    import * as Select from '@/components/ui/select';

    let {
        data,
        only = [],
    }: {
        data: App.Data.PaginatedCollection<any>;
        only?: string[];
    } = $props();

    let { currentPage, lastPage, perPage, total, links } = $derived(data);
    const linkOpts = $derived(only.length > 0 ? { only } : {});
    const from = $derived((currentPage - 1) * perPage + 1);
    const to = $derived(Math.min(currentPage * perPage, total));

    const pageLinks = $derived(
        links.filter(
            (l) => l.label !== '&laquo; Previous' && l.label !== 'Next &raquo;',
        ),
    );

    function pageUrl(page: number): string {
        const url = new URL(window.location.href);
        url.searchParams.set('page', String(page));

        return url.href;
    }

    let userPerPage = $state<string | undefined>(
        typeof localStorage !== 'undefined'
            ? (localStorage.getItem('perPage') ?? undefined)
            : undefined,
    );
    let selectValue = $derived(userPerPage ?? data.perPage);
    $effect(() => {
        if (typeof localStorage !== 'undefined') {
            localStorage.setItem('perPage', String(selectValue));
        }
    });
    let isMobile = $state(typeof window !== 'undefined' ? window.matchMedia('(max-width: 639px)').matches : false);
    $effect(() => {
        const mq = window.matchMedia('(max-width: 639px)');
        function onChange(e: MediaQueryListEvent) {
 isMobile = e.matches;
}
        mq.addEventListener('change', onChange);

        return () => mq.removeEventListener('change', onChange);
    });
    let editingPage = $state(false);
    let pageInput = $state('1');

    function goToPage() {
        const p = parseInt(pageInput, 10);

        if (p >= 1 && p <= lastPage) {
            import('@inertiajs/svelte').then(({ router }) =>
                router.get(pageUrl(p), undefined, linkOpts),
            );
        }

        editingPage = false;
    }

    function navigatePerPage(val: string) {
        const url = new URL(pageUrl(1));
        url.searchParams.set('perPage', val);
        url.searchParams.set('page', '1');
        import('@inertiajs/svelte').then(({ router }) =>
            router.get(url.href, undefined, linkOpts),
        );
    }
</script>

{#if lastPage > 1 || total > 14}
    <nav class="flex flex-col items-center gap-2 py-2">
        {#if isMobile}
            <div class="flex items-center gap-1">
                <Button size="icon" disabled={currentPage <= 1} variant="outline">
                    {#snippet child({ props })}
                        <Link
                            href={pageUrl(1)}
                            {...linkOpts}
                            {...props}
                        >
                            <ChevronsLeft />
                        </Link>
                    {/snippet}
                </Button>
                <ButtonGroup.Root>
                    <Button size="icon" disabled={currentPage <= 1} variant="outline">
                        {#snippet child({ props })}
                            <Link
                                href={pageUrl(currentPage - 1)}
                                {...linkOpts}
                                {...props}
                            >
                                <ChevronLeft />
                            </Link>
                        {/snippet}
                    </Button>
                    {#if editingPage}
                        <Button size="icon" variant="outline">
                            <input
                                type="number"
                                class="w-8 bg-transparent text-center text-sm outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                bind:value={pageInput}
                                onkeydown={(e) => {
                                    if (e.key === 'Enter') {
                                        e.preventDefault();
                                        goToPage();
                                    }

                                    if (e.key === 'Escape') {
                                        editingPage = false;
                                        pageInput = String(currentPage);
                                    }
                                }}
                                onblur={() => {
                                    editingPage = false;
                                    pageInput = String(currentPage);
                                }}
                                autofocus
                            />
                        </Button>
                    {:else}
                        <Button
                            size="icon"
                            variant="outline"
                            onclick={() => {
                                editingPage = true;
                                pageInput = String(currentPage);
                            }}
                        >
                            {currentPage}
                        </Button>
                    {/if}
                    <Button
                        size="icon"
                        disabled={currentPage >= lastPage}
                        variant="outline"
                    >
                        {#snippet child({ props })}
                            <Link
                                href={pageUrl(currentPage + 1)}
                                {...linkOpts}
                                {...props}
                            >
                                <ChevronRight />
                            </Link>
                        {/snippet}
                    </Button>
                </ButtonGroup.Root>
                <Button size="icon" disabled={currentPage >= lastPage} variant="outline">
                    {#snippet child({ props })}
                        <Link
                            href={pageUrl(lastPage)}
                            {...linkOpts}
                            {...props}
                        >
                            <ChevronsRight />
                        </Link>
                    {/snippet}
                </Button>
            </div>
        {:else}
            <div class="flex items-center gap-1">
                <Button size="icon" disabled={currentPage <= 1} variant="outline">
                    {#snippet child({ props })}
                        <Link
                            href={pageUrl(1)}
                            {...linkOpts}
                            {...props}
                        >
                            <ChevronsLeft />
                        </Link>
                    {/snippet}
                </Button>
                <Button size="icon" disabled={currentPage <= 1} variant="outline">
                    {#snippet child({ props })}
                        <Link
                            href={pageUrl(currentPage - 1)}
                            {...linkOpts}
                            {...props}
                        >
                            <ChevronLeft />
                        </Link>
                    {/snippet}
                </Button>
                <ButtonGroup.Root>
                    {#each pageLinks as link, i (i)}
                        {#if link.label === '...'}
                            <Button size="icon" disabled variant="outline"
                                >...</Button
                            >
                        {:else}
                            <Button
                                size="icon"
                                disabled={!link.url}
                                variant={link.active ? 'default' : 'outline'}
                            >
                                {#snippet child({ props })}
                                    <Link
                                        href={link.url ?? pageUrl(Number(link.label))}
                                        {...linkOpts}
                                        {...props}
                                    >
                                        {link.label}
                                    </Link>
                                {/snippet}
                            </Button>
                        {/if}
                    {/each}
                </ButtonGroup.Root>
                <Button size="icon" disabled={currentPage >= lastPage} variant="outline">
                    {#snippet child({ props })}
                        <Link
                            href={pageUrl(currentPage + 1)}
                            {...linkOpts}
                            {...props}
                        >
                            <ChevronRight />
                        </Link>
                    {/snippet}
                </Button>
                <Button size="icon" disabled={currentPage >= lastPage} variant="outline">
                    {#snippet child({ props })}
                        <Link
                            href={pageUrl(lastPage)}
                            {...linkOpts}
                            {...props}
                        >
                            <ChevronsRight />
                        </Link>
                    {/snippet}
                </Button>
            </div>
        {/if}

        <div
            class="flex flex-col items-center gap-1 text-xs text-muted-foreground"
        >
            <span>{from}–{to} of {total}</span>
            <div class="flex items-center gap-2">
                <span>Items per page</span>
                <Select.Root
                    type="single"
                    bind:value={userPerPage}
                    onValueChange={navigatePerPage}
                >
                    <Select.Trigger class="h-7 w-14 text-xs">
                        {selectValue}
                    </Select.Trigger>
                    <Select.Content class="min-w-(--bits-trigger-width)">
                        {#each [14, 25, 50, 100] as opt (opt)}
                            <Select.Item value={String(opt)}>{opt}</Select.Item>
                        {/each}
                    </Select.Content>
                </Select.Root>
            </div>
            <span>page {currentPage} of {lastPage}</span>
        </div>
    </nav>
{/if}
