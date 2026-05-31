<script lang="ts" generics="T">
    import { Link, page, router } from '@inertiajs/svelte';
    import {
        ChevronLeft,
        ChevronRight,
        ChevronsLeft,
        ChevronsRight,
    } from 'lucide-svelte';
    import { Button } from '@/components/ui/button';
    import * as ButtonGroup from '@/components/ui/button-group';
    import * as Select from '@/components/ui/select';
    import { useDisplay } from '@/lib/use-display.svelte';

    let {
        data,
        only = [],
    }: {
        data: App.Data.PaginatedCollection<T>;
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

    function pageUrl(pageNum: number): string {
        const [path, search] = (page.url ?? '/').split('?');
        const params = new URLSearchParams(search);
        params.set('page', String(pageNum));

        return path + '?' + params.toString();
    }

    let userPerPage = $state<string>(
        typeof localStorage !== 'undefined'
            ? (localStorage.getItem('per_page') ?? String(data.perPage))
            : String(data.perPage),
    );

    function setCookie(name: string, value: string, days = 365) {
        const maxAge = days * 24 * 60 * 60;
        document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
    }

    function persistPerPage(val: string) {
        localStorage.setItem('per_page', val);
        setCookie('per_page', val);
    }

    const { smAndDown } = useDisplay();
    let editingPage = $state(false);
    let pageInput = $state('1');
    let inputEl: HTMLInputElement | undefined = $state();
    $effect(() => {
        if (editingPage) {
            inputEl?.focus();
        }
    });

    function goToPage() {
        const p = parseInt(pageInput, 10);

        if (p >= 1 && p <= lastPage) {
            router.get(pageUrl(p), undefined, linkOpts);
        }

        editingPage = false;
    }

    function navigatePerPage(val: string) {
        persistPerPage(val);

        const [path, search] = (page.url ?? '/').split('?');
        const params = new URLSearchParams(search);
        params.set('perPage', val);
        params.set('page', '1');
        router.get(path + '?' + params.toString(), undefined, linkOpts);
    }
</script>

{#if lastPage > 1 || total > perPage}
    <nav class="flex flex-col items-center gap-2 py-2">
        {#if smAndDown.current}
            <div class="flex items-center gap-1">
                <Button
                    size="icon"
                    disabled={currentPage <= 1}
                    variant="outline"
                >
                    {#snippet child({ props })}
                        <Link href={pageUrl(1)} {...linkOpts} {...props}>
                            <ChevronsLeft />
                        </Link>
                    {/snippet}
                </Button>
                <ButtonGroup.Root>
                    <Button
                        size="icon"
                        disabled={currentPage <= 1}
                        variant="outline"
                    >
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
                                bind:this={inputEl}
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
                <Button
                    size="icon"
                    disabled={currentPage >= lastPage}
                    variant="outline"
                >
                    {#snippet child({ props })}
                        <Link href={pageUrl(lastPage)} {...linkOpts} {...props}>
                            <ChevronsRight />
                        </Link>
                    {/snippet}
                </Button>
            </div>
        {:else}
            <div class="flex items-center gap-1">
                <Button
                    size="icon"
                    disabled={currentPage <= 1}
                    variant="outline"
                >
                    {#snippet child({ props })}
                        <Link href={pageUrl(1)} {...linkOpts} {...props}>
                            <ChevronsLeft />
                        </Link>
                    {/snippet}
                </Button>
                <Button
                    size="icon"
                    disabled={currentPage <= 1}
                    variant="outline"
                >
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
                    {#if editingPage}
                        <Button size="icon" variant="outline">
                            <input
                                type="number"
                                class="w-8 bg-transparent text-center text-sm outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                bind:value={pageInput}
                                bind:this={inputEl}
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
                            />
                        </Button>
                    {:else}
                        {#each pageLinks as link, i (i)}
                            {#if link.label === '...'}
                                <Button
                                    size="icon"
                                    variant="outline"
                                    onclick={() => {
                                        editingPage = true;
                                        pageInput = String(currentPage);
                                    }}>...</Button
                                >
                            {:else}
                                <Button
                                    size="icon"
                                    disabled={!link.url}
                                    variant={link.active
                                        ? 'default'
                                        : 'outline'}
                                >
                                    {#snippet child({ props })}
                                        <Link
                                            href={link.url ??
                                                pageUrl(Number(link.label))}
                                            {...linkOpts}
                                            {...props}
                                        >
                                            {link.label}
                                        </Link>
                                    {/snippet}
                                </Button>
                            {/if}
                        {/each}
                    {/if}
                </ButtonGroup.Root>
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
                <Button
                    size="icon"
                    disabled={currentPage >= lastPage}
                    variant="outline"
                >
                    {#snippet child({ props })}
                        <Link href={pageUrl(lastPage)} {...linkOpts} {...props}>
                            <ChevronsRight />
                        </Link>
                    {/snippet}
                </Button>
            </div>
        {/if}

        <div
            class="flex flex-col items-center gap-1 text-xs text-muted-foreground"
        >
            <span>page {currentPage} of {lastPage}</span>
            <div class="flex items-center gap-2">
                <span>Items per page</span>
                <Select.Root
                    type="single"
                    bind:value={userPerPage}
                    onValueChange={navigatePerPage}
                >
                    <Select.Trigger
                        class="h-7 w-14 border-border bg-background hover:bg-muted hover:text-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 rounded-lg border px-2.5 text-xs font-medium transition-colors focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-3 outline-none flex items-center justify-between gap-1"
                    >
                        {userPerPage}
                    </Select.Trigger>
                    <Select.Content class="min-w-(--bits-trigger-width)">
                        {#each [14, 25, 50, 100] as opt (opt)}
                            <Select.Item value={String(opt)}>{opt}</Select.Item>
                        {/each}
                    </Select.Content>
                </Select.Root>
            </div>
            <span>{from}–{to} of {total} items</span>
        </div>
    </nav>
{/if}
