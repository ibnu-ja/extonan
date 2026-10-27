<script lang="ts">
    import { toast } from 'svelte-sonner';
    import { Icon } from '@/components/icon';
    import * as Avatar from '@/components/ui/avatar';
    import { Badge } from '@/components/ui/badge';
    import { Button } from '@/components/ui/button';
    import * as Card from '@/components/ui/card';
    import { Checkbox } from '@/components/ui/checkbox';
    import * as DropdownMenu from '@/components/ui/dropdown-menu';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Progress } from '@/components/ui/progress';
    import { Separator } from '@/components/ui/separator';
    import { Slider } from '@/components/ui/slider';
    import { Switch } from '@/components/ui/switch';
    import * as Tabs from '@/components/ui/tabs';
    import * as ToggleGroup from '@/components/ui/toggle-group';
    import {
        useDesignSystem,
        STYLES,
        RADII,
        STYLE_PRESET_RADII,
        ICON_LIBRARIES,
        M3_VARIANTS,
        M3_SEED_PRESETS,
        buildRegistryTheme,
        isValidHex,
        normalizeHex,
    } from '@/lib/design-system';
    import type {
        StyleName,
        RadiusValue,
        MenuColorValue,
        FontSizeValue,
        M3Variant,
    } from '@/lib/design-system';
    import { themeState } from '@/lib/theme.svelte';

    const designSystem = useDesignSystem();
    const { resolvedAppearance } = themeState();
    const isDark = $derived(resolvedAppearance() === 'dark');
    const activePresetRadius = $derived(
        STYLE_PRESET_RADII[designSystem.style] ?? '0.5rem',
    );
    const activeTheme = $derived(buildRegistryTheme(designSystem.toConfig()));
    const ramps = $derived(activeTheme.ramps);

    let presetInput = $state('');
    let customHexInput = $state(designSystem.sourceColor);
    let sandboxChecked = $state(true);
    let sandboxCheckbox = $state(true);
    let sliderValue = $state([65]);

    function handleHexInput(val: string) {
        customHexInput = val;

        if (isValidHex(val)) {
            designSystem.sourceColor = normalizeHex(val);
        }
    }

    function copyPreset() {
        if (typeof navigator !== 'undefined' && navigator.clipboard) {
            navigator.clipboard.writeText(designSystem.preset);
            toast.success('Preset code copied to clipboard!', {
                description: designSystem.preset,
            });
        }
    }

    function handleApplyPreset() {
        const code = presetInput.trim();

        if (!code) {
            return;
        }

        try {
            designSystem.applyPreset(code);
            toast.success('Preset applied successfully!', {
                description: `Active preset: ${code}`,
            });
            presetInput = '';
        } catch {
            toast.error('Failed to decode preset code.', {
                description:
                    'Please check that the code matches the standard format.',
            });
        }
    }

    function handleReset() {
        designSystem.reset();
        customHexInput = designSystem.sourceColor;
        toast.info('Appearance settings reset to default.');
    }

    function handleRandomize() {
        const styles = STYLES.map((s) => s.name);
        const variants = M3_VARIANTS.map((v) => v.value);
        const seedHexes = M3_SEED_PRESETS.map((p) => p.hex);
        const radii: RadiusValue[] = [null, ...RADII.map((r) => r.name)];

        designSystem.style = styles[
            Math.floor(Math.random() * styles.length)
        ] as StyleName;
        designSystem.sourceColor =
            seedHexes[Math.floor(Math.random() * seedHexes.length)];
        designSystem.variant = variants[
            Math.floor(Math.random() * variants.length)
        ] as M3Variant;
        customHexInput = designSystem.sourceColor;
        designSystem.contrast = 0.0;
        designSystem.radius = radii[Math.floor(Math.random() * radii.length)];

        toast.success('Random theme preset applied!', {
            description: `${designSystem.style.toUpperCase()} / ${designSystem.variant} (${designSystem.sourceColor}) / Radius: ${designSystem.radius ?? 'Default'}`,
        });
    }

    function triggerSampleToast() {
        toast.success('Theme token active!', {
            description: `Style: ${designSystem.style.toUpperCase()}, Palette: ${designSystem.variant} (${designSystem.sourceColor})`,
        });
    }
</script>

<svelte:window
    onkeydown={(e) => {
        if ((e.key === 'r' || e.key === 'R') && !e.metaKey && !e.ctrlKey) {
            if (
                (e.target instanceof HTMLElement &&
                    e.target.isContentEditable) ||
                e.target instanceof HTMLInputElement ||
                e.target instanceof HTMLTextAreaElement ||
                e.target instanceof HTMLSelectElement
            ) {
                return;
            }

            handleRandomize();
        }
    }}
/>

