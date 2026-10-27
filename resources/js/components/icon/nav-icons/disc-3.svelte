<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="Disc3Icon"
    tabler="IconDisc"
    hugeicons="Disc02Icon"
    phosphor="DiscIcon"
    remixicon="RiDiscLine"
    class={className}
/>
