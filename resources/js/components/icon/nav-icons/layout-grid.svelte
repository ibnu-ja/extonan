<script lang="ts">
    import { Icon } from '../index.js';

    let { class: className = 'size-4' }: { class?: string } = $props();
</script>

<Icon
    lucide="LayoutGridIcon"
    tabler="IconLayoutGrid"
    hugeicons="DashboardSquare01Icon"
    phosphor="SquaresFourIcon"
    remixicon="RiDashboardLine"
    class={className}
/>
