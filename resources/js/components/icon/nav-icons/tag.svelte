<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="TagIcon"
    tabler="IconTag"
    hugeicons="Tag01Icon"
    phosphor="TagIcon"
    remixicon="RiPriceTag3Line"
    class={className}
/>
