<script lang="ts">
    import { Icon } from '@/components/icon';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card/index.js';
    import * as Dialog from '@/components/ui/dialog/index.js';
    import * as Field from '@/components/ui/field/index.js';
    import { Input } from '@/components/ui/input';
    import * as Table from '@/components/ui/table';
    import { Textarea } from '@/components/ui/textarea';

    export interface Track {
        names?: {
            English?: string;
            Romaji?: string;
            Japanese?: string;
            en?: string;
            ja?: string;
            [key: string]: string | undefined;
        };
        track_length?: string;
        [key: string]: any;
    }

    export interface Disc {
        name?: string;
        disc_length?: string;
        tracks?: Track[];
        [key: string]: any;
    }

    let {
        vgmdbData = $bindable(null),
    }: {
        vgmdbData?: Record<string, any> | null;
    } = $props();

    const discs = $derived<Disc[]>(
        Array.isArray(vgmdbData?.discs) ? vgmdbData.discs : [],
    );

    let expandedTrackKeys = $state<Record<string, boolean>>({});

    let bulkDialogOpen = $state(false);
    let targetDiscIndex = $state<number | null>(null);
    let bulkText = $state('');
    let bulkReplace = $state(false);

    function updateDiscs(newDiscs: Disc[]) {
        vgmdbData = {
            ...(vgmdbData ?? {}),
            discs: newDiscs,
        };
    }

    function addDisc() {
        const nextNum = discs.length + 1;
        const newDisc: Disc = {
            name: `Disc ${nextNum}`,
            disc_length: '',
            tracks: [],
        };
        updateDiscs([...discs, newDisc]);
    }

    function removeDisc(index: number) {
        if (!confirm(`Are you sure you want to remove Disc ${index + 1}?`)) {
            return;
        }

        updateDiscs(discs.filter((_, i) => i !== index));
    }

    function moveDisc(index: number, direction: 'up' | 'down') {
        const targetIndex = direction === 'up' ? index - 1 : index + 1;

        if (targetIndex < 0 || targetIndex >= discs.length) {
            return;
        }

        const list = [...discs];
        const temp = list[index];
        list[index] = list[targetIndex];
        list[targetIndex] = temp;
        updateDiscs(list);
    }

    function updateDiscField(
        index: number,
        field: 'name' | 'disc_length',
        value: string,
    ) {
        const list = discs.map((d, i) => {
            if (i === index) {
                return { ...d, [field]: value };
            }

            return d;
        });
        updateDiscs(list);
    }

    function addTrack(discIndex: number) {
        const list = discs.map((d, i) => {
            if (i === discIndex) {
                const tracks = Array.isArray(d.tracks) ? [...d.tracks] : [];
                tracks.push({
                    names: { English: '', Romaji: '', Japanese: '' },
                    track_length: '',
                });

                return { ...d, tracks };
            }

            return d;
        });
        updateDiscs(list);
    }

    function removeTrack(discIndex: number, trackIndex: number) {
        const list = discs.map((d, i) => {
            if (i === discIndex) {
                const tracks = (d.tracks ?? []).filter((_, t) => t !== trackIndex);

                return { ...d, tracks };
            }

            return d;
        });
        updateDiscs(list);
    }

    function moveTrack(
        discIndex: number,
        trackIndex: number,
        direction: 'up' | 'down',
    ) {
        const targetIndex = direction === 'up' ? trackIndex - 1 : trackIndex + 1;
        const disc = discs[discIndex];

        if (!disc || !disc.tracks) {
            return;
        }

        if (targetIndex < 0 || targetIndex >= disc.tracks.length) {
            return;
        }

        const list = discs.map((d, i) => {
            if (i === discIndex) {
                const tracks = [...(d.tracks ?? [])];
                const temp = tracks[trackIndex];
                tracks[trackIndex] = tracks[targetIndex];
                tracks[targetIndex] = temp;

                return { ...d, tracks };
            }

            return d;
        });
        updateDiscs(list);
    }

    function getPrimaryTitle(
        names?: Record<string, string | null | undefined>,
    ): string {
        if (!names) {
            return '';
        }

        return (
            names.English ||
            names.Romaji ||
            names.Japanese ||
            names.en ||
            names.romaji ||
            names.ja ||
            names['ja-latn'] ||
            names.native ||
            Object.values(names).find(Boolean) ||
            ''
        );
    }

    function setPrimaryTitle(
        discIndex: number,
        trackIndex: number,
        value: string,
    ) {
        const disc = discs[discIndex];
        const track = disc?.tracks?.[trackIndex];
        const currentNames = { ...(track?.names ?? {}) };

        if (currentNames.English || (!currentNames.Romaji && !currentNames.Japanese)) {
            currentNames.English = value;
        } else if (currentNames.Romaji) {
            currentNames.Romaji = value;
        } else {
            currentNames.Japanese = value;
        }

        updateTrackField(discIndex, trackIndex, 'names', currentNames);
    }

    function updateTrackNameLang(
        discIndex: number,
        trackIndex: number,
        lang: 'English' | 'Romaji' | 'Japanese',
        value: string,
    ) {
        const disc = discs[discIndex];
        const track = disc?.tracks?.[trackIndex];
        const currentNames = { ...(track?.names ?? {}) };
        currentNames[lang] = value;
        updateTrackField(discIndex, trackIndex, 'names', currentNames);
    }

    function updateTrackField(
        discIndex: number,
        trackIndex: number,
        field: 'names' | 'track_length',
        value: any,
    ) {
        const list = discs.map((d, i) => {
            if (i === discIndex) {
                const tracks = (d.tracks ?? []).map((t, ti) => {
                    if (ti === trackIndex) {
                        return { ...t, [field]: value };
                    }

                    return t;
                });

                return { ...d, tracks };
            }

            return d;
        });
        updateDiscs(list);
    }

    function toggleExpandTrack(key: string) {
        expandedTrackKeys[key] = !expandedTrackKeys[key];
    }

    function openBulkImport(discIndex: number) {
        targetDiscIndex = discIndex;
        bulkText = '';
        bulkReplace = false;
        bulkDialogOpen = true;
    }

    function parseTrackLine(line: string): { title: string; length: string } {
        let clean = line.trim();
        clean = clean.replace(/^\d+[.\-\s:]+\s*/, '');

        let length = '';
        const match = /(\d{1,2}:\d{2}(?::\d{2})?)\s*$/.exec(clean);

        if (match && typeof match.index === 'number') {
            length = match[1];
            clean = clean.slice(0, match.index).trim();
            clean = clean.replace(/[-–—()[\]]+$/, '').trim();
        }

        return { title: clean, length };
    }

    function applyBulkImport() {
        if (targetDiscIndex === null) {
            return;
        }

        const lines = bulkText
            .split('\n')
            .map((l) => l.trim())
            .filter(Boolean);

        const newTracks: Track[] = lines.map((line) => {
            const { title, length } = parseTrackLine(line);

            return {
                names: { English: title, Romaji: '', Japanese: '' },
                track_length: length,
            };
        });

        const list = discs.map((d, i) => {
            if (i === targetDiscIndex) {
                const existing = bulkReplace ? [] : Array.isArray(d.tracks) ? d.tracks : [];

                return { ...d, tracks: [...existing, ...newTracks] };
            }

            return d;
        });

        updateDiscs(list);
        bulkDialogOpen = false;
        targetDiscIndex = null;
        bulkText = '';
    }

    const albumArtist = $derived(
        vgmdbData?.artist ??
            vgmdbData?.performers?.[0]?.names?.en ??
            vgmdbData?.performers?.[0]?.names?.ja ??
            vgmdbData?.vocals?.[0]?.names?.en ??
            '',
    );
    const albumCatalog = $derived(vgmdbData?.catalog ?? '');
    const albumLabel = $derived(
        vgmdbData?.label ??
            vgmdbData?.publisher?.names?.en ??
            vgmdbData?.publisher?.names?.ja ??
            '',
    );
    const albumReleaseDate = $derived(vgmdbData?.release_date ?? '');

    function updateAlbumMeta(
        field: 'artist' | 'catalog' | 'label' | 'release_date',
        value: string,
    ) {
        vgmdbData = {
            ...(vgmdbData ?? {}),
            [field]: value,
            ...(field === 'artist'
                ? { performers: [{ names: { en: value } }] }
                : {}),
            ...(field === 'label'
                ? { publisher: { names: { en: value } } }
                : {}),
        };
    }
