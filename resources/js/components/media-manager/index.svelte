<script lang="ts">
    import type { Snippet } from 'svelte';
    import { index as mediaIndex, getMonthsWithCounts as mediaMonthsWithCounts, destroy as mediaDestroy } from '@/actions/App/Http/Controllers/MediaController';
    import * as AlertDialog from '@/components/ui/alert-dialog';
    import * as Sidebar from '@/components/ui/sidebar';
    import * as Tabs from '@/components/ui/tabs';
    import MediaRemoteUploader from './media-remote-uploader.svelte';
    import MediaSelector from './media-selector.svelte';
    import MediaUploader from './media-uploader.svelte';
    import { MediaManager, MediaManagerTrigger, MediaManagerContent } from '.';

    let {
        value = $bindable(null),
        multiple = false,
        title = 'Add Media',
        disabled = false,
        header,
        _trigger,
        preview,
        empty,
        _sidebarNav,
        tabs,
        deleteDialog,
    }: {
        value?: App.Data.MediaData | App.Data.MediaData[] | null;
        multiple?: boolean;
        title?: string;
        disabled?: boolean;
        header?: Snippet<[{ title: string; onOpen: () => void }]>;
        _trigger?: Snippet<[{ hasValue: boolean; onclick: () => void }]>;
        preview?: Snippet<[{ value: App.Data.MediaData | App.Data.MediaData[] | null }]>;
        empty?: Snippet;
        _sidebarInfo?: Snippet;
        _sidebarNav?: Snippet;
        tabs?: Snippet;
        deleteDialog?: Snippet;
    } = $props();

    let dialogOpen = $state(false);
    let selectedMonth = $state('');
    let monthsWithCounts = $state<{ month: string; count: number }[]>([]);
    let latestSelected = $state<App.Data.MediaData | null>(null);
    let deleteTarget = $state<App.Data.MediaData | null>(null);
    type MediaResponse = App.Data.PaginatedCollection<App.Data.MediaData>;

    let media = $state<MediaResponse | null>(null);

    async function fetchMedia() {
        const query: Record<string, string> = {};

        if (selectedMonth) {
            query['filter[month]'] = selectedMonth;
        }

        const response = await fetch(mediaIndex.url({ query } as any));
        media = await response.json();
    }

    async function fetchMonthsWithCounts() {
        const response = await fetch(mediaMonthsWithCounts.url());
        monthsWithCounts = await response.json();
    }

    async function confirmDelete() {
        if (!deleteTarget) {
            return;
        }

        await fetch(mediaDestroy.url(deleteTarget.id), { method: 'DELETE' });
        await fetchMedia();
        await fetchMonthsWithCounts();

        if (latestSelected?.id === deleteTarget.id) {
            latestSelected = null;
        }

        deleteTarget = null;
    }

    function openDialog() {
        dialogOpen = true;
        selectedMonth = '';
        latestSelected = value != null ? (Array.isArray(value) ? value[0] ?? null : value) : null;
        fetchMonthsWithCounts();
        fetchMedia();
    }

    function selectMonth(month: string) {
        selectedMonth = selectedMonth === month ? '' : month;
    }

    $effect(() => {
        if (dialogOpen) {
            fetchMedia();
        }
    });
</script>

<MediaManager {title} {value} {header} {preview} {empty} onOpen={openDialog}>
    {#snippet trigger({ hasValue })}
        {#if _trigger}
            {@render _trigger({ hasValue, onclick: openDialog })}
        {:else}
            <MediaManagerTrigger {hasValue} onclick={openDialog} {disabled} />
        {/if}
    {/snippet}
</MediaManager>

<MediaManagerContent
    bind:open={dialogOpen}
    headerTitle={selectedMonth || 'All'}
    onreset={() => {
 selectedMonth = ''; 
}}
    selectedMedia={latestSelected}
    ondeleteMedia={(item) => (deleteTarget = item)}
>
    {#snippet sidebarNav()}
        {#if _sidebarNav}
            {@render _sidebarNav()}
        {:else}
            <Sidebar.Menu>
                {#each monthsWithCounts as { month, count } (month)}
                    <Sidebar.MenuItem>
                        <Sidebar.MenuButton
                            isActive={selectedMonth === month}
                            onclick={() => selectMonth(month)}
                        >
                            {#snippet child({ props })}
                                <a href="##" {...props}>
                                    <span>{month}</span>
                                    <span class="ms-auto text-xs text-muted-foreground">{count}</span>
                                </a>
                            {/snippet}
                        </Sidebar.MenuButton>
                    </Sidebar.MenuItem>
                {/each}
            </Sidebar.Menu>
        {/if}
    {/snippet}

    {#if tabs}
        {@render tabs()}
    {:else}
        <Tabs.Root value="upload">
            <Tabs.List>
                <Tabs.Trigger value="upload">Upload File(s)</Tabs.Trigger>
                <Tabs.Trigger value="remote">Remote Upload</Tabs.Trigger>
            </Tabs.List>

            <Tabs.Content value="upload" class="space-y-4">
                <MediaUploader
                    {multiple}
                    onuploaded={() => {
                        fetchMedia();
                        fetchMonthsWithCounts();
                    }}
                />

                <MediaSelector
                    bind:value
                    {media}
                    {multiple}
                    onselected={(item) => (latestSelected = item)}
                />
            </Tabs.Content>

            <Tabs.Content value="remote" class="space-y-4">
                <MediaRemoteUploader
                    onuploaded={() => {
                        fetchMedia();
                        fetchMonthsWithCounts();
                    }}
                />

                <MediaSelector
                    bind:value
                    {media}
                    {multiple}
                    onselected={(item) => (latestSelected = item)}
                />
            </Tabs.Content>
        </Tabs.Root>
    {/if}
</MediaManagerContent>

{#if deleteDialog}
    {@render deleteDialog()}
{:else}
    <AlertDialog.Root
        open={deleteTarget !== null}
        onOpenChange={(open) => {
            if (!open) {
                deleteTarget = null;
            }
        }}
    >
        <AlertDialog.Portal>
            <AlertDialog.Overlay />
            <AlertDialog.Content>
                <AlertDialog.Header>
                    <AlertDialog.Title>Are you sure?</AlertDialog.Title>
                    <AlertDialog.Description>
                        This action cannot be undone. This will permanently delete this media item.
                    </AlertDialog.Description>
                </AlertDialog.Header>
                <AlertDialog.Footer>
                    <AlertDialog.Cancel>Cancel</AlertDialog.Cancel>
                    <AlertDialog.Action onclick={confirmDelete}>Delete</AlertDialog.Action>
                </AlertDialog.Footer>
            </AlertDialog.Content>
        </AlertDialog.Portal>
    </AlertDialog.Root>
{/if}
