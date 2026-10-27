<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="FilmIcon"
    tabler="IconFilm"
    hugeicons="Film01Icon"
    phosphor="FilmSlateIcon"
    remixicon="RiFilmLine"
    class={className}
/>
