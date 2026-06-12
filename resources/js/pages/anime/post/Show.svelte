<script lang="ts">
    import { Link, setLayoutProps } from '@inertiajs/svelte';
    import ExternalLink from '@lucide/svelte/icons/external-link';
    import VideoPlayer from '@/components/anime/video-player.svelte';
    import * as Accordion from '@/components/ui/accordion';
    import { Badge } from '@/components/ui/badge';
    import * as Item from '@/components/ui/item';
    import { t, formatDate } from '@/lib/locale.svelte';
    import { index as animeIndex, show as animeShow } from '@/routes/anime';
    import { show as postShow } from '@/routes/post';

    let { anime, episodes, post }: App.Data.Anime.PostShowResponse = $props();

    // svelte-ignore state_referenced_locally
    let selectedSaluran = $state(
        post.saluran.length > 0
            ? (post.saluran[0].value[0]?.value ?? null)
            : null,
    );

    const displayTitle = $derived(
        post.postType === 'tv' && post.epNo
            ? `Episode ${post.epNo}: ${t(post.title)}`
            : t(post.title),
    );
    const animeTitle = $derived(t(anime.title));

    const formattedTitle = $derived(
        post.postType === 'tv' && post.epNo
            ? `${animeTitle} Episode ${post.epNo}`
            : displayTitle,
    );

    $effect(() => {
        setLayoutProps({
            showHeading: false,
            breadcrumbs: [
                { title: 'Anime', href: animeIndex() },
                { title: animeTitle, href: animeShow.url(anime.id) },
                { title: displayTitle, href: '' },
            ],
        });
    });

    $effect(() => {
        const slug = Object.values(post.slug).find(Boolean);

        if (slug) {
            const el = document.getElementById(slug);

            if (el) {
                el.scrollIntoView({ block: 'nearest' });
            }
        }
    });

    function getResolution(name: string): {
        label: string;
        variant: 'default' | 'secondary' | 'destructive' | 'outline';
    } | null {
        const pattern = /(\d{3,4}x\d{3,4}|\d{3,4}p)/;
        const match = name.match(pattern);

        if (!match) {
            return null;
        }

        const res = match[0];
        const map: Record<string, string> = {
            '3840x2160': '4K',
            '1920x1080': '1080p',
            '1440x1080': '1080p',
            '1280x720': '720p',
            '960x720': '720p',
            '848x480': '480p',
        };

        const label = res.includes('x') ? (map[res] ?? res) : res;
        const variantMap: Record<
            string,
            'default' | 'secondary' | 'destructive' | 'outline'
        > = {
            '4K': 'default',
            '1080p': 'secondary',
            '720p': 'outline',
            '480p': 'outline',
        };

        return { label, variant: variantMap[label] ?? 'outline' };
    }
</script>

<svelte:head>
    <title>{formattedTitle} - extonan</title>
</svelte:head>

