<script lang="ts">
    import ExternalLink from 'lucide-svelte/icons/external-link';
    import type { Snippet } from 'svelte';
    import * as Breadcrumb from '@/components/ui/breadcrumb';
    import { Button } from '@/components/ui/button';
    import * as Dialog from '@/components/ui/dialog';
    import { Separator } from '@/components/ui/separator';
    import * as Sidebar from '@/components/ui/sidebar';

    let {
        open = $bindable(false),
        headerTitle = '',
        onreset,
        sidebarInfo,
        sidebarNav,
        selectedMedia = null,
        ondeleteMedia,
        children,
    }: {
        open?: boolean;
        headerTitle?: string;
        onreset?: () => void;
        sidebarInfo?: Snippet;
        sidebarNav?: Snippet;
        selectedMedia?: App.Data.MediaData | null;
        ondeleteMedia?: (media: App.Data.MediaData) => void;
        children?: Snippet;
    } = $props();
</script>

<Dialog.Root bind:open>
    <Dialog.Content
        class="fixed top-0 left-0 translate-none m-0 h-full w-full max-w-none rounded-none border-0 ring-0 gap-0 p-0 overflow-hidden sm:top-1/2 sm:left-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2 sm:m-auto sm:h-[500px] sm:max-w-[700px] sm:rounded-xl sm:border sm:ring-1 lg:max-w-[800px]"
        trapFocus={false}
    >
        <Dialog.Title class="sr-only">Media Manager</Dialog.Title>
        <Dialog.Description class="sr-only"
            >Manage your media files</Dialog.Description
        >
        <Sidebar.Provider class="items-start min-h-0 h-full">
            <Sidebar.Root collapsible="offcanvas">
                <Sidebar.Content>
                    {#if sidebarInfo}
                        <Sidebar.Group>
                            <Sidebar.GroupContent>
                                {@render sidebarInfo()}
                            </Sidebar.GroupContent>
                        </Sidebar.Group>
                    {:else if selectedMedia}
                        <Sidebar.Group>
                            <Sidebar.GroupContent>
                                <div class="p-2">
                                    <h5 class="font-heading text-lg font-semibold">Media Details</h5>
                                    <ul class="mt-2 space-y-1 text-sm">
                                        <li>
                                            <span class="font-medium">Filename: </span>
                                            <span class="text-muted-foreground break-all">{selectedMedia.filename}.{selectedMedia.extension}</span>
                                        </li>
                                        <li>
                                            <span class="font-medium">Created: </span>
                                            <span class="text-muted-foreground">{selectedMedia.createdAt}</span>
                                        </li>
                                        <li>
                                            <span class="font-medium">Size: </span>
                                            <span class="text-muted-foreground">{selectedMedia.size} bytes</span>
                                        </li>
                                    </ul>
                                    {#if ondeleteMedia}
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            class="mt-2 w-full"
                                            onclick={() => ondeleteMedia(selectedMedia)}
                                        >
                                            Delete
                                        </Button>
                                    {/if}
                                    {#if selectedMedia.url}
                                        <div class="mt-2 space-y-1">
                                            <p class="text-xs font-medium text-muted-foreground">Links</p>
                                            <a
                                                href={selectedMedia.url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1 text-xs text-primary underline-offset-4 hover:underline"
                                            >
                                                <ExternalLink class="size-3 shrink-0" />
                                                original
                                            </a>
                                            {#if selectedMedia.mediumUrl}
                                                <a
                                                    href={selectedMedia.mediumUrl}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-1 text-xs text-primary underline-offset-4 hover:underline"
                                                >
                                                    <ExternalLink class="size-3 shrink-0" />
                                                    medium
                                                </a>
                                            {/if}
                                            {#if selectedMedia.largeUrl}
                                                <a
                                                    href={selectedMedia.largeUrl}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="inline-flex items-center gap-1 text-xs text-primary underline-offset-4 hover:underline"
                                                >
                                                    <ExternalLink class="size-3 shrink-0" />
                                                    large
                                                </a>
                                            {/if}
                                        </div>
                                    {/if}
                                </div>
                            </Sidebar.GroupContent>
                        </Sidebar.Group>
                    {:else}
                        <Sidebar.Group>
                            <Sidebar.GroupContent>
                                <div class="p-2">
                                    <h5 class="font-heading text-lg font-semibold">Media Details</h5>
                                    <p class="mt-2 text-sm text-muted-foreground">No file selected</p>
                                </div>
                            </Sidebar.GroupContent>
                        </Sidebar.Group>
                    {/if}
                    <Separator />
                    {#if sidebarNav}
                        <Sidebar.Group>
                            <Sidebar.GroupLabel
                                >Uploaded Months</Sidebar.GroupLabel
                            >
                            <Sidebar.GroupContent>
                                {@render sidebarNav()}
                            </Sidebar.GroupContent>
                        </Sidebar.Group>
                    {/if}
                </Sidebar.Content>
                <Sidebar.Rail />
            </Sidebar.Root>
            <main class="flex h-full flex-1 flex-col overflow-hidden">
                <header
                    class="flex h-12 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12"
                >
                    <div class="flex items-center gap-2 px-4">
                        <Sidebar.Trigger />
                        <Breadcrumb.Root>
                            <Breadcrumb.List>
                                <Breadcrumb.Item>
                                    <Breadcrumb.Link
                                        href="##"
                                        onclick={(e) => {
                                            e.preventDefault();
                                            onreset?.();
                                        }}
                                    >
                                        Media
                                    </Breadcrumb.Link>
                                </Breadcrumb.Item>
                                <Breadcrumb.Separator />
                                <Breadcrumb.Item>
                                    <Breadcrumb.Page>{headerTitle}</Breadcrumb.Page>
                                </Breadcrumb.Item>
                            </Breadcrumb.List>
                        </Breadcrumb.Root>
                    </div>
                </header>
                <div
                    class="flex flex-1 flex-col gap-4 overflow-y-auto p-4 pt-0"
                >
                    {@render children?.()}
                </div>
            </main>
        </Sidebar.Provider>
    </Dialog.Content>
</Dialog.Root>
