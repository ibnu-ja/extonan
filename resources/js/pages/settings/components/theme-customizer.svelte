<script lang="ts">
	import { toast } from "svelte-sonner";
	import { Icon } from "@/components/icon";
	import * as Avatar from "@/components/ui/avatar";
	import { Badge } from "@/components/ui/badge";
	import { Button } from "@/components/ui/button";
	import * as Card from "@/components/ui/card";
	import { Checkbox } from "@/components/ui/checkbox";
	import { Input } from "@/components/ui/input";
	import { Label } from "@/components/ui/label";
	import { Progress } from "@/components/ui/progress";
	import { Separator } from "@/components/ui/separator";
	import { Slider } from "@/components/ui/slider";
	import { Switch } from "@/components/ui/switch";
	import * as Tabs from "@/components/ui/tabs";
	import {
		useDesignSystem,
		STYLES,
		BASE_THEMES,
		THEMES,
		RADII,
		MENU_COLORS,
		MENU_ACCENTS
	} from "@/lib/design-system";
	import type { StyleName, RadiusValue, MenuColorValue, MenuAccentValue } from "@/lib/design-system";

	const designSystem = useDesignSystem();

	let presetInput = $state("");
	let sandboxChecked = $state(true);
	let sandboxCheckbox = $state(true);
	let sliderValue = $state([65]);

	function copyPreset() {
		if (typeof navigator !== "undefined" && navigator.clipboard) {
			navigator.clipboard.writeText(designSystem.preset);
			toast.success("Preset code copied to clipboard!", {
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
			toast.success("Preset applied successfully!", {
				description: `Active preset: ${code}`,
			});
			presetInput = "";
		} catch {
			toast.error("Failed to decode preset code.", {
				description: "Please check that the code matches the standard format.",
			});
		}
	}

	function handleReset() {
		designSystem.reset();
		toast.info("Design system reset to default (Nova).");
	}

	function handleRandomize() {
		const styles = STYLES.map((s) => s.name);
		const baseColors = BASE_THEMES.map((b) => b.name);
		const themes = THEMES.map((t) => t.name);
		const radii = RADII.map((r) => r.name);
		const menuColors = MENU_COLORS.map((m) => m.value);
		const menuAccents = MENU_ACCENTS.map((m) => m.value);

		designSystem.style = styles[Math.floor(Math.random() * styles.length)] as StyleName;
		designSystem.baseColor = baseColors[Math.floor(Math.random() * baseColors.length)];
		designSystem.theme = themes[Math.floor(Math.random() * themes.length)];
		designSystem.radius = radii[Math.floor(Math.random() * radii.length)] as RadiusValue;
		designSystem.menuColor = menuColors[Math.floor(Math.random() * menuColors.length)] as MenuColorValue;
		designSystem.menuAccent = menuAccents[Math.floor(Math.random() * menuAccents.length)] as MenuAccentValue;

		toast.success("Random preset applied!", {
			description: `${designSystem.style.toUpperCase()} / ${designSystem.baseColor} / ${designSystem.theme}`,
		});
	}

	function triggerSampleToast() {
		toast.success("Design system token test!", {
			description: `Style: ${designSystem.style.toUpperCase()}, Theme: ${designSystem.theme}, Radius: ${designSystem.radius}`,
		});
	}
</script>

<svelte:window
	onkeydown={(e) => {
		if ((e.key === "r" || e.key === "R") && !e.metaKey && !e.ctrlKey) {
			if (
				(e.target instanceof HTMLElement && e.target.isContentEditable) ||
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
				<div>
					<Card.Title class="text-base font-semibold">Preset Sharing</Card.Title>
					<Card.Description>
						Import, export, or share complete design system configurations via 9-character preset codes. Press <kbd class="rounded border px-1.5 py-0.5 font-mono text-xs text-muted-foreground">R</kbd> to randomize.
					</Card.Description>
				</div>
				<div class="flex items-center gap-2">
					<Badge variant="outline" class="font-mono text-xs tracking-wider">
						{designSystem.preset}
					</Badge>
					<Button variant="outline" size="sm" onclick={copyPreset}>
						<Icon
							lucide="Copy"
							tabler="IconCopy"
							hugeicons="Copy01Icon"
							phosphor="CopyIcon"
							remixicon="RiFileCopyLine"
							class="size-3.5"
						/>
						Copy Code
					</Button>
					<Button variant="outline" size="sm" onclick={handleRandomize}>
						<Icon
							lucide="Shuffle"
							tabler="IconArrowsShuffle"
							hugeicons="Shuffle01Icon"
							phosphor="ShuffleIcon"
							remixicon="RiShuffleLine"
							class="size-3.5"
						/>
						Randomize
					</Button>
					<Button variant="ghost" size="sm" onclick={handleReset}>
						<Icon
							lucide="RotateCcw"
							tabler="IconRotate"
							hugeicons="RotateLeft01Icon"
							phosphor="ArrowCounterClockwiseIcon"
							remixicon="RiRestartLine"
							class="size-3.5"
						/>
						Reset
					</Button>
				</div>
			</div>
		</Card.Header>
		<Card.Content>
			<div class="flex max-w-md items-center gap-2">
				<Input
					placeholder="Paste preset code (e.g. b1FSSTiJW)..."
					bind:value={presetInput}
					class="font-mono text-sm"
				/>
				<Button size="sm" onclick={handleApplyPreset} disabled={!presetInput.trim()}>
					Apply
				</Button>
			</div>
		</Card.Content>
	</Card.Root>

	<!-- Style Selector (8 Styles) -->
	<div class="space-y-3">
		<div class="flex items-center justify-between">
			<Label class="text-sm font-semibold">1. Interface Style (8 Upstream Styles)</Label>
			<span class="text-xs text-muted-foreground">Current: {designSystem.style}</span>
		</div>
		<div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
			{#each STYLES as s (s.name)}
				{@const active = designSystem.style === s.name}
				<button
					type="button"
					onclick={() => (designSystem.style = s.name as StyleName)}
					class="flex flex-col items-start gap-1 rounded-xl border p-3 text-left transition-all hover:bg-muted/50 {active
						? 'border-primary bg-primary/5 ring-1 ring-primary'
						: 'border-border'}"
				>
					<div class="flex w-full items-center justify-between">
						<span class="font-medium text-sm text-foreground">{s.title}</span>
						{#if active}
							<Icon lucide="Check" hugeicons="Tick02Icon" class="size-3.5 text-primary" />
						{/if}
					</div>
					<span class="text-xs text-muted-foreground line-clamp-2 leading-relaxed">
						{s.description}
					</span>
				</button>
			{/each}
		</div>
	</div>

	<Separator />

	<!-- Base Color and Theme Accent -->
	<div class="grid gap-6 md:grid-cols-2">
		<!-- Base Color -->
		<div class="space-y-3">
			<div class="flex items-center justify-between">
				<Label class="text-sm font-semibold">2. Base Color Palette</Label>
				<span class="text-xs capitalize text-muted-foreground">{designSystem.baseColor}</span>
			</div>
			<div class="grid grid-cols-3 gap-2">
				{#each BASE_THEMES as b (b.name)}
					{@const active = designSystem.baseColor === b.name}
					<button
						type="button"
						onclick={() => (designSystem.baseColor = b.name)}
						class="flex items-center gap-2 rounded-lg border p-2 text-left text-xs font-medium transition-all hover:bg-muted/50 {active
							? 'border-primary bg-primary/5 ring-1 ring-primary'
							: 'border-border'}"
					>
						<span
							class="size-3.5 rounded-full border"
							style="background-color: {b.cssVars.light.primary}"
						></span>
						<span>{b.title}</span>
					</button>
				{/each}
			</div>
		</div>

		<!-- Corner Radius -->
		<div class="space-y-3">
			<div class="flex items-center justify-between">
				<Label class="text-sm font-semibold">3. Corner Radius</Label>
				<span class="text-xs capitalize text-muted-foreground">{designSystem.radius}</span>
			</div>
			<div class="grid grid-cols-5 gap-2">
				{#each RADII as r (r.name)}
					{@const active = designSystem.radius === r.name}
					<button
						type="button"
						onclick={() => (designSystem.radius = r.name as RadiusValue)}
						class="flex flex-col items-center justify-center gap-1.5 rounded-lg border p-2 text-center text-xs font-medium transition-all hover:bg-muted/50 {active
							? 'border-primary bg-primary/5 ring-1 ring-primary'
							: 'border-border'}"
					>
						<div
							class="size-4 border-2 border-foreground/70"
							style="border-top-left-radius: {r.value}; border-top-right-radius: {r.value};"
						></div>
						<span>{r.label}</span>
					</button>
				{/each}
			</div>
		</div>
	</div>

	<!-- Primary Accent Palette (24 Swatches) -->
	<div class="space-y-3">
		<div class="flex items-center justify-between">
			<Label class="text-sm font-semibold">4. Accent Theme Color (24 Palettes)</Label>
			<span class="text-xs capitalize text-muted-foreground">{designSystem.theme}</span>
		</div>
		<div class="flex flex-wrap gap-2.5">
			{#each THEMES as t (t.name)}
				{@const active = designSystem.theme === t.name}
				<button
					type="button"
					title={t.title}
					onclick={() => (designSystem.theme = t.name)}
					class="relative flex size-8 items-center justify-center rounded-full border transition-all hover:scale-110 {active
						? 'ring-2 ring-primary ring-offset-2'
						: 'border-border/60'}"
					style="background-color: {t.cssVars.light.primary}"
				>
					{#if active}
						<Icon lucide="Check" hugeicons="Tick02Icon" class="size-4 text-white drop-shadow-md" />
					{/if}
				</button>
			{/each}
		</div>
	</div>

	<Separator />

	<!-- Icon Library & Menu Options -->
	<div class="grid gap-6 md:grid-cols-3">
		<!-- Icon Library -->
		<div class="space-y-3">
			<Label class="text-sm font-semibold">5. Icon Library</Label>
			<div class="grid grid-cols-2 gap-2">
				<button
					type="button"
					onclick={() => (designSystem.iconLibrary = "lucide")}
					class="flex items-center justify-center gap-2 rounded-lg border p-2.5 text-xs font-medium transition-all {designSystem.iconLibrary ===
					'lucide'
						? 'border-primary bg-primary/5 ring-1 ring-primary'
						: 'border-border hover:bg-muted/50'}"
				>
					<Icon lucide="Sparkles" class="size-4" />
					<span>Lucide</span>
				</button>
				<button
					type="button"
					onclick={() => (designSystem.iconLibrary = "hugeicons")}
					class="flex items-center justify-center gap-2 rounded-lg border p-2.5 text-xs font-medium transition-all {designSystem.iconLibrary ===
					'hugeicons'
						? 'border-primary bg-primary/5 ring-1 ring-primary'
						: 'border-border hover:bg-muted/50'}"
				>
					<Icon hugeicons="SparklesIcon" class="size-4" />
					<span>Hugeicons</span>
				</button>
			</div>
		</div>

		<!-- Menu Color -->
		<div class="space-y-3">
			<Label class="text-sm font-semibold">6. Menu Background</Label>
			<div class="grid grid-cols-2 gap-2">
				{#each MENU_COLORS as mc (mc.value)}
					{@const active = designSystem.menuColor === mc.value}
					<button
						type="button"
						onclick={() => (designSystem.menuColor = mc.value as MenuColorValue)}
						class="rounded-lg border p-2 text-center text-xs font-medium transition-all {active
							? 'border-primary bg-primary/5 ring-1 ring-primary'
							: 'border-border hover:bg-muted/50'}"
					>
						{mc.label}
					</button>
				{/each}
			</div>
		</div>

		<!-- Menu Accent -->
		<div class="space-y-3">
			<Label class="text-sm font-semibold">7. Menu Accent</Label>
			<div class="grid grid-cols-2 gap-2">
				{#each MENU_ACCENTS as ma (ma.value)}
					{@const active = designSystem.menuAccent === ma.value}
					<button
						type="button"
						onclick={() => (designSystem.menuAccent = ma.value as MenuAccentValue)}
						class="rounded-lg border p-2 text-center text-xs font-medium transition-all {active
							? 'border-primary bg-primary/5 ring-1 ring-primary'
							: 'border-border hover:bg-muted/50'}"
					>
						{ma.label}
					</button>
				{/each}
			</div>
		</div>
	</div>

	<!-- Interactive Live Sandbox Preview -->
	<Card.Root class="border-2 border-primary/20 bg-muted/20">
		<Card.Header>
			<div class="flex items-center justify-between">
				<div class="space-y-1">
					<Card.Title class="text-base font-semibold">Live Component Sandbox</Card.Title>
					<Card.Description>
						All UI components adapt in real time to your active style, corner radius, tokens, and icon engine.
					</Card.Description>
				</div>
				<Badge variant="secondary" class="gap-1.5">
					<span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
					Active
				</Badge>
			</div>
		</Card.Header>
		<Card.Content class="space-y-6">
			<!-- Button Showcase -->
			<div class="space-y-2">
				<span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Buttons</span>
				<div class="flex flex-wrap items-center gap-2">
					<Button variant="default">
						<Icon lucide="Sparkles" hugeicons="SparklesIcon" class="size-4" />
						Primary Action
					</Button>
					<Button variant="secondary">
						Secondary
					</Button>
					<Button variant="outline">
						<Icon lucide="Download" hugeicons="Download01Icon" class="size-4" />
						Outline
					</Button>
					<Button variant="destructive">
						<Icon lucide="Trash2" hugeicons="Delete02Icon" class="size-4" />
						Destructive
					</Button>
					<Button variant="ghost" size="icon">
						<Icon lucide="Settings" hugeicons="Settings01Icon" class="size-4" />
					</Button>
					<Button variant="outline" size="sm" onclick={triggerSampleToast}>
						Trigger Toast
					</Button>
				</div>
			</div>

			<!-- Badges & Status -->
			<div class="space-y-2">
				<span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Badges</span>
				<div class="flex flex-wrap items-center gap-2">
					<Badge variant="default">Default</Badge>
					<Badge variant="secondary">Secondary</Badge>
					<Badge variant="outline">Outline</Badge>
					<Badge variant="destructive">Destructive</Badge>
				</div>
			</div>

			<!-- Tabs & Progress Showcase -->
			<div class="space-y-2">
				<span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Tabs & Progress</span>
				<div class="grid gap-4 md:grid-cols-2">
					<Tabs.Root value="overview" class="w-full">
						<Tabs.List class="grid w-full grid-cols-3">
							<Tabs.Trigger value="overview">Overview</Tabs.Trigger>
							<Tabs.Trigger value="analytics">Analytics</Tabs.Trigger>
							<Tabs.Trigger value="reports">Reports</Tabs.Trigger>
						</Tabs.List>
						<Tabs.Content value="overview" class="mt-2 text-xs text-muted-foreground">
							Live preview of tabs active states styled by active style rules.
						</Tabs.Content>
						<Tabs.Content value="analytics" class="mt-2 text-xs text-muted-foreground">
							Detailed metrics and token performance.
						</Tabs.Content>
						<Tabs.Content value="reports" class="mt-2 text-xs text-muted-foreground">
							System health and theme logs.
						</Tabs.Content>
					</Tabs.Root>

					<div class="flex flex-col justify-center gap-3">
						<div class="flex items-center justify-between text-xs">
							<span class="font-medium">System Metric</span>
							<span class="text-muted-foreground">{sliderValue[0]}%</span>
						</div>
						<Progress value={sliderValue[0]} class="h-2" />
						<Slider type="multiple" bind:value={sliderValue} min={0} max={100} step={1} class="w-full" />
					</div>
				</div>
			</div>

			<!-- Form Elements & Avatars -->
			<div class="space-y-2">
				<span class="text-xs font-medium text-muted-foreground uppercase tracking-wider">Controls & Avatars</span>
				<div class="flex flex-wrap items-center gap-6">
					<div class="flex items-center space-x-2">
						<Switch id="sandbox-switch" bind:checked={sandboxChecked} />
						<Label for="sandbox-switch">Interactive Switch</Label>
					</div>
					<div class="flex items-center space-x-2">
						<Checkbox id="sandbox-check" bind:checked={sandboxCheckbox} />
						<Label for="sandbox-check">Checkbox</Label>
					</div>
					<div class="flex max-w-xs items-center gap-2">
						<Input placeholder="Customizer input test..." />
					</div>
					<div class="flex items-center gap-2">
						<Avatar.Root class="size-8">
							<Avatar.Image src="https://github.com/shadcn.png" alt="Shadcn" />
							<Avatar.Fallback>CN</Avatar.Fallback>
						</Avatar.Root>
						<Avatar.Root class="size-8">
							<Avatar.Fallback class="bg-primary text-primary-foreground font-semibold">EX</Avatar.Fallback>
						</Avatar.Root>
					</div>
				</div>
			</div>
		</Card.Content>
	</Card.Root>
</div>