<div class="w-full mx-auto lg:max-w-7xl xl:max-w-screen-2xl">
    <div class="px-4 py-6 pt-16 md:pt-6">
        <Link
            href={animeShow.url(anime.id)}
            class="hover:text-foreground transition-colors text-sm text-muted-foreground md:hidden"
        >
            {animeTitle}
        </Link>
        <h1 class="text-2xl md:text-4xl font-bold font-heading mt-1">
            {displayTitle}
            {#if !post.isPublished}
                <Badge variant="destructive" class="ml-2 align-middle text-sm"
                    >Draft</Badge
                >
            {/if}
        </h1>
        {#if post.publishedAt && post.author}
            <p class="text-muted-foreground mt-2 text-sm">
                Published {formatDate(post.publishedAt)} by {post.author.name}
            </p>
        {/if}
    </div>

    <hr />

    <div class="grid gap-6 px-4 py-6 md:grid-cols-12">
        <div class="md:col-span-8 lg:col-span-8 space-y-6">
            {#if post.embed}
                <!--eslint-disable-next-line svelte/no-at-html-tags-->
                {@html post.embed.value[0]?.value}
            {:else if post.saluran.length > 0 && selectedSaluran}
                <VideoPlayer
                    src={selectedSaluran}
                    poster={post.thumbnail?.extraLarge}
                />
                {#if post.saluran.length > 1}
                    <div class="flex flex-wrap gap-2">
                        {#each post.saluran as saluranItem (saluranItem.id)}
                            {#each saluranItem.value as stream (stream.value)}
                                <button
                                    class="rounded-md border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted"
                                    class:bg-primary={selectedSaluran ===
                                        stream.value}
                                    class:text-primary-foreground={selectedSaluran ===
                                        stream.value}
                                    onclick={() =>
                                        (selectedSaluran = stream.value)}
                                >
                                    {stream.name}
                                </button>
                            {/each}
                        {/each}
                    </div>
                {/if}
            {:else if post.thumbnail}
                <img
                    src={post.thumbnail.extraLarge}
                    alt={displayTitle}
                    class="max-h-80 mx-auto rounded-lg"
                />
            {/if}

            {#if post.description?.en}
                <p class="text-sm leading-relaxed">
                    {post.description.en}
                </p>
            {/if}

            <div>
                <h3 class="text-lg font-semibold font-heading mb-3">
                    Download Links
                </h3>
                {#if post.links.length > 0}
                    <Accordion.Root type="multiple" class="w-full">
                        {#each post.links as link (link.id)}
                            {@const resolution = getResolution(link.name)}
                            <Accordion.Item value={link.id.toString()}>
                                <Accordion.Trigger>
                                    <span class="flex items-center gap-2">
                                        {link.name}
                                        {#if resolution}
                                            <Badge
                                                variant={resolution.variant}
                                                class="text-xs"
                                            >
                                                {resolution.label}
                                            </Badge>
                                        {/if}
                                    </span>
                                </Accordion.Trigger>
                                <Accordion.Content>
                                    <div class="flex flex-wrap gap-2">
                                        {#each link.value as item (item.name)}
                                            <a
                                                href={item.value}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1.5 rounded-md border px-3 py-1.5 text-sm font-medium transition-colors hover:bg-muted"
                                            >
                                                {item.name}
                                                <ExternalLink class="size-3" />
                                            </a>
                                        {/each}
                                    </div>
                                </Accordion.Content>
                            </Accordion.Item>
                        {/each}
                    </Accordion.Root>
                {:else}
                    <p class="text-muted-foreground text-sm">
                        No download links available.
                    </p>
                {/if}
            </div>
        </div>

        <div class="md:col-span-4 lg:col-span-4">
            <div class="mb-3 px-0 md:px-4">
                <h4 class="text-lg font-semibold font-heading">
                    Other Episodes
                </h4>
            </div>

            <div class="max-h-128 overflow-y-auto px-0 md:px-4">
                <Item.Group class="px-0 md:px-4">
                    {#each episodes as episode (episode.id)}
                        {@const isActive = episode.id === post.id}
                        {@const slug = Object.values(episode.slug).find(
                            Boolean,
                        )}
                        <Item.Root class={isActive ? 'bg-muted' : ''} id={slug}>
                            {#snippet child({ props })}
                                {#if isActive}
                                    <div {...props}>
                                        <Item.Media variant="image">
                                            {#if episode.thumbnail}
                                                <img
                                                    src={episode.thumbnail
                                                        .medium}
                                                    alt=""
                                                    class="size-10 rounded object-cover"
                                                />
                                            {/if}
                                        </Item.Media>
                                        <Item.Content>
                                            <Item.Title>
                                                {#if episode.epNo}Episode {episode.epNo}:
                                                {/if}
                                                {t(episode.title)}
                                            </Item.Title>
                                            <Item.Description>
                                                {formatDate(
                                                    episode.publishedAt,
                                                )}
                                            </Item.Description>
                                        </Item.Content>
                                    </div>
                                {:else}
                                    <Link
                                        href={postShow.url({
                                            anime: anime.id,
                                            post: episode.id,
                                        })}
                                        {...props}
                                    >
                                        <Item.Media variant="image">
                                            {#if episode.thumbnail}
                                                <img
                                                    src={episode.thumbnail
                                                        .medium}
                                                    alt=""
                                                    class="size-10 rounded object-cover"
                                                />
                                            {/if}
                                        </Item.Media>
                                        <Item.Content>
                                            <Item.Title>
                                                {#if episode.epNo}Episode {episode.epNo}:
                                                {/if}
                                                {t(episode.title)}
                                            </Item.Title>
                                            <Item.Description>
                                                {formatDate(
                                                    episode.publishedAt,
                                                )}
                                            </Item.Description>
                                        </Item.Content>
                                    </Link>
                                {/if}
                            {/snippet}
                        </Item.Root>
                    {/each}
                </Item.Group>
            </div>
        </div>
    </div>
</div>