<div class="space-y-8">
    <!-- Preset Quick Bar -->
    <Card.Root>
        <Card.Header class="pb-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="space-y-1">
                    <Card.Title class="text-base font-semibold"
                        >Preset Sharing</Card.Title
                    >
                    <Card.Description>
                        Export or import your complete theme configuration
                        token. Press <kbd
                            class="rounded border px-1.5 py-0.5 font-mono text-xs text-muted-foreground"
                            >R</kbd
                        > to randomize.
                    </Card.Description>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onclick={handleRandomize}
                    >
                        <Icon
                            lucide={() => import('lucide-svelte/icons/shuffle')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/arrows-shuffle')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/ShuffleIcon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/ShuffleIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/shuffle-line')}
                            class="size-3.5"
                        />
                        Randomize
                    </Button>
                    <Button variant="ghost" size="sm" onclick={handleReset}>
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/rotate-ccw')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/rotate')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/RotateLeft01Icon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/ArrowCounterClockwiseIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/restart-line')}
                            class="size-3.5"
                        />
                        Reset
                    </Button>
                </div>
            </div>
        </Card.Header>
        <Card.Content class="space-y-4">
            <!-- Codeblock with copy button -->
            <div class="space-y-1.5">
                <Label class="text-xs font-semibold text-muted-foreground"
                    >Active Preset Token</Label
                >
                <div
                    class="relative rounded-lg border border-border/60 bg-muted/40 p-3 pr-12 font-mono text-xs"
                >
                    <div
                        class="max-h-24 overflow-y-auto break-all select-all font-mono text-xs leading-relaxed text-foreground/90"
                    >
                        {designSystem.preset}
                    </div>
                    <Button
                        variant="secondary"
                        size="icon"
                        class="absolute top-2.5 right-2.5 size-7 shadow-xs"
                        onclick={copyPreset}
                        title="Copy preset token"
                    >
                        <Icon
                            lucide={() => import('lucide-svelte/icons/copy')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/copy')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Copy01Icon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/CopyIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/file-copy-line')}
                            class="size-3.5"
                        />
                    </Button>
                </div>
            </div>

            <div class="flex max-w-md items-center gap-2">
                <Input
                    placeholder="Paste preset token (ex_...)..."
                    bind:value={presetInput}
                    class="font-mono text-xs"
                />
                <Button
                    size="sm"
                    onclick={handleApplyPreset}
                    disabled={!presetInput.trim()}
                >
                    Apply Preset
                </Button>
            </div>
        </Card.Content>
    </Card.Root>

    <!-- Style Selector (8 Styles) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <Label class="text-sm font-semibold">Interface Style</Label>
            <span class="text-xs text-muted-foreground"
                >Current: {designSystem.style}</span
            >
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
            {#each STYLES as s (s.name)}
                {@const active = designSystem.style === s.name}
                <Card.Root
                    role="button"
                    tabindex={0}
                    onclick={() => (designSystem.style = s.name as StyleName)}
                    onkeydown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            designSystem.style = s.name as StyleName;
                        }
                    }}
                    class="cursor-pointer gap-1 p-3 text-left transition-all hover:bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {active
                        ? 'border-primary bg-primary/10 ring-1 ring-primary shadow-xs'
                        : 'border-border hover:text-foreground'}"
                >
                    <Card.Header class="p-0">
                        <div class="flex w-full items-center justify-between">
                            <Card.Title
                                class="text-sm font-medium {active
                                    ? 'text-primary font-semibold'
                                    : 'text-foreground'}"
                            >
                                {s.title}
                            </Card.Title>
                            {#if active}
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/check')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/check')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/Tick02Icon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/CheckIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/check-line')}
                                    class="size-3.5 text-primary"
                                />
                            {/if}
                        </div>
                    </Card.Header>
                    <Card.Content class="p-0">
                        <Card.Description
                            class="line-clamp-2 text-xs leading-relaxed"
                        >
                            {s.description}
                        </Card.Description>
                    </Card.Content>
                </Card.Root>
            {/each}
        </div>
    </div>

    <Separator />

    <!-- 2. Material 3 Color Studio -->
    <Card.Root>
        <Card.Header class="pb-3">
            <div
                class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="space-y-0.5">
                    <Card.Title class="text-base font-semibold"
                        >Color Studio</Card.Title
                    >
                    <Card.Description>
                        Coloring system with perceptual CAM16 contrast and dynamic
                        tonal palettes.
                    </Card.Description>
                </div>
                <div class="flex items-center gap-2">
                    <Badge variant="outline" class="font-mono text-xs uppercase">
                        {designSystem.sourceColor}
                    </Badge>
                    <div
                        class="size-4 rounded-full border border-border shadow-xs"
                        style="background-color: {designSystem.sourceColor}"
                    ></div>
                </div>
            </div>
        </Card.Header>

        <Card.Content class="space-y-5">

        <!-- Sub-row 1: Source Seed Color (Quick presets + Custom Hex + Native Picker) -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span
                    class="text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                    >Source Seed Color</span
                >
                <span class="text-xs text-muted-foreground"
                    >Pick a curated seed or enter custom HEX</span
                >
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <!-- Curated Presets -->
                <ToggleGroup.Root
                    type="single"
                    value={designSystem.sourceColor.toLowerCase()}
                    onValueChange={(val) => {
                        if (val) {
                            designSystem.sourceColor = val;
                            customHexInput = val;
                        }
                    }}
                    class="flex flex-wrap items-center gap-2"
                >
                    {#each M3_SEED_PRESETS as preset (preset.name)}
                        {@const active =
                            designSystem.sourceColor.toLowerCase() ===
                            preset.hex.toLowerCase()}
                        <ToggleGroup.Item
                            value={preset.hex.toLowerCase()}
                            title="{preset.label} ({preset.hex})"
                            class="relative flex size-8 shrink-0 items-center justify-center rounded-full border p-0 transition-all hover:scale-110 {active
                                ? 'ring-2 ring-primary ring-offset-2 scale-105 shadow-sm'
                                : 'border-border/60 opacity-85 hover:opacity-100'}"
                            style="background-color: {preset.hex}"
                        >
                            {#if active}
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/check')}
                                    tabler={() =>
                                        import('@tabler/icons-svelte/icons/check')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/Tick02Icon')}
                                    phosphor={() =>
                                        import('phosphor-svelte/lib/CheckIcon')}
                                    remixicon={() =>
                                        import('remixicon-svelte/icons/check-line')}
                                    class="size-3.5 text-white drop-shadow-md"
                                />
                            {/if}
                        </ToggleGroup.Item>
                    {/each}
                </ToggleGroup.Root>

                <div class="h-6 w-px bg-border mx-1"></div>

                <!-- Custom Native Picker + Hex Input -->
                <div
                    class="flex items-center gap-1.5 rounded-[var(--radius)] border border-border bg-background px-2 py-1"
                >
                    <label
                        class="relative flex cursor-pointer items-center justify-center"
                    >
                        <input
                            type="color"
                            value={designSystem.sourceColor}
                            oninput={(e) => {
                                const hex = (
                                    e.currentTarget as HTMLInputElement
                                ).value;
                                customHexInput = hex;
                                designSystem.sourceColor = hex;
                            }}
                            class="absolute inset-0 size-full opacity-0 cursor-pointer"
                        />
                        <span
                            class="size-5 rounded-full border border-border shadow-xs transition-transform hover:scale-110"
                            style="background-color: {designSystem.sourceColor}"
                        ></span>
                    </label>
                    <Input
                        type="text"
                        value={customHexInput}
                        oninput={(e) =>
                            handleHexInput(
                                (e.currentTarget as HTMLInputElement).value,
                            )}
                        placeholder="#2563eb"
                        maxlength={7}
                        class="h-7 w-20 border-0 p-0 font-mono text-xs uppercase focus-visible:ring-0 shadow-none bg-transparent"
                    />
                </div>
            </div>
        </div>

        <!-- Sub-row 2: Scheme Variant -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span
                    class="text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                    >Scheme Variant</span
                >
                <span class="text-xs text-muted-foreground">
                    {M3_VARIANTS.find((v) => v.value === designSystem.variant)
                        ?.description ?? ''}
                </span>
            </div>
            <ToggleGroup.Root
                type="single"
                variant="outline"
                value={designSystem.variant}
                onValueChange={(val) => {
                    if (val) {
                        designSystem.variant = val as M3Variant;
                    }
                }}
                class="grid w-full grid-cols-2 gap-2 sm:grid-cols-4 md:grid-cols-7"
            >
                {#each M3_VARIANTS as v (v.value)}
                    {@const active = designSystem.variant === v.value}
                    <ToggleGroup.Item
                        value={v.value}
                        class="h-auto p-0 border-0 bg-transparent"
                    >
                        {#snippet child({ props })}
                            <Card.Root
                                {...props}
                                class="h-full w-full cursor-pointer flex flex-col items-center justify-center gap-1 p-2.5 text-center text-xs font-medium transition-all bg-transparent shadow-none hover:bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {active
                                    ? 'border-primary bg-primary/10 ring-1 ring-primary text-primary font-semibold'
                                    : 'border-border text-foreground hover:text-foreground'}"
                            >
                                <Card.Content class="p-0 flex flex-col items-center justify-center gap-1">
                                    <span>{v.label}</span>
                                </Card.Content>
                            </Card.Root>
                        {/snippet}
                    </ToggleGroup.Item>
                {/each}
            </ToggleGroup.Root>
        </div>

        <!-- Sub-row 3: Contrast Level Slider -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <span
                    class="text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                    >Contrast Level</span
                >
                <span class="text-xs font-mono text-muted-foreground">
                    {designSystem.contrast > 0
                        ? `+${designSystem.contrast.toFixed(2)}`
                        : designSystem.contrast.toFixed(2)}
                    {#if designSystem.contrast <= -0.5}
                        (Low)
                    {:else if designSystem.contrast === 0}
                        (Normal)
                    {:else if designSystem.contrast >= 0.8}
                        (High / AAA)
                    {:else}
                        (Medium)
                    {/if}
                </span>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[11px] text-muted-foreground">-1.0 (Low)</span
                >
                <Slider
                    type="single"
                    min={-1}
                    max={1}
                    step={0.05}
                    value={designSystem.contrast}
                    onValueChange={(val) => {
                        designSystem.contrast = val;
                    }}
                    class="grow"
                />
                <span class="text-[11px] text-muted-foreground"
                    >+1.0 (High)</span
                >
                <Button
                    variant="ghost"
                    size="xs"
                    onclick={() => (designSystem.contrast = 0.0)}
                    disabled={designSystem.contrast === 0.0}
                    class="text-[11px] h-7 px-2"
                >
                    Reset
                </Button>
            </div>
        </div>

        <!-- Sub-row 4: Tonal Palette Inspector (kendell.dev/m3-svelte/theme style) -->
        {#if ramps}
            <div class="space-y-2 pt-2 border-t border-border/60">
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-semibold uppercase tracking-wider text-muted-foreground"
                        >Tonal Palette Inspector</span
                    >
                    <span
                        class="text-[11px] text-muted-foreground font-mono inline-flex items-center gap-1"
                    >
                        <span>Tones: 10 (Dark)</span>
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/arrow-right')}
                            tabler={() =>
                                import('@tabler/icons-svelte/icons/arrow-right')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/ArrowRight01Icon')}
                            phosphor={() =>
                                import('phosphor-svelte/lib/ArrowRightIcon')}
                            remixicon={() =>
                                import('remixicon-svelte/icons/arrow-right-line')}
                            class="size-3 text-muted-foreground"
                        />
                        <span>98 (Light)</span>
                    </span>
                </div>
                <div class="space-y-1.5">
                    <!-- Primary Ramp -->
                    <div class="flex items-center gap-2">
                        <span
                            class="w-16 shrink-0 text-[11px] font-medium text-muted-foreground"
                            >Primary</span
                        >
                        <div
                            class="flex grow overflow-hidden rounded-[var(--radius-sm)] border border-border/40 shadow-2xs"
                        >
                            {#each ramps.primary as item (item.tone)}
                                <div
                                    class="h-5 grow transition-colors hover:scale-105"
                                    style="background-color: {item.hex}"
                                    title="Tone {item.tone}: {item.hex}"
                                ></div>
                            {/each}
                        </div>
                    </div>
                    <!-- Secondary Ramp -->
                    <div class="flex items-center gap-2">
                        <span
                            class="w-16 shrink-0 text-[11px] font-medium text-muted-foreground"
                            >Secondary</span
                        >
                        <div
                            class="flex grow overflow-hidden rounded-[var(--radius-sm)] border border-border/40 shadow-2xs"
                        >
                            {#each ramps.secondary as item (item.tone)}
                                <div
                                    class="h-5 grow transition-colors hover:scale-105"
                                    style="background-color: {item.hex}"
                                    title="Tone {item.tone}: {item.hex}"
                                ></div>
                            {/each}
                        </div>
                    </div>
                    <!-- Tertiary Ramp -->
                    <div class="flex items-center gap-2">
                        <span
                            class="w-16 shrink-0 text-[11px] font-medium text-muted-foreground"
                            >Tertiary</span
                        >
                        <div
                            class="flex grow overflow-hidden rounded-[var(--radius-sm)] border border-border/40 shadow-2xs"
                        >
                            {#each ramps.tertiary as item (item.tone)}
                                <div
                                    class="h-5 grow transition-colors hover:scale-105"
                                    style="background-color: {item.hex}"
                                    title="Tone {item.tone}: {item.hex}"
                                ></div>
                            {/each}
                        </div>
                    </div>
                    <!-- Neutral Ramp -->
                    <div class="flex items-center gap-2">
                        <span
                            class="w-16 shrink-0 text-[11px] font-medium text-muted-foreground"
                            >Surface</span
                        >
                        <div
                            class="flex grow overflow-hidden rounded-[var(--radius-sm)] border border-border/40 shadow-2xs"
                        >
                            {#each ramps.neutral as item (item.tone)}
                                <div
                                    class="h-5 grow transition-colors hover:scale-105"
                                    style="background-color: {item.hex}"
                                    title="Tone {item.tone}: {item.hex}"
                                ></div>
                            {/each}
                        </div>
                    </div>
                    <!-- Neutral Variant Ramp -->
                    <div class="flex items-center gap-2">
                        <span
                            class="w-16 shrink-0 text-[11px] font-medium text-muted-foreground"
                            >Outline</span
                        >
                        <div
                            class="flex grow overflow-hidden rounded-[var(--radius-sm)] border border-border/40 shadow-2xs"
                        >
                            {#each ramps.neutralVariant as item (item.tone)}
                                <div
                                    class="h-5 grow transition-colors hover:scale-105"
                                    style="background-color: {item.hex}"
                                    title="Tone {item.tone}: {item.hex}"
                                ></div>
                            {/each}
                        </div>
                    </div>
                </div>
            </div>
        {/if}
        </Card.Content>
    </Card.Root>

    <Separator />

    <!-- 3. Corner Radius -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <Label class="text-sm font-semibold">Corner Radius</Label>
            <span class="text-xs text-muted-foreground">
                {#if designSystem.radius === null}
                    <span class="text-primary font-medium">Preset</span>
                    ({STYLE_PRESET_RADII[designSystem.style] ?? '0.5rem'})
                {:else}
                    <span class="text-amber-500 font-medium">Override</span>
                    ({designSystem.radius})
                {/if}
            </span>
        </div>
        <ToggleGroup.Root
            type="single"
            value={designSystem.radius ?? 'preset'}
            onValueChange={(val) => {
                if (val === 'preset') {
                    designSystem.radius = null;
                } else if (val) {
                    designSystem.radius = val as RadiusValue;
                }
            }}
            class="grid w-full grid-cols-2 gap-2 sm:grid-cols-6"
        >
            <!-- Preset -->
            <ToggleGroup.Item
                value="preset"
                class="h-auto p-0 border-0 bg-transparent"
            >
                {#snippet child({ props })}
                    <Card.Root
                        {...props}
                        class="h-full w-full cursor-pointer flex flex-col items-center justify-center gap-1.5 p-2 text-center text-xs font-medium transition-all hover:bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {designSystem.radius ===
                        null
                            ? 'border-primary bg-primary/10 ring-1 ring-primary text-primary font-semibold shadow-xs'
                            : 'border-border text-muted-foreground hover:text-foreground'}"
                    >
                        <Card.Content
                            class="p-0 flex flex-col items-center justify-center gap-1.5"
                        >
                            <div
                                class="size-4 border-2 border-current transition-all"
                                style="border-top-left-radius: {activePresetRadius}; border-top-right-radius: {activePresetRadius};"
                            ></div>
                            <span>Preset</span>
                            <span class="text-[10px] opacity-75"
                                >{activePresetRadius}</span
                            >
                        </Card.Content>
                    </Card.Root>
                {/snippet}
            </ToggleGroup.Item>

            <!-- Overrides -->
            {#each RADII as r (r.name)}
                {@const active = designSystem.radius === r.name}
                <ToggleGroup.Item
                    value={r.name}
                    class="h-auto p-0 border-0 bg-transparent"
                >
                    {#snippet child({ props })}
                        <Card.Root
                            {...props}
                            class="h-full w-full cursor-pointer flex flex-col items-center justify-center gap-1.5 p-2 text-center text-xs font-medium transition-all hover:bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {active
                                ? 'border-primary bg-primary/10 ring-1 ring-primary text-primary font-semibold shadow-xs'
                                : 'border-border text-muted-foreground hover:text-foreground'}"
                        >
                            <Card.Content
                                class="p-0 flex flex-col items-center justify-center gap-1.5"
                            >
                                <div
                                    class="size-4 border-2 border-current transition-all"
                                    style="border-top-left-radius: {r.value}; border-top-right-radius: {r.value};"
                                ></div>
                                <span>{r.label}</span>
                                <span class="text-[10px] opacity-75"
                                    >{r.value}</span
                                >
                            </Card.Content>
                        </Card.Root>
                    {/snippet}
                </ToggleGroup.Item>
            {/each}
        </ToggleGroup.Root>
    </div>

    <Separator />

    <!-- Icon Library (5 Upstream Stroke Engines) -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div class="space-y-0.5">
                <Label class="text-sm font-semibold">Icon Library</Label>
                <p class="text-xs text-muted-foreground">
                    5 stroke-based icon engines supported by shadcn-svelte.
                </p>
            </div>
            <span class="text-xs font-mono capitalize text-muted-foreground"
                >Current: {designSystem.iconLibrary}</span
            >
        </div>
        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-5">
            {#each ICON_LIBRARIES as lib (lib.name)}
                {@const active = designSystem.iconLibrary === lib.name}
                <Card.Root
                    role="button"
                    tabindex={0}
                    onclick={() => (designSystem.iconLibrary = lib.name)}
                    onkeydown={(e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            designSystem.iconLibrary = lib.name;
                        }
                    }}
                    class="cursor-pointer gap-2.5 p-3 text-left transition-all hover:bg-muted/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring {active
                        ? 'border-primary bg-primary/10 ring-1 ring-primary shadow-xs'
                        : 'border-border hover:text-foreground'}"
                >
                    <Card.Header class="p-0">
                        <div class="flex items-center justify-between">
                            <Card.Title class="text-xs font-semibold text-foreground">
                                {lib.label}
                            </Card.Title>
                            {#if active}
                                <span class="size-2 rounded-full bg-primary"></span>
                            {/if}
                        </div>
                    </Card.Header>
                    <Card.Content class="p-0">
                        <div
                            class="flex items-center justify-between gap-1.5 pt-0.5 text-muted-foreground"
                        >
                        {#if lib.name === 'lucide'}
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/house')}
                                class=" size-6"
                            />
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/film')}
                                class=" size-6"
                            />
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/disc-3')}
                                class=" size-6"
                            />
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/layout-grid')}
                                class=" size-6"
                            />
                            <Icon
                                lucide={() =>
                                    import('lucide-svelte/icons/sparkles')}
                                class=" size-6"
                            />
                        {:else if lib.name === 'hugeicons'}
                            <Icon
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/Home01Icon')}
                                class=" size-6"
                            />
                            <Icon
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/Film01Icon')}
                                class=" size-6"
                            />
                            <Icon
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/RecordIcon')}
                                class=" size-6"
                            />
                            <Icon
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/DashboardSquare01Icon')}
                                class=" size-6"
                            />
                            <Icon
                                hugeicons={() =>
                                    import('@hugeicons/core-free-icons/SparklesIcon')}
                                class=" size-6"
                            />
                        {:else if lib.name === 'tabler'}
                            <Icon
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/home')}
                                class=" size-6"
                            />
                            <Icon
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/movie')}
                                class=" size-6"
                            />
                            <Icon
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/disc')}
                                class=" size-6"
                            />
                            <Icon
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/layout-grid')}
                                class=" size-6"
                            />
                            <Icon
                                tabler={() =>
                                    import('@tabler/icons-svelte/icons/sparkles')}
                                class=" size-6"
                            />
                        {:else if lib.name === 'phosphor'}
                            <Icon
                                phosphor={() =>
                                    import('phosphor-svelte/lib/HouseIcon')}
                                class=" size-6"
                            />
                            <Icon
                                phosphor={() =>
                                    import('phosphor-svelte/lib/FilmSlateIcon')}
                                class=" size-6"
                            />
                            <Icon
                                phosphor={() =>
                                    import('phosphor-svelte/lib/DiscIcon')}
                                class=" size-6"
                            />
                            <Icon
                                phosphor={() =>
                                    import('phosphor-svelte/lib/SquaresFourIcon')}
                                class=" size-6"
                            />
                            <Icon
                                phosphor={() =>
                                    import('phosphor-svelte/lib/SparkleIcon')}
                                class=" size-6"
                            />
                        {:else if lib.name === 'remixicon'}
                            <Icon
                                remixicon={() =>
                                    import('remixicon-svelte/icons/home-line')}
                                class=" size-6"
                            />
                            <Icon
                                remixicon={() =>
                                    import('remixicon-svelte/icons/film-line')}
                                class=" size-6"
                            />
                            <Icon
                                remixicon={() =>
                                    import('remixicon-svelte/icons/disc-line')}
                                class=" size-6"
                            />
                            <Icon
                                remixicon={() =>
                                    import('remixicon-svelte/icons/dashboard-line')}
                                class=" size-6"
                            />
                            <Icon
                                remixicon={() =>
                                    import('remixicon-svelte/icons/sparkling-line')}
                                class=" size-6"
                            />
                        {/if}
                    </div>
                    </Card.Content>
                </Card.Root>
            {/each}
        </div>
    </div>

    <Separator />

    <!-- Menu Background -->
    <div
        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="space-y-0.5">
            <div class="flex items-center gap-2">
                <Label class="text-sm font-semibold">Menu Background</Label>
                {#if isDark}
                    <Badge
                        variant="outline"
                        class="text-[10px] text-muted-foreground"
                    >
                        Locked in Dark mode
                    </Badge>
                {/if}
            </div>
            <p class="text-xs text-muted-foreground">
                {isDark
                    ? 'Sidebar styling is locked to default while in dark mode.'
                    : 'Sidebar and navigation surface styling.'}
            </p>
        </div>
        <Tabs.Root
            activationMode="manual"
            value={isDark ? 'default' : designSystem.menuColor}
            onValueChange={(val) => {
                if (val && !isDark) {
                    designSystem.menuColor = val as MenuColorValue;
                }
            }}
        >
            <Tabs.List class="h-9 p-1">
                <Tabs.Trigger
                    value="default"
                    class="px-3 py-1 text-xs"
                    disabled={isDark}>Default</Tabs.Trigger
                >
                <Tabs.Trigger
                    value="inverted"
                    class="px-3 py-1 text-xs"
                    disabled={isDark}>Inverted</Tabs.Trigger
                >
            </Tabs.List>
        </Tabs.Root>
    </div>

    <Separator />

    <!-- Font Size -->
    <div
        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="space-y-0.5">
            <Label class="text-sm font-semibold">Font Size</Label>
            <p class="text-xs text-muted-foreground">
                Root rem scaling for typography and icons across all views.
            </p>
        </div>
        <Tabs.Root
            activationMode="manual"
            value={String(designSystem.fontSize)}
            onValueChange={(val) => {
                if (val) {
                    designSystem.fontSize = Number(val) as FontSizeValue;
                }
            }}
        >
            <Tabs.List class="h-9 p-1">
                <Tabs.Trigger
                    value="75"
                    class="px-3 py-1 text-xs"
                    onpointerup={() => (designSystem.fontSize = 75)}
                >
                    12px
                </Tabs.Trigger>
                <Tabs.Trigger
                    value="100"
                    class="px-3 py-1 text-xs"
                    onpointerup={() => (designSystem.fontSize = 100)}
                >
                    16px
                </Tabs.Trigger>
                <Tabs.Trigger
                    value="125"
                    class="px-3 py-1 text-xs"
                    onpointerup={() => (designSystem.fontSize = 125)}
                >
                    20px
                </Tabs.Trigger>
                <Tabs.Trigger
                    value="150"
                    class="px-3 py-1 text-xs"
                    onpointerup={() => (designSystem.fontSize = 150)}
                >
                    24px
                </Tabs.Trigger>
            </Tabs.List>
        </Tabs.Root>
    </div>

    <!-- Interactive Live Sandbox Preview -->
    <Card.Root>
        <Card.Header>
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <Card.Title class="text-base font-semibold"
                        >Live Component Sandbox</Card.Title
                    >
                    <Card.Description>
                        All UI components adapt in real time to your active
                        style, corner radius, tokens, and icon engine.
                    </Card.Description>
                </div>
                <Badge variant="secondary" class="gap-1.5">
                    <span
                        class="size-2 rounded-full bg-emerald-500 animate-pulse"
                    ></span>
                    Active
                </Badge>
            </div>
        </Card.Header>
        <Card.Content class="space-y-6">
            <!-- Button Showcase -->
            <div class="space-y-2">
                <span
                    class="text-xs font-medium text-muted-foreground uppercase tracking-wider"
                    >Buttons</span
                >
                <div class="flex flex-wrap items-center gap-2">
                    <Button variant="default">
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/sparkles')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/SparklesIcon')}
                            class="size-4"
                        />
                        Primary Action
                    </Button>
                    <Button variant="secondary">Secondary</Button>
                    <Button variant="outline">
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/download')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Download01Icon')}
                            class="size-4"
                        />
                        Outline
                    </Button>
                    <Button variant="destructive">
                        <Icon
                            lucide={() => import('lucide-svelte/icons/trash-2')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Delete02Icon')}
                            class="size-4"
                        />
                        Destructive
                    </Button>
                    <Button variant="ghost" size="icon">
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/settings')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Settings01Icon')}
                            class="size-4"
                        />
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        onclick={triggerSampleToast}
                    >
                        Trigger Toast
                    </Button>
                    <DropdownMenu.Root>
                        <DropdownMenu.Trigger>
                            {#snippet child({ props })}
                                <Button
                                    variant="outline"
                                    size="sm"
                                    class="gap-1.5"
                                    {...props}
                                >
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/menu')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/Menu01Icon')}
                                        class="size-3.5"
                                    />
                                    <span>Dropdown Menu</span>
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/chevron-down')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/ArrowDown01Icon')}
                                        class="size-3 opacity-70"
                                    />
                                </Button>
                            {/snippet}
                        </DropdownMenu.Trigger>
                        <DropdownMenu.Content align="start" class="w-48">
                            <DropdownMenu.Label class="text-xs font-semibold"
                                >Live Menu Preview</DropdownMenu.Label
                            >
                            <DropdownMenu.Separator />
                            <DropdownMenu.Group>
                                <DropdownMenu.Item>
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/user')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/UserIcon')}
                                        class="size-4 mr-2"
                                    />
                                    Profile
                                </DropdownMenu.Item>
                                <DropdownMenu.Item>
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/palette')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/PaintBoardIcon')}
                                        class="size-4 mr-2"
                                    />
                                    Appearance
                                </DropdownMenu.Item>
                                <DropdownMenu.Item>
                                    <Icon
                                        lucide={() =>
                                            import('lucide-svelte/icons/settings')}
                                        hugeicons={() =>
                                            import('@hugeicons/core-free-icons/Settings01Icon')}
                                        class="size-4 mr-2"
                                    />
                                    Settings
                                </DropdownMenu.Item>
                            </DropdownMenu.Group>
                            <DropdownMenu.Separator />
                            <DropdownMenu.Item
                                class="text-destructive focus:text-destructive"
                            >
                                <Icon
                                    lucide={() =>
                                        import('lucide-svelte/icons/log-out')}
                                    hugeicons={() =>
                                        import('@hugeicons/core-free-icons/Logout01Icon')}
                                    class="size-4 mr-2"
                                />
                                Log out
                            </DropdownMenu.Item>
                        </DropdownMenu.Content>
                    </DropdownMenu.Root>
                </div>
            </div>

            <!-- Badges & Status -->
            <div class="space-y-2">
                <span
                    class="text-xs font-medium text-muted-foreground uppercase tracking-wider"
                    >Badges</span
                >
                <div class="flex flex-wrap items-center gap-2">
                    <Badge variant="default">Default</Badge>
                    <Badge variant="secondary">Secondary</Badge>
                    <Badge variant="outline">Outline</Badge>
                    <Badge variant="destructive">Destructive</Badge>
                </div>
            </div>

            <!-- Tabs & Progress Showcase -->
            <div class="space-y-2">
                <span
                    class="text-xs font-medium text-muted-foreground uppercase tracking-wider"
                    >Tabs & Progress</span
                >
                <div class="grid gap-4 md:grid-cols-2">
                    <Tabs.Root value="overview" class="w-full">
                        <Tabs.List class="grid w-full grid-cols-3">
                            <Tabs.Trigger value="overview"
                                >Overview</Tabs.Trigger
                            >
                            <Tabs.Trigger value="analytics"
                                >Analytics</Tabs.Trigger
                            >
                            <Tabs.Trigger value="reports">Reports</Tabs.Trigger>
                        </Tabs.List>
                        <Tabs.Content
                            value="overview"
                            class="mt-2 text-xs text-muted-foreground"
                        >
                            Live preview of tabs active states styled by active
                            style rules.
                        </Tabs.Content>
                        <Tabs.Content
                            value="analytics"
                            class="mt-2 text-xs text-muted-foreground"
                        >
                            Detailed metrics and token performance.
                        </Tabs.Content>
                        <Tabs.Content
                            value="reports"
                            class="mt-2 text-xs text-muted-foreground"
                        >
                            System health and theme logs.
                        </Tabs.Content>
                    </Tabs.Root>

                    <div class="flex flex-col justify-center gap-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-medium">System Metric</span>
                            <span class="text-muted-foreground"
                                >{sliderValue[0]}%</span
                            >
                        </div>
                        <Progress value={sliderValue[0]} class="h-2" />
                        <Slider
                            type="multiple"
                            bind:value={sliderValue}
                            min={0}
                            max={100}
                            step={1}
                            class="w-full"
                        />
                    </div>
                </div>
            </div>

            <!-- Form Elements & Avatars -->
            <div class="space-y-2">
                <span
                    class="text-xs font-medium text-muted-foreground uppercase tracking-wider"
                    >Controls & Avatars</span
                >
                <div class="flex flex-wrap items-center gap-6">
                    <div class="flex items-center space-x-2">
                        <Switch
                            id="sandbox-switch"
                            bind:checked={sandboxChecked}
                        />
                        <Label for="sandbox-switch">Interactive Switch</Label>
                    </div>
                    <div class="flex items-center space-x-2">
                        <Checkbox
                            id="sandbox-check"
                            bind:checked={sandboxCheckbox}
                        />
                        <Label for="sandbox-check">Checkbox</Label>
                    </div>
                    <div class="flex max-w-xs items-center gap-2">
                        <Input placeholder="Customizer input test..." />
                    </div>
                    <div class="flex items-center gap-2">
                        <Avatar.Root class="size-8">
                            <Avatar.Image
                                src="https://github.com/shadcn.png"
                                alt="Shadcn"
                            />
                            <Avatar.Fallback>CN</Avatar.Fallback>
                        </Avatar.Root>
                        <Avatar.Root class="size-8">
                            <Avatar.Fallback
                                class="bg-primary text-primary-foreground font-semibold"
                                >EX</Avatar.Fallback
                            >
                        </Avatar.Root>
                    </div>
                </div>
            </div>

            <!-- Menu Navigation States Showcase -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span
                        class="text-xs font-medium text-muted-foreground uppercase tracking-wider"
                        >Menu Navigation States</span
                    >
                    <span class="text-xs text-muted-foreground font-mono">
                        Active: var(--primary-container) /
                        var(--on-primary-container)
                    </span>
                </div>
                <div
                    class="grid grid-cols-1 sm:grid-cols-3 gap-2 rounded-[var(--radius)] border border-border/50 bg-sidebar/50 p-3"
                >
                    <div
                        data-slot="sidebar-menu-button"
                        data-sidebar="menu-button"
                        class="flex items-center gap-2 rounded-[var(--radius)] px-3 py-2 text-sm text-sidebar-foreground cursor-default transition-all"
                    >
                        <Icon
                            lucide={() => import('lucide-svelte/icons/circle')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/CircleIcon')}
                            class="size-4"
                        />
                        <span>Resting (Default)</span>
                    </div>
                    <div
                        data-slot="sidebar-menu-button"
                        data-sidebar="menu-button"
                        class="flex items-center gap-2 rounded-[var(--radius)] px-3 py-2 text-sm bg-sidebar-accent text-sidebar-accent-foreground cursor-default transition-all"
                    >
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/mouse-pointer')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/Cursor01Icon')}
                            class="size-4"
                        />
                        <span>Hover State</span>
                    </div>
                    <div
                        data-slot="sidebar-menu-button"
                        data-sidebar="menu-button"
                        data-active="true"
                        class="flex items-center gap-2 rounded-[var(--radius)] px-3 py-2 text-sm cursor-default transition-all"
                    >
                        <Icon
                            lucide={() =>
                                import('lucide-svelte/icons/check-circle-2')}
                            hugeicons={() =>
                                import('@hugeicons/core-free-icons/CheckmarkCircle02Icon')}
                            class="size-4"
                        />
                        <span>Active State</span>
                    </div>
                </div>
            </div>
        </Card.Content>
    </Card.Root>
</div>
