<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="HouseIcon"
    tabler="IconHome"
    hugeicons="Home01Icon"
    phosphor="HouseIcon"
    remixicon="RiHomeLine"
    class={className}
/>
