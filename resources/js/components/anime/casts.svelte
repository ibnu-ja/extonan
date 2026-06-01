<script lang="ts">
    type CharacterEdge = App.Data.Anilist.CharacterEdgeData;

    type Props = {
        characters: CharacterEdge[];
    };

    let { characters }: Props = $props();
</script>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    {#each characters as character (character.node?.id)}
        <div class="grid grid-cols-2 overflow-hidden">
            <div class="grid grid-cols-4">
                <img
                    src={character.node?.image?.large ?? undefined}
                    alt={character.node?.name?.full ?? ''}
                    class="aspect-2/3 w-full object-cover"
                />
                <div class="col-span-3 flex flex-col justify-between px-3 py-2">
                    <div class="text-sm font-medium">
                        {character.node?.name?.full}
                    </div>
                    <div class="text-muted-foreground text-xs">
                        {character.role}
                    </div>
                </div>
            </div>
            {#if character.voiceActors?.[0]?.name}
                <div class="grid grid-cols-4">
                    <div
                        class="col-span-3 flex flex-col justify-between px-3 py-2 text-right"
                    >
                        <div class="text-sm font-medium">
                            {character.voiceActors[0].name.full}
                        </div>
                        <div class="text-muted-foreground text-xs">
                            Japanese
                        </div>
                    </div>
                    <img
                        src={character.voiceActors[0].image?.large ?? undefined}
                        alt={character.voiceActors[0].name.full}
                        class="aspect-2/3 w-full object-cover"
                    />
                </div>
            {/if}
        </div>
    {/each}
</div>