</script>

<Card.Root>
    <Card.Header class="px-4 py-3 md:px-6 md:py-4">
        <div class="flex items-center justify-between w-full">
            <div>
                <Card.Title>Tracklist & Album Info</Card.Title>
                <Card.Description class="text-xs text-muted-foreground mt-0.5">
                    Configure album metadata, discs, and track information.
                </Card.Description>
            </div>
            <Button variant="outline" size="sm" onclick={addDisc}>
                <Icon
                    lucide={() => import('lucide-svelte/icons/plus')}
                    tabler={() => import('@tabler/icons-svelte/icons/plus')}
                    phosphor={() => import('phosphor-svelte/lib/PlusIcon')}
                    remixicon={() => import('remixicon-svelte/icons/add-line')}
                    class="mr-1.5 size-4"
                />
                Add Disc
            </Button>
        </div>
    </Card.Header>

    <Card.Content class="space-y-6 px-4 pb-6 md:px-6">
        <!-- Album Metadata Grid -->
        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-4 rounded-lg border bg-muted/30 p-3">
            <Field.Field>
                <Field.Label class="text-xs">Artist</Field.Label>
                <Input
                    value={albumArtist}
                    oninput={(e) => updateAlbumMeta('artist', e.currentTarget.value)}
                    placeholder="e.g. milet, Atarayo"
                    class="h-8 text-xs bg-background"
                />
            </Field.Field>

            <Field.Field>
                <Field.Label class="text-xs">Catalog Number</Field.Label>
                <Input
                    value={albumCatalog}
                    oninput={(e) => updateAlbumMeta('catalog', e.currentTarget.value)}
                    placeholder="e.g. SECL-2937"
                    class="h-8 text-xs bg-background"
                />
            </Field.Field>

            <Field.Field>
                <Field.Label class="text-xs">Label</Field.Label>
                <Input
                    value={albumLabel}
                    oninput={(e) => updateAlbumMeta('label', e.currentTarget.value)}
                    placeholder="e.g. SME Records"
                    class="h-8 text-xs bg-background"
                />
            </Field.Field>

            <Field.Field>
                <Field.Label class="text-xs">Release Date</Field.Label>
                <Input
                    type="date"
                    value={albumReleaseDate}
                    oninput={(e) => updateAlbumMeta('release_date', e.currentTarget.value)}
                    class="h-8 text-xs bg-background"
                />
            </Field.Field>
        </div>
        {#if discs.length === 0}
            <div
                class="flex flex-col items-center justify-center p-8 rounded-lg border border-dashed text-center text-muted-foreground"
            >
                <Icon
                    lucide={() => import('lucide-svelte/icons/disc')}
                    tabler={() => import('@tabler/icons-svelte/icons/disc')}
                    phosphor={() => import('phosphor-svelte/lib/DiscIcon')}
                    remixicon={() => import('remixicon-svelte/icons/disc-line')}
                    class="size-10 opacity-30 mb-2"
                />
                <p class="text-sm font-medium">No discs configured yet</p>
                <p class="text-xs text-muted-foreground mt-1 mb-4">
                    Add a disc to start adding tracks.
                </p>
                <Button variant="outline" size="sm" onclick={addDisc}>
                    <Icon
                        lucide={() => import('lucide-svelte/icons/plus')}
                        tabler={() => import('@tabler/icons-svelte/icons/plus')}
                        phosphor={() => import('phosphor-svelte/lib/PlusIcon')}
                        remixicon={() =>
                            import('remixicon-svelte/icons/add-line')}
                        class="mr-1.5 size-4"
                    />
                    Add Disc
                </Button>
            </div>
        {:else}
            {#each discs as disc, dIndex (dIndex)}
                <div class="rounded-lg border bg-card/60 overflow-hidden shadow-xs">
                    <!-- Disc Header Bar -->
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 bg-muted/50 px-4 py-3 border-b"
                    >
                        <div class="flex flex-1 items-center gap-2 min-w-[200px]">
                            <Icon
                                lucide={() => import('lucide-svelte/icons/disc')}
                                tabler={() => import('@tabler/icons-svelte/icons/disc')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/DiscIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/disc-line')}
                                class="size-4 shrink-0 text-muted-foreground"
                            />
                            <Input
                                value={disc.name ?? `Disc ${dIndex + 1}`}
                                oninput={(e) =>
                                    updateDiscField(
                                        dIndex,
                                        'name',
                                        e.currentTarget.value,
                                    )}
                                placeholder="Disc Name (e.g. Disc 1 [CD])"
                                class="h-8 max-w-xs text-sm font-medium bg-background"
                            />
                            <Input
                                value={disc.disc_length ?? ''}
                                oninput={(e) =>
                                    updateDiscField(
                                        dIndex,
                                        'disc_length',
                                        e.currentTarget.value,
                                    )}
                                placeholder="Length (e.g. 45:10)"
                                class="h-8 w-28 text-xs font-mono bg-background"
                            />
                        </div>

                        <div class="flex items-center gap-1.5">
                            <Button
                                variant="outline"
                                size="sm"
                                class="h-8 text-xs"
                                onclick={() => openBulkImport(dIndex)}
                            >
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/file-text')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/file-text')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/FileTextIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/file-text-line')}
                                    class="mr-1 size-3.5"
                                />
                                Bulk Paste
                            </Button>

                            <Button
                                variant="ghost"
                                size="icon"
                                class="size-8"
                                disabled={dIndex === 0}
                                onclick={() => moveDisc(dIndex, 'up')}
                                title="Move Disc Up"
                            >
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/chevron-up')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/chevron-up')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/CaretUpIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/arrow-up-s-line')}
                                    class="size-4"
                                />
                            </Button>

                            <Button
                                variant="ghost"
                                size="icon"
                                class="size-8"
                                disabled={dIndex === discs.length - 1}
                                onclick={() => moveDisc(dIndex, 'down')}
                                title="Move Disc Down"
                            >
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/chevron-down')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/chevron-down')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/CaretDownIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/arrow-down-s-line')}
                                    class="size-4"
                                />
                            </Button>

                            <Button
                                variant="ghost"
                                size="icon"
                                class="size-8 text-destructive hover:text-destructive"
                                onclick={() => removeDisc(dIndex)}
                                title="Delete Disc"
                            >
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/trash-2')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/trash')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/TrashIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/delete-bin-line')}
                                    class="size-4"
                                />
                            </Button>
                        </div>
                    </div>

                    <!-- Tracks Table -->
                    <div class="p-3 space-y-3">
                        {#if (disc.tracks?.length ?? 0) === 0}
                            <div class="py-6 text-center text-xs text-muted-foreground">
                                No tracks in this disc. Click "Add Track" or "Bulk Paste" above.
                            </div>
                        {:else}
                            <Table.Root>
                                <Table.Header>
                                    <Table.Row>
                                        <Table.Head class="w-10 text-center">#</Table.Head>
                                        <Table.Head>Title</Table.Head>
                                        <Table.Head class="w-24">Length</Table.Head>
                                        <Table.Head class="w-24 text-right">Actions</Table.Head>
                                    </Table.Row>
                                </Table.Header>
                                <Table.Body>
                                    {#each disc.tracks ?? [] as track, tIndex (tIndex)}
                                        {@const trackKey = `${dIndex}-${tIndex}`}
                                        {@const isExpanded = Boolean(expandedTrackKeys[trackKey])}
                                        <Table.Row class="align-top">
                                            <Table.Cell
                                                class="text-center font-mono text-xs pt-3.5 text-muted-foreground"
                                            >
                                                {tIndex + 1}
                                            </Table.Cell>

                                            <Table.Cell class="space-y-2">
                                                <div class="flex items-center gap-1.5">
                                                    <Input
                                                        value={getPrimaryTitle(track.names)}
                                                        oninput={(e) =>
                                                            setPrimaryTitle(
                                                                dIndex,
                                                                tIndex,
                                                                e.currentTarget.value,
                                                            )}
                                                        placeholder={`Track ${tIndex + 1} title`}
                                                        class="h-8 text-sm"
                                                    />
                                                    <Button
                                                        variant={isExpanded ? 'secondary' : 'outline'}
                                                        size="sm"
                                                        class="h-8 px-2 text-xs shrink-0"
                                                        onclick={() => toggleExpandTrack(trackKey)}
                                                        title="Multilingual Titles (EN/Romaji/JA)"
                                                    >
                                                        <Icon
                                                            lucide={() =>
                                                                import('lucide-svelte/icons/globe')}
                                                            tabler={() =>
                                                                import('@tabler/icons-svelte/icons/world')}
                                                            phosphor={() =>
                                                                import('phosphor-svelte/lib/GlobeIcon')}
                                                            remixicon={() =>
                                                                import('remixicon-svelte/icons/global-line')}
                                                            class="size-3.5 mr-1"
                                                        />
                                                        Langs
                                                    </Button>
                                                </div>

                                                {#if isExpanded}
                                                    <div
                                                        class="grid gap-2 p-2.5 rounded-md bg-muted/60 border text-xs"
                                                    >
                                                        <Field.Field orientation="horizontal">
                                                            <Field.Label class="w-16 text-xs text-muted-foreground">
                                                                English
                                                            </Field.Label>
                                                            <Input
                                                                value={track.names?.English ?? track.names?.en ?? ''}
                                                                oninput={(e) =>
                                                                    updateTrackNameLang(
                                                                        dIndex,
                                                                        tIndex,
                                                                        'English',
                                                                        e.currentTarget.value,
                                                                    )}
                                                                placeholder="English title"
                                                                class="h-7 text-xs bg-background"
                                                            />
                                                        </Field.Field>

                                                        <Field.Field orientation="horizontal">
                                                            <Field.Label class="w-16 text-xs text-muted-foreground">
                                                                Romaji
                                                            </Field.Label>
                                                            <Input
                                                                value={track.names?.Romaji ?? track.names?.['ja-latn'] ?? ''}
                                                                oninput={(e) =>
                                                                    updateTrackNameLang(
                                                                        dIndex,
                                                                        tIndex,
                                                                        'Romaji',
                                                                        e.currentTarget.value,
                                                                    )}
                                                                placeholder="Romaji title"
                                                                class="h-7 text-xs bg-background"
                                                            />
                                                        </Field.Field>

                                                        <Field.Field orientation="horizontal">
                                                            <Field.Label class="w-16 text-xs text-muted-foreground">
                                                                Japanese
                                                            </Field.Label>
                                                            <Input
                                                                value={track.names?.Japanese ?? track.names?.ja ?? ''}
                                                                oninput={(e) =>
                                                                    updateTrackNameLang(
                                                                        dIndex,
                                                                        tIndex,
                                                                        'Japanese',
                                                                        e.currentTarget.value,
                                                                    )}
                                                                placeholder="Japanese title"
                                                                class="h-7 text-xs bg-background"
                                                            />
                                                        </Field.Field>
                                                    </div>
                                                {/if}
                                            </Table.Cell>

                                            <Table.Cell>
                                                <Input
                                                    value={track.track_length ?? track.length ?? ''}
                                                    oninput={(e) =>
                                                        updateTrackField(
                                                            dIndex,
                                                            tIndex,
                                                            'track_length',
                                                            e.currentTarget.value,
                                                        )}
                                                    placeholder="3:45"
                                                    class="h-8 w-20 text-xs font-mono"
                                                />
                                            </Table.Cell>

                                            <Table.Cell class="text-right">
                                                <div class="flex items-center justify-end gap-1">
                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="size-7"
                                                        disabled={tIndex === 0}
                                                        onclick={() => moveTrack(dIndex, tIndex, 'up')}
                                                        title="Move Track Up"
                                                    >
                                                        <Icon
                                                            lucide={() =>
                                                                import('lucide-svelte/icons/chevron-up')}
                                                            tabler={() =>
                                                                import('@tabler/icons-svelte/icons/chevron-up')}
                                                            phosphor={() =>
                                                                import('phosphor-svelte/lib/CaretUpIcon')}
                                                            remixicon={() =>
                                                                import('remixicon-svelte/icons/arrow-up-s-line')}
                                                            class="size-3.5"
                                                        />
                                                    </Button>

                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="size-7"
                                                        disabled={tIndex === (disc.tracks?.length ?? 0) - 1}
                                                        onclick={() => moveTrack(dIndex, tIndex, 'down')}
                                                        title="Move Track Down"
                                                    >
                                                        <Icon
                                                            lucide={() =>
                                                                import('lucide-svelte/icons/chevron-down')}
                                                            tabler={() =>
                                                                import('@tabler/icons-svelte/icons/chevron-down')}
                                                            phosphor={() =>
                                                                import('phosphor-svelte/lib/CaretDownIcon')}
                                                            remixicon={() =>
                                                                import('remixicon-svelte/icons/arrow-down-s-line')}
                                                            class="size-3.5"
                                                        />
                                                    </Button>

                                                    <Button
                                                        variant="ghost"
                                                        size="icon"
                                                        class="size-7 text-destructive hover:text-destructive"
                                                        onclick={() => removeTrack(dIndex, tIndex)}
                                                        title="Delete Track"
                                                    >
                                                        <Icon
                                                            lucide={() =>
                                                                import('lucide-svelte/icons/trash-2')}
                                                            tabler={() =>
                                                                import('@tabler/icons-svelte/icons/trash')}
                                                            phosphor={() =>
                                                                import('phosphor-svelte/lib/TrashIcon')}
                                                            remixicon={() =>
                                                                import('remixicon-svelte/icons/delete-bin-line')}
                                                            class="size-3.5"
                                                        />
                                                    </Button>
                                                </div>
                                            </Table.Cell>
                                        </Table.Row>
                                    {/each}
                                </Table.Body>
                            </Table.Root>
                        {/if}

                        <Button
                            variant="outline"
                            size="sm"
                            class="h-8 text-xs"
                            onclick={() => addTrack(dIndex)}
                        >
                            <Icon
                                lucide={() => import('lucide-svelte/icons/plus')}
                                tabler={() => import('@tabler/icons-svelte/icons/plus')}
                                phosphor={() =>
                                    import('phosphor-svelte/lib/PlusIcon')}
                                remixicon={() =>
                                    import('remixicon-svelte/icons/add-line')}
                                class="mr-1.5 size-3.5"
                            />
                            Add Track
                        </Button>
                    </div>
                </div>
            {/each}
        {/if}
    </Card.Content>
</Card.Root>

<!-- Bulk Import Dialog -->
<Dialog.Root bind:open={bulkDialogOpen}>
    <Dialog.Content class="max-w-md">
        <Dialog.Header>
            <Dialog.Title>
                Bulk Paste Tracks
                {#if targetDiscIndex !== null && discs[targetDiscIndex]}
                    <span class="text-muted-foreground font-normal text-sm">
                        for {discs[targetDiscIndex].name ?? `Disc ${targetDiscIndex + 1}`}
                    </span>
                {/if}
            </Dialog.Title>
            <Dialog.Description class="text-xs text-muted-foreground">
                Paste track lines (one per line). Track numbers and trailing lengths like
                "(3:45)" or "4:12" will be auto-parsed.
            </Dialog.Description>
        </Dialog.Header>

        <div class="space-y-3 py-2">
            <Textarea
                bind:value={bulkText}
                placeholder="01. First Song (3:45)&#10;02. Second Song - 4:10&#10;Third Song"
                rows={8}
                class="font-mono text-xs"
            />

            <label class="flex items-center gap-2 text-xs text-muted-foreground cursor-pointer">
                <input
                    type="checkbox"
                    bind:checked={bulkReplace}
                    class="rounded border-input text-primary focus:ring-primary"
                />
                <span>Replace existing tracks on this disc</span>
            </label>
        </div>

        <Dialog.Footer class="gap-2 sm:gap-0">
            <Button
                variant="outline"
                size="sm"
                onclick={() => (bulkDialogOpen = false)}
            >
                Cancel
            </Button>
            <Button
                size="sm"
                onclick={applyBulkImport}
                disabled={!bulkText.trim()}
            >
                Import Tracks
            </Button>
        </Dialog.Footer>
    </Dialog.Content>
</Dialog.Root>
