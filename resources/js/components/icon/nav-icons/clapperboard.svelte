<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="ClapperboardIcon"
    tabler="IconClapperboard"
    hugeicons="ClapperboardIcon"
    phosphor="FilmReelIcon"
    remixicon="RiMovieLine"
    class={className}
/>
