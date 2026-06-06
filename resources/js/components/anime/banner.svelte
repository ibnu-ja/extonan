<script lang="ts">
    import ChevronDownIcon from '@lucide/svelte/icons/chevron-down';

    let {
        bannerImage,
        coverImage,
        title,
        summary,
        isDraft = false,
    }: {
        bannerImage?: string | null;
        coverImage?: string | null;
        title: string;
        summary?: string | null;
        isDraft?: boolean;
    } = $props();

    let expanded = $state(false);
</script>

<div class="relative">
    {#if bannerImage}
        <div class="relative h-50 md:h-80 overflow-hidden">
            <img src={bannerImage} alt="" class="h-full w-full object-cover" />
            <div
                class="absolute inset-0 bg-[linear-gradient(to_top,var(--background)_0%,color-mix(in_srgb,var(--background)_80%,transparent)_30%,transparent_80%)]"
            ></div>
        </div>
    {/if}

    <div
        class="relative p-4 mx-auto lg:max-w-7xl xl:max-w-screen-2xl px-4 pb-6 {bannerImage ? '-mt-16 md:-mt-24' : 'pt-6'}"
    >
        <div class="flex flex-col md:flex-row gap-6 items-start">
            {#if coverImage}
                <div
                    class="self-center md:self-auto shrink-0 w-40 md:w-55 {bannerImage ? '-mt-8 md:-mt-12' : ''}"
                >
                    <img
                        src={coverImage}
                        alt={title}
                        class="w-full rounded-lg shadow-2xl"
                    />
                </div>
            {/if}

            <div class="flex-1 min-w-0">
                <h1 class="text-2xl md:text-4xl font-bold font-heading">
                    {title}
                    {#if isDraft}
                        <span class="ml-2 inline-block align-middle text-sm font-medium text-destructive">Draft</span>
                    {/if}
                </h1>
                {#if summary}
                    <div class="mt-3 text-sm text-muted-foreground">
                        <div
                            class="relative overflow-hidden transition-[max-height] duration-300 ease-in-out"
                            style="max-height: {expanded ? '4000px' : '4.5rem'}"
                        >
                            <div class="text-sm text-muted-foreground">
                                {@html summary}
                            </div>
                            {#if !expanded}
                                <div
                                    class="absolute inset-x-0 bottom-0 h-8 bg-linear-to-t from-background to-transparent pointer-events-none"
                                ></div>
                            {/if}
                        </div>
                        <button
                            onclick={() => (expanded = !expanded)}
                            class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-muted-foreground/70 hover:text-muted-foreground transition-colors cursor-pointer"
                        >
                            {expanded ? 'Show less' : 'Show more'}
                            <span
                                class="inline-flex transition-transform duration-200"
                                class:rotate-180={expanded}
                            >
                                <ChevronDownIcon class="size-3" />
                            </span>
                        </button>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
