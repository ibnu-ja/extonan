<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'Playground', href: '/playground' }],
    };
</script>

<script lang="ts">
    import {
        Share,
        Trash2,
        Plus,
        X,
        FilePen,
        Download,
        Eye,
    } from 'lucide-svelte';
    import MediaManager from '@/components/media-manager/index.svelte';
    import MediaManagerTrigger from '@/components/media-manager/media-manager-trigger.svelte';
    import * as SpeedDial from '@/components/ui/speed-dial/index.js';

    let singleValue = $state<App.Data.MediaData | null>(null);
    let multiValue = $state<App.Data.MediaData[] | null>(null);

    function onEdit() {
        console.log('Edit clicked');
    }

    function onShare() {
        console.log('Share clicked');
    }

    function onDelete() {
        console.log('Delete clicked');
    }
</script>

<svelte:head>
    <title>Media Playground</title>
</svelte:head>

<div class="space-y-8 p-4">
    <div>
        <h2
            class="font-heading scroll-m-20 text-2xl font-semibold tracking-tight"
        >
            Single
        </h2>
        <p class="text-muted-foreground text-sm">
            Select a single media item. Custom header, trigger, and preview.
        </p>
        <div class="mt-4">
            <MediaManager title="Select Thumbnail" bind:value={singleValue}>
                {#snippet header({ title, onOpen })}
                    <div
                        class="flex items-center justify-between rounded-lg border p-4"
                    >
                        <div>
                            <h4 class="font-heading text-lg font-semibold">
                                {title}
                            </h4>
                            <p class="text-xs text-muted-foreground">
                                Click to open media picker
                            </p>
                        </div>
                        <MediaManagerTrigger
                            hasValue={singleValue !== null}
                            onclick={onOpen}
                        />
                    </div>
                {/snippet}

                {#snippet preview({ value: val })}
                    {#if val}
                        <div class="flex gap-2">
                            {#if Array.isArray(val)}
                                {#each val as item (item.id)}
                                    <img
                                        src={item.mediumUrl ?? item.url}
                                        alt={item.filename}
                                        class="h-20 w-20 rounded object-cover"
                                    />
                                {/each}
                            {:else}
                                <img
                                    src={val.mediumUrl ?? val.url}
                                    alt={val.filename}
                                    class="h-20 w-20 rounded object-cover"
                                />
                            {/if}
                        </div>
                    {:else}
                        <p class="text-sm text-muted-foreground">
                            Nothing selected yet.
                        </p>
                    {/if}
                {/snippet}
            </MediaManager>
        </div>
        {#if singleValue}
            <pre
                class="mt-2 text-xs text-muted-foreground overflow-auto">{JSON.stringify(
                    singleValue,
                    null,
                    2,
                )}</pre>
        {/if}
    </div>

    <div>
        <h2
            class="font-heading scroll-m-20 text-2xl font-semibold tracking-tight"
        >
            Multiple
        </h2>
        <p class="text-muted-foreground text-sm">
            Select multiple media items. Custom header, trigger, and preview.
        </p>
        <div class="mt-4">
            <MediaManager
                title="Select Gallery Images"
                multiple
                bind:value={multiValue}
            >
                {#snippet header({ title, onOpen })}
                    <div
                        class="flex items-center justify-between rounded-lg border p-4"
                    >
                        <h4 class="font-heading text-lg font-semibold">
                            {title}
                        </h4>
                        <MediaManagerTrigger
                            hasValue={multiValue !== null &&
                                multiValue.length > 0}
                            onclick={onOpen}
                        />
                    </div>
                {/snippet}

                {#snippet preview({ value: val })}
                    {#if val}
                        <div class="flex gap-2">
                            {#if Array.isArray(val)}
                                {#each val as item (item.id)}
                                    <img
                                        src={item.mediumUrl ?? item.url}
                                        alt={item.filename}
                                        class="h-20 w-20 rounded object-cover"
                                    />
                                {/each}
                            {:else}
                                <img
                                    src={val.mediumUrl ?? val.url}
                                    alt={val.filename}
                                    class="h-20 w-20 rounded object-cover"
                                />
                            {/if}
                        </div>
                    {:else}
                        <p class="text-sm text-muted-foreground">
                            Nothing selected yet.
                        </p>
                    {/if}
                {/snippet}
            </MediaManager>
        </div>
        {#if multiValue && multiValue.length > 0}
            <pre
                class="mt-2 text-xs text-muted-foreground overflow-auto">{JSON.stringify(
                    multiValue,
                    null,
                    2,
                )}</pre>
        {/if}
    </div>

    <div>
        <h2
            class="font-heading scroll-m-20 text-2xl font-semibold tracking-tight"
        >
            Speed Dial
        </h2>
        <p class="text-muted-foreground text-sm">
            FAB that expands action items above. Trigger toggles Plus/X with rotation.
            Different button variants for each action.
        </p>

        <SpeedDial.Root>
            <SpeedDial.Trigger>
                {#snippet icon()}
                    <Plus class="size-4" />
                {/snippet}
                {#snippet iconClose()}
                    <X class="size-4" />
                {/snippet}
                {#snippet labelClose()}
                    Closess
                {/snippet}
                Add
            </SpeedDial.Trigger>
            <SpeedDial.Content>
                <SpeedDial.Item variant="default" onclick={onEdit}>
                    <FilePen class="size-4" />
                    Edit
                </SpeedDial.Item>
                <SpeedDial.Item variant="secondary" onclick={onShare}>
                    <Share class="size-4" />
                    Share
                </SpeedDial.Item>
                <SpeedDial.Item variant="outline" onclick={() => console.log('Download')}>
                    <Download class="size-4" />
                    Download
                </SpeedDial.Item>
                <SpeedDial.Item variant="ghost" onclick={() => console.log('Preview')}>
                    <Eye class="size-4" />
                    Preview
                </SpeedDial.Item>
                <SpeedDial.Item variant="destructive" onclick={onDelete}>
                    <Trash2 class="size-4" />
                    Delete
                </SpeedDial.Item>
            </SpeedDial.Content>
        </SpeedDial.Root>

        <div class="mt-4">
            <h3 class="font-heading text-lg font-semibold">Variants demo</h3>
            <p class="text-muted-foreground text-xs mb-2">Each item uses a different button variant:</p>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center gap-1 rounded-md bg-primary px-2 py-0.5 text-xs text-primary-foreground">default</span>
                <span class="inline-flex items-center gap-1 rounded-md bg-secondary px-2 py-0.5 text-xs text-secondary-foreground">secondary</span>
                <span class="inline-flex items-center gap-1 rounded-md border px-2 py-0.5 text-xs">outline</span>
                <span class="inline-flex items-center gap-1 rounded-md bg-muted px-2 py-0.5 text-xs text-muted-foreground">ghost</span>
                <span class="inline-flex items-center gap-1 rounded-md bg-destructive/10 px-2 py-0.5 text-xs text-destructive">destructive</span>
            </div>
        </div>
    </div>
</div>
